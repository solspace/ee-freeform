var paths = require("./tasks/_paths")
var gulp = require("gulp");
var gulpRequireTasks = require("gulp-require-tasks");

global.deployment = false;

gulpRequireTasks({
  path: process.cwd() + '/tasks'
});

gulp.task("default", ["build:scripts", "build:styles", "build:fonts", "build:themes", "composer"]);

gulp.task("watch", ["build:scripts", "build:styles", "build:fonts", "build:react", "build:themes", "composer"], function () {
  gulp.watch(paths.scripts.src, ["build:scripts"]);
  gulp.watch(paths.react.src, ["build:react"]);
  gulp.watch(paths.styles.src, ["build:styles"]);
});
