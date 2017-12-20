const paths = require("./tasks/_paths"),
      gulp = require("gulp"),
      gulpRequireTasks = require("gulp-require-tasks");

global.deployment = false;

gulpRequireTasks({
  path: process.cwd() + '/tasks'
});

gulp.task("default", ["build:scripts", "build:styles", "build:fonts", "build:themes", "build:crypt", "composer"]);

gulp.task("watch", ["build:scripts", "build:styles", "build:fonts", "build:react", "build:themes", "build:crypt", "composer"], function () {
  gulp.watch(paths.scripts.src, ["build:scripts"]);
  gulp.watch(paths.react.src, ["build:react"]);
  gulp.watch(paths.styles.src, ["build:styles"]);
  gulp.watch(paths.themes.src, ["build:themes"]);
  gulp.watch(paths.crypt.src, ["build:crypt"]);
});
