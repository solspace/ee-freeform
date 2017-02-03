var paths = require("../_paths");
var del   = require("del");

module.exports = {
  dep: ['clean:vendors', 'composer'],
  fn: function (gulp, callback) {
    var stream = gulp.src(paths.vendors.dist);

    stream.on('end', function () {
      del(paths.vendors.deleteList);
      callback();
    });

    stream.on('error', function (err) {
      callback(err);
    });
  }
};
