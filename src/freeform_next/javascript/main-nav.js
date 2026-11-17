// Mark Freeform's native custom submenu using its destinations rather than its
// editable label. EE can move hover dropdowns into the document body.
$(() => {
  const isFreeformUrl = href => {
    const url = new URL(href, document.baseURI);
    return /(?:^|\/)addons\/settings\/freeform_next(?:\/|[?&]|$)/.test(url.pathname + url.search);
  };

  document.querySelectorAll('.nav-custom .ee-sidebar__item.js-dropdown-hover').forEach(item => {
    const dropdown = item._dropdown || item.nextElementSibling;
    if (!dropdown || !dropdown.classList.contains('dropdown')) {
      return;
    }

    const links = Array.from(dropdown.querySelectorAll('a.dropdown__link'));
    // A user-created dropdown may also contain unrelated links.
    if (!links.length || !links.every(link => isFreeformUrl(link.href))) {
      return;
    }
    item.classList.add('freeform-nav-item');

    // Keep EE's hover/touch dropdown, and use the permitted Forms destination
    // for a normal click when the member has access to Forms.
    const forms = links.find(link => {
      const url = new URL(link.href);
      return /(?:^|\/)addons\/settings\/freeform_next\/forms\/?(?:[?&]|$)/.test(url.pathname + url.search);
    });
    if (forms) {
      item.href = forms.href;
    }
  });
});
