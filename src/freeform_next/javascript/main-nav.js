// EE's custom submenu parents use href="#". Reuse the permitted Forms link
// as Freeform's destination, retaining EE's native hover/touch dropdown.
$(() => {
  document.querySelectorAll('.nav-custom .ee-sidebar__item.js-dropdown-hover[href="#"]').forEach(item => {
    if (!/^Freeform(?: Lite| Express)?$/.test(item.title)) {
      return;
    }

    // EE moves hover dropdowns into the document body during initialization.
    const dropdown = item._dropdown || item.nextElementSibling;
    if (!dropdown || !dropdown.classList.contains('dropdown')) {
      return;
    }

    const forms = Array.from(dropdown.querySelectorAll('a.dropdown__link')).find(link => {
      const url = new URL(link.href);
      // EE can put the CP route in the query string (admin.php?/cp/...).
      return /(?:^|\/)addons\/settings\/freeform_next\/forms\/?(?:[?&]|$)/.test(url.pathname + url.search);
    });

    if (forms) {
      item.href = forms.href;
    }
  });
});
