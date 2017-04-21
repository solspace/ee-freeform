var paths     = require("./_paths");
var del       = require("del");
var zip       = require("gulp-zip");
var zipFolder = require('zip-folder');
var helpers   = require("./_helpers");
var fs        = require("fs");


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

    var addonStream = gulp
      .src(paths.deploy.addon.src)
      .pipe(gulp.dest(paths.deploy.addon.dist));

    addonStream.on('end', function () {

      var themeStream = gulp
        .src(paths.deploy.themes.src)
        .pipe(gulp.dest(paths.deploy.themes.dist));

      themeStream.on('end', function () {
        del(paths.vendors.deleteList)
          .then(function () {
            var version = getVersionNumber();

            zipFolder(paths.deploy.buildPath, "dist/freeform_next_pro_v" + version + ".zip", function (err) {
              if (err) {
                callback(err);
              } else {
                del(paths.deploy.addon.proFiles)
                  .then(function () {
                    zipFolder(paths.deploy.buildPath, "dist/freeform_next_basic_v" + version + ".zip", function (err) {
                      if (err) {
                        callback(err);
                      } else {
                        del(paths.deploy.buildPath);
                        callback()
                      }
                    });
                  });
              }
            });
          });
      });

      themeStream.on('error', function (err) {
        callback(err);
      });

    });

    addonStream.on('error', function (err) {
      callback(err);
    });
  }
};
