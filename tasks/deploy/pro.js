const paths = require("../_paths");
const del = require("del");
const zipFolder = require("zip-folder");
const helpers = require("../_helpers");
const replace = require("gulp-replace");

module.exports = {
  deps: [
    "deploy",
  ],
  fn: (gulp, callback) => {
    const version = helpers.version();

    gulp
      .src(paths.deploy.addon.src)
      .pipe(replace(/('name'\s+=>\s+')Freeform Lite',/g, "$1Freeform Pro',"))
      .pipe(gulp.dest(paths.deploy.addon.dist))
      .on("end", () => {
        gulp
          .src(paths.deploy.themes.src)
          .pipe(gulp.dest(paths.deploy.themes.dist))
          .on("end", () => {
            del(paths.vendors.deleteList).then(() => {
              zipFolder(
                paths.deploy.buildPath,
                "dist/EE-Freeform-Pro_" + version + ".zip",
                (err) => {
                  if (err) {
                    callback(err);
                  } else {
                    del(paths.deploy.buildPath).then(() => {
                      callback();
                    })
                  }
                },
              );
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
