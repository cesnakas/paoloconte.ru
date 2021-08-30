
/**
 * Require dependencies.
 */

var gulp = require('gulp')
, concat = require('gulp-concat')
, uglify = require('gulp-uglify')
, less = require('gulp-less')
, csso = require('gulp-csso')
, rename = require('gulp-rename');

/**
 * Project assets.
 */

var paths = {
  javascript: [
    'vendor/jquery/dist/jquery.js',
    'vendor/jquery.maskedinput/dist/jquery.maskedinput.min.js',
    'vendor/inputmask/dist/min/jquery.inputmask.bundle.min.js',
    'vendor/select2/select2.js',
    'vendor/select2/select2_locale_ru.js',
    'vendor/owl.carousel/dist/owl.carousel.js',
    'vendor/bootstrap/js/transition.js',
    'vendor/bootstrap/js/modal.js',
    'vendor/bootstrap/js/tab.js',
    'node_modules/element-closest/element-closest.js',
    'javascript/vendor/*.js',
    'javascript/Global.js',
    'javascript/Sliders.js',
    'javascript/Gmap.js',
    'javascript/Main.js',
    'javascript/detail_nav.js'
  ],
  less: [
    'less/application.less'
  ],
  stylesheets: [
    'vendor/owl.carousel/dist/assets/owl.carousel.css',
    'vendor/detail_nav.css',
    'css/vendor/*.css'
  ]
};

/**
 * Build javascript.
 */

gulp.task('javascript', function() {
return gulp.src(paths.javascript)
.pipe(concat('javascript-build.js'))
.pipe(gulp.dest('build/js'));
});

/**
 * Build less styles.
 */

gulp.task('less', function() {
return gulp.src(paths.less)
.pipe(less())
.pipe(concat('less-build.css'))
.pipe(gulp.dest('build/css'));
});

/**
 * Build stylesheets.
 */

gulp.task('stylesheets', function() {
return gulp.src(paths.stylesheets)
.pipe(concat('stylesheets-build.css'))
.pipe(gulp.dest('build/css'));
});

/**
 * Build distribution.
 */

gulp.task('dist', ['javascript', 'less', 'stylesheets'], function() {
gulp.src(['build/css/stylesheets-build.css', 'build/css/less-build.css'])
.pipe(concat('template_styles.css'))
    .pipe(gulp.dest('./'));

  return gulp.src('build/js/javascript-build.js')
    .pipe(rename('scripts.js'))
    .pipe(gulp.dest('./'));
});

/**
 * Rerun the task when a file changes
 */

gulp.task('watch', function() {
gulp.watch(paths.javascript, ['javascript', 'less', 'stylesheets', 'dist']);
gulp.watch([paths.less, 'less/**/*.less'], ['javascript', 'less', 'stylesheets', 'dist']);
gulp.watch(paths.stylesheets, ['javascript', 'less', 'stylesheets', 'dist']);
});

/**
 * The default task (called when you run `gulp` from cli)
 */

gulp.task('default', ['dist']);