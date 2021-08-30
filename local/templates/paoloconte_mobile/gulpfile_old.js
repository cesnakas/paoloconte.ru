var gulp = require('gulp');
var concat = require('gulp-concat');
var csso = require('gulp-csso');
var uglify = require('gulp-uglify');

var paths = {
    javascript: [
        'js/vendor/jquery-1.10.2.js',
        'js/my_script.js',
    ],
    css: [
        'css/my_style.css',
    ]
};

gulp.task('scripts', function() {
    gulp.src(paths.javascript)
        .pipe(concat('script.js'))
        .pipe(uglify())
        .pipe(gulp.dest(''));
});

gulp.task('styles', function() {
    gulp.src(paths.css)
        .pipe(concat('template_styles.css'))
        .pipe(csso())
        .pipe(gulp.dest(''));
});

gulp.task('default', function() {
    gulp.run('styles');
    gulp.run('scripts');

    //gulp.watch('src/**', function(event) {
    //    gulp.run('scripts');
    //})

    /*gulp.watch('css/**', function(event) {
        gulp.run('styles');
    })*/
});