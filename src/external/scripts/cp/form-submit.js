const form = document.getElementById('{{FORM_ANCHOR}}').parentElement;

form.addEventListener('submit', () => {
  const submitButtonList = form.querySelectorAll('[type=submit]');

  for (const submit of submitButtonList) {
    submit.disabled = true;

    setTimeout(function () {
      submit.disabled = false;
    }, 10000);
  }
});
