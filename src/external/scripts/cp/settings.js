$(() => {
  $('.btn.add-template').on({
    click: (e) => {
      const self = $(e.target);

      $.ajax({
        url: self.attr('href'),
        type: 'post',
        dataType: 'json',
        data: {
          force_file: true,
          name: 'sample_template',
          templateName: 'sample_template',
        },
        success: (response) => {
          if (response.errors && response.errors.length) {
            for (let i = 0; i < response.errors.length; i++) {
              alert(response.errors[i]);
            }
          } else {
            window.location.reload(false);
          }
        }
      });

      e.preventDefault();
      e.stopPropagation();
      return false;
    }
  })
});
