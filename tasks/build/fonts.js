const paths = require("../_paths");

module.exports = {
  deps: ['clean:fonts'],
  fn: function (gulp, callback) {
    return gulp.src(paths.fonts.src)
      .pipe(gulp.dest(paths.fonts.dest))
  },
};
