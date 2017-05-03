const paths      = require("../_paths"),
      uglify     = require("gulp-uglify"),
      sourcemaps = require("gulp-sourcemaps"),
      gulpif     = require("gulp-if"),
      helpers    = require("../_helpers"),
      babel      = require('gulp-babel'),
      notify     = require('gulp-notify');

module.exports = {
  dep: ['clean:scripts'],
  fn: function (gulp, callback) {
    return gulp
      .src(paths.scripts.src)
      .pipe(gulpif(helpers.isProd(), sourcemaps.init()))
      .pipe(
        babel({
          presets: ['es2015']
        })
          .on("error", notify.onError({
            message: 'Error: <%= error.message %>',
            sound: 'Sosumi'
          }))
      )
      .pipe(uglify({
        mangle: true,
      }))
      .pipe(gulpif(helpers.isProd(), sourcemaps.write()))
      .pipe(gulp.dest(paths.scripts.dest));
  }
};
