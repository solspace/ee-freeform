const gutil = require("gulp-util");
const fs = require("fs");

module.exports = {
  isProd: () => {
    return gutil.env.env === "prod";
  },
  version: () => {
    const fileContent = fs.readFileSync("src/freeform_next/addon.setup.php", "utf8");
    const regex = /['"]version['"]\s*=>\s*['"]([0-9\.]+)['"]/g;

    return regex.exec(fileContent)[1];
  },
};
