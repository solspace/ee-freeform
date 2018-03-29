const paths = require("../_paths"),
      del   = require("del");

module.exports = {
  deps: ['clean:vendors', 'composer'],
  fn: function (gulp, callback) {
    const stream = gulp.src(paths.vendors.dist);

    stream.on('end', function () {
      del(paths.vendors.deleteList);
      callback();
    });

    stream.on('error', function (err) {
      callback(err);
    });
  },
};
