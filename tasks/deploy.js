var paths     = require("./_paths");
var del       = require("del");
var zip       = require("gulp-zip");
var zipFolder = require('zip-folder');
var helpers   = require("./_helpers");

module.exports = {
  dep: ["build:scripts", "build:styles", "build:react", "build:fonts", "composer", "clean:deploy"],
  fn: function (gulp, callback) {
    if (!helpers.isProd()) {
      console.log("!!! Deployment must be run with '--env prod' environment variable !!!");
      process.exit(1);
    }

    var stream = gulp.src(paths.deploy.src)
      .pipe(gulp.dest(paths.deploy.dist));

    stream.on('end', function () {
      del(paths.vendors.deleteList)
        .then(function () {
          zipFolder(paths.deploy.dist, "dist/freeform_next.zip", function (err) {
            if (err) {
              callback(err);
            } else {
              callback()
            }
          });
        });
    });

    stream.on('error', function (err) {
      callback(err);
    });
  }
};
