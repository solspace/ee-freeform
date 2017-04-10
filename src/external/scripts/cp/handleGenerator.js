$(() => {
  const baseField   = $('input[data-generator-base]');
  const targetField = $('input[data-generator-target]');

  const underscored = (str) => {
    return str
      .replace(/(?:^\w|[A-Z]|\b\w)/g, (letter, index) => (letter.toLowerCase()))
      .replace(/\s+/g, '_')
      .replace(/[^a-zA-Z0-9_]/g, '')
      .replace(/^_|_$/g, '')
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
      const val = underscored($(e.target).val());

      $(e.target).val(val);
    }
  });
});
