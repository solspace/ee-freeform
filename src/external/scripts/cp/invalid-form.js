let stateCheck = setInterval(() => {
  if (document.readyState === 'complete') {
    clearInterval(stateCheck);

    document.getElementById('{{FORM_ANCHOR}}').scrollIntoView(); ;
  }
}, 300);
