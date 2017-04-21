var src = "src/",
    dist = "dist/",
    buildPath = dist + "build/";

var paths = {
  scripts: {
    src: [src + "external/scripts/cp/**/*.js"],
    dest: src + "freeform_next/javascript/",
    deleteList: [src + "freeform_next/javascript/**/*.js"]
  },
  react: {
    src: [src + "external/scripts/composer/**/*.js"],
    dest: src + "themes/freeform_next/javascript/composer/"
  },
  styles: {
    src: [src + "external/styles/**/*.scss"],
    dest: src + "themes/freeform_next/css/"
  },
  fonts: {
    src: [src + "external/font/**/*.*"],
    dest: src + "themes/freeform_next/font/"
  },
  themes: {
    src: [src + "external/themes/**/*.*"],
    dest: src + "themes/freeform_next/lib/",
  },
  deploy: {
    dist: dist + "**/*",
    buildPath: buildPath,
    addon: {
      src: src + "freeform_next/**/*",
      dist: buildPath + "freeform_next/",
      proFiles: [
        buildPath + "freeform_next/Integrations",
      ]
    },
    themes: {
      src: src + "themes/**/*",
      dist: buildPath + "themes/"
    }
  },
  vendors: {
    dist: buildPath + "freeform_next/vendor/",
    deleteList: [
      buildPath + "freeform_next/vendor/**/tests",
      buildPath + "freeform_next/vendor/**/Tests",
      buildPath + "freeform_next/vendor/**/test",
      buildPath + "freeform_next/vendor/**/doc",
      buildPath + "freeform_next/composer.lock",
      buildPath + "freeform_next/composer.json"
    ]
  },
};

module.exports = paths;
