const paths = require("../_paths");
const del = require("del");
const zipFolder = require("zip-folder");
const helpers = require("../_helpers");
const composer = require("gulp-composer");

module.exports = {
  deps: [
    "deploy",
  ],
  fn: (gulp, callback) => {
    const version = helpers.version();

    gulp
      .src(paths.deploy.addon.src)
      .pipe(gulp.dest(paths.deploy.addon.dist))
      .on("end", () => {
        gulp
          .src(paths.deploy.themes.src)
          .pipe(gulp.dest(paths.deploy.themes.dist))
          .on("end", () => {
            del(paths.vendors.deleteList).then(() => {
              del(paths.deploy.addon.proFiles).then(() => {
                del(paths.deploy.addon.liteFiles).then(() => {
                  composer(
                    "dumpautoload",
                    { "working-dir": paths.deploy.buildPath + "freeform_next" },
                  )
                    .on("end", () => {
                      zipFolder(
                        paths.deploy.buildPath,
                        "dist/EE-Freeform-Express_" + version + ".zip",
                        function (err) {
                          if (err) {
                            callback(err);
                          } else {
                            del(paths.deploy.buildPath).then(() => {
                              callback()
                            });
                          }
                        },
                      );
                    });

                });
              });
            });
          })
          .on("error", (err) => {
            callback(err);
          });
      })
      .on("error", (err) => {
        callback(err);
      });
  },
};

