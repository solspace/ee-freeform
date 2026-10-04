import { EditorView, basicSetup } from "codemirror";
import { Compartment } from "@codemirror/state";
import { html } from "@codemirror/lang-html";
import { indentWithTab } from "@codemirror/commands";
import { keymap } from "@codemirror/view";

export function mountHtmlEditor(host, { value = "", onChange, compact = false, label = "HTML source" } = {}) {
  const shell = document.createElement("div");
  shell.className = `freeform-html-editor${compact ? " freeform-html-editor--compact" : ""}`;

  const toolbar = document.createElement("div");
  toolbar.className = "freeform-html-editor__toolbar";
  const language = document.createElement("span");
  language.className = "freeform-html-editor__language";
  language.textContent = "HTML";
  const wrapButton = document.createElement("button");
  wrapButton.type = "button";
  wrapButton.className = "freeform-html-editor__wrap is-active";
  const wrapCheck = document.createElement("span");
  wrapCheck.className = "freeform-html-editor__wrap-check";
  wrapCheck.textContent = "✓";
  wrapCheck.setAttribute("aria-hidden", "true");
  wrapButton.append(wrapCheck, document.createTextNode("Wrap lines"));
  wrapButton.setAttribute("aria-pressed", "true");
  toolbar.append(language, wrapButton);

  const body = document.createElement("div");
  body.className = "freeform-html-editor__body";
  const footer = document.createElement("div");
  footer.className = "freeform-html-editor__footer";
  const position = document.createElement("span");
  footer.append(position);
  shell.append(toolbar, body, footer);
  host.replaceChildren(shell);

  const wrapping = new Compartment();
  const updatePosition = (state) => {
    const cursor = state.selection.main.head;
    const line = state.doc.lineAt(cursor);
    position.textContent = `Ln ${line.number}, Col ${cursor - line.from + 1}`;
  };

  const view = new EditorView({
    doc: value,
    parent: body,
    extensions: [
      basicSetup,
      keymap.of([indentWithTab]),
      html(),
      EditorView.contentAttributes.of({ "aria-label": label }),
      wrapping.of(EditorView.lineWrapping),
      EditorView.updateListener.of((update) => {
        if (update.docChanged && onChange) onChange(update.state.doc.toString());
        if (update.docChanged || update.selectionSet) updatePosition(update.state);
      }),
    ],
  });
  updatePosition(view.state);

  let isWrapped = true;
  wrapButton.addEventListener("click", () => {
    isWrapped = !isWrapped;
    wrapButton.classList.toggle("is-active", isWrapped);
    wrapButton.setAttribute("aria-pressed", String(isWrapped));
    view.dispatch({ effects: wrapping.reconfigure(isWrapped ? EditorView.lineWrapping : []) });
    view.focus();
  });

  return {
    getValue() {
      return view.state.doc.toString();
    },
    setValue(nextValue) {
      if (view.state.doc.toString() !== nextValue) {
        view.dispatch({ changes: { from: 0, to: view.state.doc.length, insert: nextValue || "" } });
      }
    },
    destroy() {
      view.destroy();
      host.replaceChildren();
    },
  };
}
