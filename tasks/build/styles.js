const paths        = require("../_paths"),
      sourcemaps   = require("gulp-sourcemaps"),
      autoprefixer = require("gulp-autoprefixer"),
      sass         = require("gulp-sass"),
      notify       = require('gulp-notify');

module.exports = {
  dep: ['clean:styles'],
  fn: function (gulp, callback) {
    return gulp
      .src(paths.styles.src)
      .pipe(sourcemaps.init())
      .pipe(
        sass({
          outputStyle: "compressed"
        })
          .on("error", notify.onError({
            message: 'Error: <%= error.message %>',
            sound: 'Sosumi'
          }))
      )
      .pipe(autoprefixer({
        remove: false,
        browsers: ["ie >= 11"],
      }))
      .pipe(gulp.dest(paths.styles.dest))
  }
};
