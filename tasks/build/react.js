const paths      = require("../_paths"),
      browserify = require("browserify"),
      babelify   = require('babelify'),
      streamify  = require('streamify'),
      buffer     = require('vinyl-buffer'),
      source     = require('vinyl-source-stream'),
      del        = require('del'),
      notify     = require('gulp-notify'),
      gutil      = require('gulp-util'),
      uglify     = require('gulp-uglify'),
      gulpif     = require("gulp-if"),
      helpers    = require("../_helpers");

const dependencies = [
  'react',
  'react-dom',
];

let scriptsCount = 0;

module.exports = {
  fn: function (gulp, callback) {
    scriptsCount++;

    const isDeploy = global.deployment;

    const bundler = browserify({
      entries: ['./src/external/scripts/composer/app.js'],
      extensions: ['.js'],
      debug: !isDeploy, // Add sourcemaps
    });

    if (scriptsCount === 1) {
      browserify({
        require: dependencies,
        debug: !isDeploy,
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
        plugins: ["transform-object-rest-spread", "transform-decorators-legacy"],
      }) // JSX and ES6 => JS
      .bundle() // Browserify bundles required files
      .on("error", notify.onError({
        message: 'Error: <%= error.message %>',
        sound: 'Sosumi',
      }))
      .pipe(source('app.js')) // Desired filename of bundled files
      .pipe(gulpif(helpers.isProd(), buffer()))
      .pipe(gulpif(helpers.isProd(), uglify()))
      .pipe(notify("React compiled"))
      .pipe(gulp.dest(paths.react.dest));
  },
};
