var paths    = require("./_paths");
var composer = require("gulp-composer");
var helpers  = require("./_helpers");

module.exports = {
  fn: function (gulp, callback) {
    var options = {
      "working-dir": "./src/freeform_next",
    };

    if (helpers.isProd()) {
      options["no-dev"] = true;
    }

    return composer(options);
  }
};
