const paths = require("../_paths");

module.exports = {
  dep: ['clean:themes'],
  fn: function (gulp, callback) {
    return gulp
      .src(paths.themes.src)
      .pipe(gulp.dest(paths.themes.dest))
  }
};
