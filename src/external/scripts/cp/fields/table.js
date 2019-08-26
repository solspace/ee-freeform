(() => {
  const pattern = /([^\[]+)\[(\d+)\](.*)/g;
  const tables = document.querySelectorAll(".form-table");

  const removeRow = (event) => {
    if (event.target.closest('tbody').querySelectorAll('tr').length === 1) {
      return;
    }

    event.target.closest('tr').remove()
  }

  for (let i = 0; i < tables.length; i++) {
    const table = tables[i];
    const button = table.parentNode.querySelector(".form-table-add-row");
    const maxRows = table.getAttribute("data-max-rows");

    const removeRowButtons = table.querySelectorAll(".form-table-remove-row");
    removeRowButtons.forEach(removeButton => {
      removeButton.addEventListener('click', removeRow);
    })

    if (button) {
      const getNextMaxIndex = () => {
        const inputs = table.querySelectorAll("input, select");

        let maxIndex = 0;
        inputs.forEach(input => {
          const matches = pattern.exec(input.name);
          if (!matches) return;

          const index = parseInt(matches[2]);
          maxIndex = Math.max(maxIndex, index);
        })

        return ++maxIndex;
      }

      button.addEventListener("click", () => {
        const referenceRow = table.querySelector("tbody > tr:first-child");

        if (referenceRow) {
          const cloneRow = referenceRow.cloneNode(true);
          const inputs = cloneRow.querySelectorAll("input, select");
          const maxIndex = getNextMaxIndex();
          inputs.forEach((item) => {
            item.name = item.name.replace(pattern, `$1[${maxIndex}]$3`)
            if (item.tagName !== "SELECT") {
              item.checked = false;
              item.value = item.dataset.defaultValue;
            }
          });

          const removeRowButton = cloneRow.querySelector('.form-table-remove-row');
          if (removeRowButton) {
            removeRowButton.addEventListener('click', removeRow);
          }

          table.querySelector("tbody").appendChild(cloneRow);
        }
      });
    }
  }
})();
