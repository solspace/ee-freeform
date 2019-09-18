const paths = require("./_paths");
const del = require("del");
const zipFolder = require("zip-folder");
const helpers = require("./_helpers");
const fs = require("fs");
const replace = require("gulp-replace");

module.exports = {
  deps: [
    "build:scripts",
    "build:styles",
    "build:react",
    "build:fonts",
    "build:themes",
    "build:crypt",
    "composer",
    "clean:deploy",
  ],
  fn: (gulp, callback) => {
    if (!helpers.isProd()) {
      console.log("!!! Deployment must be run with '--env prod' environment variable !!!");
      process.exit(1);
    }

    callback();
  },
};
