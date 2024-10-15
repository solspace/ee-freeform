document.addEventListener('DOMContentLoaded', () => {
  let anchor = document.getElementById('{{FORM_ANCHOR}}');
  if (anchor) {
    let form = anchor.parentElement;
    if (form) {
      form.addEventListener('submit', () => {
        const submitButtonList = form.querySelectorAll('[type=submit]:not([name={{PREV_BUTTON_NAME}}])');
        for (const submit of submitButtonList) {
          submit.disabled = true;

          setTimeout(function () {
            submit.disabled = false;
          }, 600000);
        }
      });
    }
  }
});
