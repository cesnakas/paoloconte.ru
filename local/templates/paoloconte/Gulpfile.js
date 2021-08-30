/**
 * Require dependencies.
 */

var gulp = require('gulp')
    , concat = require('gulp-concat')
    , uglify = require('gulp-uglify')
    , less = require('gulp-less')
    , csso = require('gulp-csso')
    , rename = require('gulp-rename')
    , autoprefixer = require('gulp-autoprefixer')
    , svgstore = require('gulp-svgstore')
    , svgmin = require('gulp-svgmin')
    , cheerio = require('gulp-cheerio')
    , cleancss = require('gulp-clean-css')
    , postcss   = require('gulp-postcss');
/**
 * Project assets.
 */

var paths = {
    javascript: [
        'vendor/jquery/dist/jquery.js',
        'vendor/elevatezoom/jquery.elevateZoom-3.0.8.min.js',
        'vendor/select2/select2.js',
        'vendor/select2/select2_locale_ru.js',
        'vendor/owl.carousel/dist/owl.carousel.js',
        'vendor/bootstrap/js/transition.js',
        'vendor/bootstrap/js/modal.js',
        'vendor/bootstrap/js/tab.js',
        'vendor/jquery.maskedinput/dist/jquery.maskedinput.min.js',
        'vendor/jquery.lazyload/jquery.lazyload.js',
        'vendor/jquery.ui.widget.js',
        'vendor/jquery.iframe-transport.js',
        'vendor/jquery.fileupload.js',
        'vendor/js-cookie/src/js.cookie.js',
        'vendor/blazy/blazy.min.js',
        'vendor/object-fit-images/ofi.min.js',
        'vendor/svg4everybody.min.js',
        'vendor/jquery.menu-aim.js',
        'node_modules/element-closest/element-closest.js',
        'node_modules/swiper/dist/js/swiper.min.js',
        'node_modules/air-datepicker/dist/js/datepicker.min.js',
        'node_modules/imask/dist/imask.min.js',
        'javascript/vendor/*.js',
        'javascript/callback.js',
        'javascript/cookie.js',
        'javascript/Global.js',
        'javascript/loader.js',
        'javascript/Main.js',
        'javascript/popup.js',
        'javascript/review.js',
        'javascript/Sliders.js',
        'javascript/Static_pages.js',
        'javascript/subscribe_footer.js',
        'javascript/svgUse.js',
        'javascript/tabs.js',
        'javascript/datepicker.js',
    ],
    less: [
        'less/application.less'
    ],
    stylesheets: [
        'vendor/owl.carousel/dist/assets/owl.carousel.css',
        'node_modules/swiper/dist/css/swiper.min.css',
        'node_modules/air-datepicker/dist/css/datepicker.min.css',
        'css/vendor/*.css',
        'css/my_styles.css'
    ]
};

/**
 * Build javascript.
 */

gulp.task('javascript', function () {
    return gulp.src(paths.javascript)
        .pipe(concat('javascript-build.js'))
        .pipe(gulp.dest('build/js'));
});

/**
 * Build less styles.
 */

gulp.task('less', function () {
    return gulp.src(paths.less)
        .pipe(less())
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(concat('less-build.css'))
        .pipe(cleancss())
        .pipe(gulp.dest('build/css'));
});

/**
 * Build stylesheets.
 */

gulp.task('stylesheets', function () {
    return gulp.src(paths.stylesheets)
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(concat('stylesheets-build.css'))
        .pipe(cleancss())
        .pipe(gulp.dest('build/css'));
});

/**
 * Build svg icons.
 */

gulp.task('svg', function () {
    return gulp
        .src('images/sprite/*.svg')
        .pipe(cheerio({
            run: function ($) {
                $('[fill]').removeAttr('fill');
                $('[style]').removeAttr('style');
            },
            parserOptions: { xmlMode: true }
        }))
        .pipe(svgstore())
        .pipe(gulp.dest('build/'));
});

gulp.task('stylesheets', function () {
    return gulp.src(paths.stylesheets)
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(concat('stylesheets-build.css'))
        .pipe(cleancss())
        .pipe(gulp.dest('build/css'));
});

/**
 * Build distribution.
 */

gulp.task('dist', ['javascript', 'less', 'stylesheets', 'svg'], function () {
    gulp.src(['build/css/stylesheets-build.css', 'build/css/less-build.css'])
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(concat('template_styles.css'))
        .pipe(csso())
        .pipe(postcss([require('postcss-object-fit-images')]))
        .pipe(gulp.dest('./'));

    return gulp.src('build/js/javascript-build.js')
        .pipe(rename('scripts.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./'));
});

/**
 * Rerun the task when a file changes
 */

gulp.task('watch', function () {
    gulp.watch(paths.javascript, ['javascript', 'less', 'stylesheets', 'dist']);
    gulp.watch([paths.less, 'less/**/*.less'], ['javascript', 'less', 'stylesheets', 'dist']);
    gulp.watch(paths.stylesheets, ['javascript', 'less', 'stylesheets', 'dist']);
});

/**
 * The default task (called when you run `gulp` from cli)
 */

gulp.task('default', ['dist']);
