$(() => {
  const baseField   = $('input[data-generator-base]');
  const targetField = $('input[data-generator-target]');

  const underscored = (str) => {
    return str
      .replace(/(?:^\w|[A-Z]|\b\w)/g, (letter, index) => (letter.toLowerCase()))
      .replace(/\s+/g, '_')
      .replace(/[^a-zA-Z0-9_]/g, '')
  };

  baseField.on({
    keyup: (e) => {
      const val = underscored($(e.target).val());

      targetField.val(val);
    },
    change: () => {
      $(this).trigger('keyup');
    }
  });

  targetField.on({
    keyup: (e) => {
      const target   = e.target,
            $target  = $(e.target),
            valLength = $target.val().length,
            caretPos = target.selectionStart;

      const val = underscored($target.val());

      $target.val(val);

      if (target.createTextRange) {
        const range = target.createTextRange();
        range.move('character', caretPos);
        range.select();

        console.log('range');
      } else {
        const diff = valLength - val.length;

        target.setSelectionRange(caretPos - diff, caretPos - diff);
      }
    }
  });
});
