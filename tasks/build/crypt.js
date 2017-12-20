const paths  = require("../_paths"),
      base64_encode = require('gulp-base64-encode');

module.exports = {
  dep: ['clean:crypt'],
  fn: function (gulp, callback) {
    return gulp
      .src(paths.crypt.src)
      .pipe(base64_encode())
      .pipe(gulp.dest(paths.crypt.dest))
  }
};
