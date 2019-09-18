const paths    = require("./_paths"),
      composer = require("gulp-composer"),
      helpers  = require("./_helpers");

module.exports = {
  fn: function (gulp, callback) {
    let options = {
      "working-dir": "./src/freeform_next",
      "optimize-autoloader": true,
    };

    if (helpers.isProd()) {
      options["no-dev"] = true;
    }

    return composer(options);
  },
};
