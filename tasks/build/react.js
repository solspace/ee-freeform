var paths      = require("../_paths");
var browserify = require("browserify");
var babelify   = require('babelify');
var streamify  = require('streamify');
var buffer     = require('vinyl-buffer');
var source     = require('vinyl-source-stream');
var del        = require('del');
var notify     = require('gulp-notify');
var gutil      = require('gulp-util');
var uglify     = require('gulp-uglify');
var gulpif     = require("gulp-if");
var helpers    = require("../_helpers");

var dependencies = [
  'react',
  'react-dom'
];
var scriptsCount = 0;

module.exports = {
  fn: function (gulp, callback) {
    scriptsCount++;

    var isDeploy = global.deployment;

    var bundler = browserify({
      entries: ['./src/external/scripts/composer/app.js'],
      extensions: ['.js'],
      debug: !isDeploy // Add sourcemaps
    });

    if (scriptsCount === 1) {
      browserify({
        require: dependencies,
        debug: !isDeploy
      })
        .bundle()
        .on('error', function (err) {
          gutil.log(err.message);
          this.emit('end');
        })
        .pipe(source('vendors.js'))
        .pipe(gulpif(helpers.isProd(), buffer()))
        .pipe(gulpif(helpers.isProd(), uglify()))
        .pipe(gulp.dest(paths.react.dest));
    }

    dependencies.forEach(function (dep) {
      bundler.external(dep);
    });

    return bundler
      .transform('babelify', {
        presets: ["es2015", "react", "stage-0"],
        plugins: ["transform-object-rest-spread", "transform-decorators-legacy"]
      }) // JSX and ES6 => JS
      .bundle() // Browserify bundles required files
      .on("error", notify.onError({
        message: 'Error: <%= error.message %>',
        sound: 'Sosumi'
      }))
      .pipe(source('app.js')) // Desired filename of bundled files
      .pipe(gulpif(helpers.isProd(), buffer()))
      .pipe(gulpif(helpers.isProd(), uglify()))
      .pipe(notify("React compiled"))
      .pipe(gulp.dest(paths.react.dest));
  }
};
