let stateCheck = setInterval(() => {
  if (document.readyState === 'complete') {
    clearInterval(stateCheck);

    const top = document.getElementById('{{FORM_ANCHOR}}').offsetTop;
    window.scrollTo(0, top);
  }
}, 100);
