/*
 * Freeform for ExpressionEngine
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2026, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/freeform/v3/
 * @license       https://docs.solspace.com/license-agreement/
 */

$(function () {
  const viewButtons = document.querySelectorAll('[data-forms-view]');
  const viewPanels = document.querySelectorAll('[data-forms-panel]');
  const viewContainer = document.querySelector('.freeform-forms-cards');
  const viewKey = 'freeform-forms-view';

  if (viewButtons.length && viewContainer) {
    const setView = (view) => {
      viewButtons.forEach((button) => {
        const active = button.dataset.formsView === view;
        button.classList.toggle('is-active', active);
        button.setAttribute('aria-pressed', active ? 'true' : 'false');
      });
      viewPanels.forEach((panel) => {
        panel.hidden = panel.dataset.formsPanel !== view;
      });
      viewContainer.closest('.panel').classList.toggle('freeform-forms-card-mode', view === 'cards');
    };

    let savedView;
    try { savedView = window.localStorage.getItem(viewKey); } catch (error) { /* Storage can be disabled. */ }
    setView(savedView === 'list' ? 'list' : 'cards');

    viewButtons.forEach((button) => button.addEventListener('click', () => {
      const view = button.dataset.formsView;
      setView(view);
      try { window.localStorage.setItem(viewKey, view); } catch (error) { /* Keep the switch working. */ }
    }));
  }

  $(".reset-spam-count").on({
    click: (e) => {
      const self = $(e.target);
      const msg  = self.data("confirm-message");

      if (!confirm(msg)) {
        return false;
      }

      const formId = self.data("form-id");

      $.ajax({
        url: self.data('url'),
        type: "post",
        data: {
          formId: formId,
          csrf: self.data("csrf"),
        },
        dataType: "json",
        success: (response) => {
          if (response.error) {
            alert(response.error);
          } else if (response.success) {
            window.location.reload(false);
          }
        }
      })
    }
  });

  $(".duplicate").on({
    click: (e) => {
      const self = $(e.currentTarget);

      const formId = self.data("form-id");

      $.ajax({
        url: self.data('url'),
        type: "post",
        data: {
          formId: formId,
          csrf: self.data("csrf"),
        },
        dataType: "json",
        success: (response) => {
          if (response.error) {
            alert(response.error);
          } else if (response.success) {
            window.location.reload(false);
          }
        }
      })
    }
  });
});
