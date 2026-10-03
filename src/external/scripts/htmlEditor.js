import { mountHtmlEditor } from "./shared/htmlEditor";

function initializeHtmlEditors() {
  document.querySelectorAll("[data-freeform-html-editor]").forEach((host) => {
    const textarea = document.getElementById(host.dataset.source);
    if (!textarea) return;

    const editor = mountHtmlEditor(host, {
      value: textarea.value,
      onChange: (value) => { textarea.value = value; },
      label: "Email body HTML",
    });
    textarea.form?.addEventListener("submit", () => {
      textarea.value = editor.getValue();
    });
  });
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initializeHtmlEditors);
} else {
  initializeHtmlEditors();
}
