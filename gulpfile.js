const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));

// Paths to your SCSS files
const paths = {
    scss: {
        src: 'sass/style.scss',
        dest: './'
    }
};

// Compile SCSS to CSS
function compileSass() {
    return gulp.src(paths.scss.src)
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest(paths.scss.dest));
}

// Watch for changes in SCSS files
function watchFiles() {
    gulp.watch(paths.scss.src, compileSass);
}

// Define complex tasks
const build = gulp.series(compileSass);
const watch = gulp.parallel(watchFiles);

// Export tasks
exports.compile = compileSass;
exports.watch = watch;
exports.default = build;