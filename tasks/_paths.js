module.exports = {
  src: "src/",
  scripts: {
    src: ["src/external/scripts/cp/**/*.js"],
    dest: "src/freeform_next/javascript/",
    deleteList: ["src/freeform_next/javascript/**/*.js"]
  },
  react: {
    src: ["src/external/scripts/composer/**/*.js"],
    dest: "src/themes/freeform_next/javascript/composer/"
  },
  styles: {
    src: ["src/external/styles/**/*.scss"],
    dest: "src/themes/freeform_next/css/"
  },
  fonts: {
    src: ["src/external/font/**/*.*"],
    dest: "src/themes/freeform_next/font/"
  },
  vendors: {
    dist: "dist/freeform_next/vendor/",
    deleteList: [
      "dist/freeform_next/vendor/**/tests",
      "dist/freeform_next/vendor/**/Tests",
      "dist/freeform_next/vendor/**/test",
      "dist/freeform_next/vendor/**/doc",
      "dist/freeform_next/composer.lock",
      "dist/freeform_next/composer.json"
    ]
  },
  deploy: {
    src: "src/freeform_next/**/*",
    dist: "dist/freeform_next/"
  }
};
