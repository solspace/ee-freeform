const paths        = require("../_paths"),
      sourcemaps   = require("gulp-sourcemaps"),
      autoprefixer = require("gulp-autoprefixer"),
      gulpif       = require("gulp-if"),
      helpers      = require("../_helpers"),
      sass         = require("gulp-sass"),
      notify       = require('gulp-notify');

module.exports = {
  deps: ['clean:styles'],
  fn: function (gulp, callback) {
    return gulp
      .src(paths.styles.src)
      .pipe(gulpif(!helpers.isProd(), sourcemaps.init()))
      .pipe(
        sass({
          outputStyle: "compressed",
        })
          .on("error", notify.onError({
            message: 'Error: <%= error.message %>',
            sound: 'Sosumi',
          })),
      )
      .pipe(autoprefixer({
        remove: false,
        browsers: ["ie >= 11"],
      }))
      .pipe(gulpif(!helpers.isProd(), sourcemaps.write()))
      .pipe(gulp.dest(paths.styles.dest))
  },
};
