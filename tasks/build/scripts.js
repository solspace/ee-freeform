var paths      = require("../_paths");
var uglify     = require("gulp-uglify");
var sourcemaps = require("gulp-sourcemaps");
var gulpif     = require("gulp-if");
var helpers    = require("../_helpers");

module.exports = {
  dep: ['clean:scripts'],
  fn: function (gulp, callback) {
    return gulp.src(paths.scripts.src)
      .pipe(gulpif(helpers.isProd(), sourcemaps.init()))
      .pipe(uglify({
        mangle: true,
      }))
      .pipe(gulpif(helpers.isProd(), sourcemaps.write()))
      .pipe(gulp.dest(paths.scripts.dest));
  }
};
