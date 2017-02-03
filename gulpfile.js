var paths           = require("./tasks/_paths")
var gulp            = require("gulp");
var gulpRequireTaks = require("gulp-require-tasks");

global.deployment = false;

gulpRequireTaks({
    path: __dirname + '/tasks'
});

gulp.task("default", ["build:scripts", "build:styles", "build:fonts", "composer"]);

gulp.task("watch", ["build:scripts", "build:styles", "build:fonts", "build:react", "composer"], function () {
    gulp.watch(paths.scripts.src, ["build:scripts"]);
    gulp.watch(paths.react.src, ["build:react"]);
    gulp.watch(paths.styles.src, ["build:styles"]);
});
