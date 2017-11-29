var paths     = require("./_paths");
var del       = require("del");
var zip       = require("gulp-zip");
var zipFolder = require('zip-folder');
var helpers   = require("./_helpers");
var fs        = require("fs");
var replace   = require("gulp-replace");


function getVersionNumber() {
  var fileContent = fs.readFileSync("src/freeform_next/addon.setup.php", "utf8");

  const regex = /['"]version['"]\s*=>\s*['"]([0-9\.]+)['"]/g;

  return regex.exec(fileContent)[1];
}

module.exports = {
  dep: ["build:scripts", "build:styles", "build:react", "build:fonts", "build:themes", "composer", "clean:deploy"],
  fn: function (gulp, callback) {
    if (!helpers.isProd()) {
      console.log("!!! Deployment must be run with '--env prod' environment variable !!!");
      process.exit(1);
    }

    buildProVersion(gulp, callback);
  }
};

/**
 * Build and package the pro version
 */
function buildProVersion(gulp, callback) {
  var version = getVersionNumber();

  var addonStream = gulp
    .src(paths.deploy.addon.src)
    .pipe(replace(/('name'\s+=>\s+')Freeform Lite',/g, "$1Freeform Pro',"))
    .pipe(gulp.dest(paths.deploy.addon.dist));

  addonStream.on('end', function () {
    var themeStream = gulp
      .src(paths.deploy.themes.src)
      .pipe(gulp.dest(paths.deploy.themes.dist));

    themeStream.on('end', function () {
      del(paths.vendors.deleteList).then(function () {
        zipFolder(paths.deploy.buildPath, "dist/EE-Freeform-Pro_" + version + ".zip", function (err) {
          if (err) {
            callback(err);
          } else {
            del(paths.deploy.buildPath).then(function () {
              buildBasicVersion(gulp, callback);
            })
          }
        });
      });
    }).on('error', function (err) {
      callback(err);
    });

  }).on('error', function (err) {
    callback(err);
  });
}

/**
 * Build and package the basic version
 */
function buildBasicVersion(gulp, callback) {
  var version = getVersionNumber();

  var addonStream = gulp
    .src(paths.deploy.addon.src)
    .pipe(gulp.dest(paths.deploy.addon.dist));

  addonStream.on('end', function () {
    var themeStream = gulp
      .src(paths.deploy.themes.src)
      .pipe(gulp.dest(paths.deploy.themes.dist));

    themeStream.on('end', function () {
      del(paths.vendors.deleteList).then(function () {
        del(paths.deploy.addon.proFiles).then(function () {
          zipFolder(paths.deploy.buildPath, "dist/EE-Freeform-Lite_" + version + ".zip", function (err) {
            if (err) {
              callback(err);
            } else {
              del(paths.deploy.buildPath);
              callback()
            }
          });
        });
      });
    }).on('error', function (err) {
      callback(err);
    });

  }).on('error', function (err) {
    callback(err);
  });
}
