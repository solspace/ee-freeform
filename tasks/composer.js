const paths    = require("./_paths"),
      composer = require("gulp-composer"),
      helpers  = require("./_helpers");

module.exports = {
  fn: function (gulp, callback) {
    var options = {
      "working-dir": "./src/freeform_next",
    };

    if (helpers.isProd()) {
      options["no-dev"] = true;
    }

    return composer(options);
  },
};
