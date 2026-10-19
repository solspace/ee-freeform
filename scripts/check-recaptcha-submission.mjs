// Run with node --test scripts/check-recaptcha-submission.mjs; no EE or Google connection required.
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { test } from 'node:test';
import vm from 'node:vm';

const root = new URL('../', import.meta.url);
const service = readFileSync(new URL('src/freeform_next/Services/RecaptchaService.php', root), 'utf8');
const captcha = service.split("$template = <<<'JS'\n<script>\n")[1].split('\n</script>\nJS;')[0]
  .replace('const SITE_KEY = %s;', 'const SITE_KEY = "test-site-key";');
const ajax = readFileSync(new URL('src/external/scripts/form-ajax.js', root), 'utf8');

class TestEvent {
  constructor(type, options = {}) { Object.assign(this, { type, defaultPrevented: false }, options); }
  preventDefault() { this.defaultPrevented = true; }
  stopImmediatePropagation() { this.stopped = true; }
}

class Target {
  listeners = [];
  addEventListener(type, callback, options = {}) {
    this.listeners.push({ type, callback, capture: options === true || options.capture === true });
  }
  emit(event, capture = false) {
    for (const listener of this.listeners) {
      if (event.stopped) break;
      if (listener.type === event.type && listener.capture === capture) listener.callback(event);
    }
  }
  dispatchEvent(event) { this.emit(event); return !event.defaultPrevented; }
}

function fixture({ ready = 'loading', builtin = false, custom = false } = {}) {
  const document = new Target();
  const forms = [], scripts = [], requests = [], tokens = [];
  let observe;
  class Form extends Target {
    dataset = {};
    attributes = new Map();
    token = { name: 'g-recaptcha-response', value: '' };
    elements = [this.token];
    constructor({ freeform = true, ajaxEnabled = builtin } = {}) {
      super();
      this.freeform = freeform;
      if (ajaxEnabled) this.dataset.freeformAjaxAction = '/submit';
      this.submitter = { form: this, name: 'send', value: 'Send', disabled: false };
      forms.push(this);
    }
    querySelector(selector) {
      if (selector === 'input[name="formHash"]') return this.freeform ? {} : null;
      if (selector === '#g-recaptcha-response') return this.token;
      return null;
    }
    querySelectorAll() { return []; }
    hasAttribute(name) { return name === 'data-freeform-ajax-action' && !!this.dataset.freeformAjaxAction; }
    setAttribute(name, value) { this.attributes.set(name, value); }
    removeAttribute(name) { this.attributes.delete(name); }
    requestSubmit(submitter) {
      const event = new TestEvent('submit', { target: this, submitter });
      document.emit(event, true);
      this.emit(event, true);
      this.emit(event);
      document.emit(event);
      if (!event.defaultPrevented) this.submit();
    }
    submit() { requests.push({ kind: 'native', token: this.token.value }); }
  }
  Object.assign(document, {
    readyState: ready,
    documentElement: {},
    head: { appendChild: script => scripts.push(script) },
    getElementById: id => scripts.find(script => script.id === id),
    createElement: () => ({}),
    querySelectorAll: () => forms,
  });
  class Payload {
    constructor(form) { this.values = new Map(form.elements.map(({name, value}) => [name, value])); }
    append(name, value) { this.values.set(name, value); }
    get(name) { return this.values.get(name); }
  }
  const grecaptcha = {
    ready: callback => callback(),
    execute: () => new Promise((resolve, reject) => tokens.push({ resolve, reject })),
  };
  const context = vm.createContext({
    document, HTMLFormElement: Form, FormData: Payload, CustomEvent: TestEvent,
    window: { grecaptcha, location: { href: 'https://example.test/form', assign() {} } },
    grecaptcha, URL, setTimeout,
    MutationObserver: class { constructor(callback) { observe = callback; } observe() {} },
    fetch: (url, options) => {
      requests.push({ kind: 'ajax', token: options.body.get('g-recaptcha-response'), submitter: options.body.get('send') });
      return new Promise(() => {}); // Keep AJAX in flight to exercise duplicate-click protection.
    },
  });
  const form = new Form();
  if (custom) form.addEventListener('submit', event => {
    event.preventDefault();
    requests.push({ kind: 'custom', token: form.token.value, submitter: event.submitter });
  });
  if (builtin) vm.runInContext(ajax, context);
  vm.runInContext(captcha, context);
  function loaded() {
    document.readyState = 'complete';
    document.dispatchEvent(new TestEvent('DOMContentLoaded'));
  }
  return { form, Form, loaded, requests, tokens, scripts, mutate: () => observe(), rerun: () => vm.runInContext(captcha, context) };
}

const settled = () => new Promise(resolve => setImmediate(resolve));

test('built-in AJAX sends exactly one request after a fresh token and keeps the submitter', async () => {
  const f = fixture({ builtin: true });
  f.loaded();
  f.form.requestSubmit(f.form.submitter);
  assert.equal(f.requests.length, 0);
  f.tokens[0].resolve('fresh-token');
  await settled();
  assert.deepEqual(f.requests, [{kind: 'ajax', token: 'fresh-token', submitter: 'Send'}]);
});

test('custom submit listeners cannot send an empty token or cause an extra native post', async () => {
  const f = fixture({ custom: true });
  f.loaded();
  f.form.requestSubmit(f.form.submitter);
  assert.equal(f.requests.length, 0);
  f.tokens[0].resolve('custom-token');
  await settled();
  assert.equal(f.requests.length, 1);
  assert.equal(f.requests[0].kind, 'custom');
  assert.equal(f.requests[0].token, 'custom-token');
  assert.equal(f.requests[0].submitter, f.form.submitter);
});

test('repeated clicks request only one token and do not submit again during AJAX', async () => {
  const f = fixture({ builtin: true });
  f.loaded();
  f.form.requestSubmit(f.form.submitter);
  f.form.requestSubmit(f.form.submitter);
  assert.equal(f.tokens.length, 1);
  f.tokens[0].resolve('once');
  await settled();
  f.form.requestSubmit(f.form.submitter);
  assert.equal(f.tokens.length, 1);
  assert.equal(f.requests.length, 1);
});

test('normal submissions still wait for a token and submit once', async () => {
  const f = fixture();
  f.loaded();
  f.form.requestSubmit(f.form.submitter);
  assert.equal(f.requests.length, 0);
  f.tokens[0].resolve('normal-token');
  await settled();
  assert.deepEqual(f.requests, [{kind: 'native', token: 'normal-token'}]);
});

test('token generation failure clears an earlier token for server-side rejection, and permits retry', async () => {
  const f = fixture({ custom: true });
  f.loaded();
  f.form.token.value = 'old-token';
  f.form.requestSubmit(f.form.submitter);
  f.tokens[0].reject(new Error('provider unavailable'));
  await settled();
  assert.equal(f.requests.length, 1);
  assert.equal(f.requests[0].token, '');
  f.form.requestSubmit(f.form.submitter);
  f.tokens[1].resolve('retry-token');
  await settled();
  assert.equal(f.requests.length, 2);
  assert.equal(f.requests[1].token, 'retry-token');
});

test('scripts loaded after DOMContentLoaded initialize immediately and dynamic forms bind once', async () => {
  const f = fixture({ ready: 'complete' });
  assert.equal(f.scripts.length, 1);
  const dynamic = new f.Form();
  f.mutate();
  f.mutate();
  f.rerun();
  dynamic.requestSubmit(dynamic.submitter);
  assert.equal(f.tokens.length, 1);
  f.tokens[0].resolve('dynamic-token');
  await settled();
  assert.deepEqual(f.requests, [{kind: 'native', token: 'dynamic-token'}]);
  assert.equal(f.scripts.length, 1);
});

test('forms have independent token requests and unrelated forms are left alone', async () => {
  const f = fixture();
  f.loaded();
  const second = new f.Form(), unrelated = new f.Form({freeform: false});
  f.mutate();
  unrelated.requestSubmit(unrelated.submitter);
  f.form.requestSubmit(f.form.submitter);
  second.requestSubmit(second.submitter);
  assert.equal(f.tokens.length, 2);
  f.tokens[1].resolve('second-token');
  f.tokens[0].resolve('first-token');
  await settled();
  assert.deepEqual(f.requests, [
    {kind: 'native', token: ''}, {kind: 'native', token: 'second-token'}, {kind: 'native', token: 'first-token'},
  ]);
});
