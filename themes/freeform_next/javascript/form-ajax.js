/* Front-end form submission for forms with Use AJAX enabled. */
(() => {
  if (window.__freeformAjaxBound) return;
  window.__freeformAjaxBound = true;

  let errorId = 0;

  function clearErrors(form) {
    form.querySelectorAll('[data-freeform-ajax-error]').forEach((node) => node.remove());
    form.querySelectorAll('[data-freeform-ajax-error-id]').forEach((control) => {
      const id = control.dataset.freeformAjaxErrorId;
      const describedBy = (control.getAttribute('aria-describedby') || '').split(/\s+/).filter((value) => value && value !== id);
      if (describedBy.length) control.setAttribute('aria-describedby', describedBy.join(' '));
      else control.removeAttribute('aria-describedby');
      control.removeAttribute('aria-invalid');
      delete control.dataset.freeformAjaxErrorId;
    });
  }

  function showSummary(form, messages, heading) {
    const container = form.querySelector('.ff-tailwind, .ff-bootstrap, .ff-basic-light, .ff-basic-dark, .ff-basic-floating-labels, .ff-flexbox, .ff-grid') || form;
    const summary = document.createElement('div');
    summary.className = 'ff-summary freeform-ajax-summary';
    summary.dataset.freeformAjaxError = '';
    summary.setAttribute('role', 'alert');
    summary.setAttribute('tabindex', '-1');

    const title = document.createElement('strong');
    title.textContent = heading || form.dataset.freeformErrorMessage || 'Please correct the errors below.';
    summary.appendChild(title);
    if (messages.length) {
      const list = document.createElement('ul');
      messages.forEach((message) => {
        const item = document.createElement('li');
        item.textContent = String(message);
        list.appendChild(item);
      });
      summary.appendChild(list);
    }
    container.insertBefore(summary, container.firstChild);
    return summary;
  }

  function showErrors(form, response) {
    const formErrors = Array.isArray(response.formErrors) ? response.formErrors : [];
    const fieldErrors = response.errors && typeof response.errors === 'object' ? response.errors : {};
    const unmatchedErrors = [];
    let firstControl = null;

    Object.entries(fieldErrors).forEach(([handle, messages]) => {
      if (!Array.isArray(messages) || !messages.length) return;
      const controls = Array.from(form.elements).filter((element) => element.name === handle || element.name === `${handle}[]`);
      if (!controls.length) {
        unmatchedErrors.push(...messages);
        return;
      }

      const list = document.createElement('ul');
      list.className = 'ff-errors';
      list.dataset.freeformAjaxError = '';
      list.id = `freeform-ajax-error-${++errorId}`;
      messages.forEach((message) => {
        const item = document.createElement('li');
        item.textContent = String(message);
        list.appendChild(item);
      });

      const control = controls[0];
      const group = control.closest('fieldset, .form-floating, .ff-floating, label.ff-option');
      (group || control).insertAdjacentElement('afterend', list);
      controls.forEach((element) => {
        element.setAttribute('aria-invalid', 'true');
        element.setAttribute('aria-describedby', [element.getAttribute('aria-describedby'), list.id].filter(Boolean).join(' '));
        element.dataset.freeformAjaxErrorId = list.id;
      });
      if (!firstControl && control.type !== 'hidden') firstControl = control;
    });

    const summary = showSummary(form, [...formErrors, ...unmatchedErrors], response.errorMessage);
    const focusTarget = firstControl || summary;
    focusTarget.focus({ preventScroll: true });
    focusTarget.scrollIntoView({ block: 'center', behavior: 'smooth' });
  }

  function updateTokens(form, response) {
    if (response.csrfToken) {
      const csrf = form.querySelector('input[name="csrf_token"]');
      if (csrf) csrf.value = response.csrfToken;
    }

    if (response.honeypot && response.honeypot.name) {
      const input = Array.from(form.elements).find((element) => element.name && element.name.startsWith('freeform_form_handle'));
      if (input) {
        const label = input.id && form.querySelector(`label[for="${input.id}"]`);
        input.name = response.honeypot.name;
        input.id = response.honeypot.name;
        input.value = response.honeypot.hash || '';
        if (label) label.htmlFor = input.id;
      }
    }
  }

  function resetCaptcha(form) {
    document.dispatchEvent(new CustomEvent('freeform:captcha-reset', { detail: { form } }));
    if (form.querySelector('.g-recaptcha') && window.grecaptcha && window.grecaptcha.reset) {
      window.grecaptcha.reset();
    }
  }

  function finish(form, response) {
    form.dispatchEvent(new CustomEvent('freeform:success', { bubbles: true, detail: response }));
    if (response.successBehavior === 'message') {
      const pageUrl = new URL(window.location.href);
      pageUrl.searchParams.delete('freeform_ajax_page');
      if (pageUrl.href === window.location.href) window.location.reload();
      else window.location.replace(pageUrl.href);
      return;
    }

    if (response.successBehavior !== 'message' && response.returnUrl) {
      const destination = new URL(response.returnUrl, window.location.href);
      if (destination.protocol === 'http:' || destination.protocol === 'https:') {
        window.location.assign(destination.href);
        return;
      }
    }

    const status = document.createElement('div');
    status.className = 'freeform-success-banner';
    if (form.dataset.freeformFeedbackTheme === 'dark' || form.querySelector('.ff-dark, .ff-basic-dark, [data-bs-theme="dark"]')) {
      status.classList.add('freeform-success-banner--dark');
    }
    status.setAttribute('role', 'status');
    status.setAttribute('tabindex', '-1');
    status.textContent = response.successMessage || 'Thank you! Your submission has been received.';
    form.insertAdjacentElement('afterend', status);
    form.hidden = true;
    status.focus();
  }

  document.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.dataset.freeformAjaxAction || event.defaultPrevented) return;
    event.preventDefault();
    if (form.dataset.freeformAjaxSubmitting) return;

    clearErrors(form);
    const submitter = event.submitter || (document.activeElement?.form === form ? document.activeElement : null);
    const payload = new FormData(form);
    if (submitter && submitter.name) payload.append(submitter.name, submitter.value || '');

    form.dataset.freeformAjaxSubmitting = 'true';
    form.setAttribute('aria-busy', 'true');
    const buttons = Array.from(form.querySelectorAll('button[type="submit"], input[type="submit"]'));
    const disabled = buttons.map((button) => button.disabled);
    buttons.forEach((button) => { button.disabled = true; });
    form.dispatchEvent(new CustomEvent('freeform:submit', { bubbles: true }));

    try {
      const result = await fetch(form.dataset.freeformAjaxAction, {
        method: 'POST',
        body: payload,
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
      });
      if (!result.ok) throw new Error(`HTTP ${result.status}`);
      const response = await result.json();
      if (!response || typeof response.success !== 'boolean') throw new Error('Invalid Freeform response');

      updateTokens(form, response);
      if (response.success) {
        if (response.finished) finish(form, response);
        else {
          form.dispatchEvent(new CustomEvent('freeform:page', { bubbles: true, detail: response }));
          const pageUrl = new URL(window.location.href);
          if (response.formHash) pageUrl.searchParams.set('freeform_ajax_page', response.formHash);
          window.location.assign(pageUrl.href);
        }
      } else {
        showErrors(form, response);
        resetCaptcha(form);
        form.dispatchEvent(new CustomEvent('freeform:error', { bubbles: true, detail: response }));
      }
    } catch (error) {
      const summary = showSummary(form, ['The form could not be submitted. Please try again.']);
      summary.focus();
      resetCaptcha(form);
      form.dispatchEvent(new CustomEvent('freeform:error', { bubbles: true, detail: { error } }));
    } finally {
      buttons.forEach((button, index) => { button.disabled = disabled[index]; });
      form.removeAttribute('aria-busy');
      delete form.dataset.freeformAjaxSubmitting;
    }
  });
})();
