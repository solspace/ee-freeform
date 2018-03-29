const paths = require("../_paths"),
      del   = require("del");

module.exports = {
  fn: function (gulp, callback) {
    return del(paths.fonts.dest);
  },
};
