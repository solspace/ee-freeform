var paths = require("../_paths");
var del   = require("del");

module.exports = {
    fn: function (gulp, callback) {
        return del(paths.styles.dest);
    }
};
