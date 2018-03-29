const gutil = require("gulp-util");

module.exports = {
  isProd: function isProd() {
    return gutil.env.env === "prod";
  },
};
