$(() => {
  let name   = $('input:text[name=name]');
  let handle = $('input:text[name=handle]');

  const camelize = (str) => {
    return str
      .replace(/(?:^\w|[A-Z]|\b\w)/g, (letter, index) => (
        index === 0 ? letter.toLowerCase() : letter.toUpperCase()
      ))
      .replace(/\s+/g, '')
      .replace(/[^a-zA-Z0-9_]/g, '')
  };

  name.on({
    keyup: () => {
      let val = name.val();

      val = camelize(val);
      handle.val(val);
    },
    change: (event) => $(event.target).trigger('keypress'),
  });
});
