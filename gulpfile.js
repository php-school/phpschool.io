var gulp        = require('gulp');
var post        = require('gulp-postcss');
var cssnano     = require('cssnano');
var pre         = require('autoprefixer');
var sass        = require('gulp-sass');
var imp         = require('postcss-import');
var easysvg     = require('easy-svg');
var concat      = require('gulp-concat');
var uglify      = require('gulp-uglify');
var imagemin    = require('gulp-imagemin');

gulp.task('sass', function () {
    gulp.src('scss/core.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(post([imp, pre, cssnano]))
        .pipe(gulp.dest('public/css'));

    return gulp.src('scss/page-login.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(post([imp, pre, cssnano]))
        .pipe(gulp.dest('public/css'));
});

gulp.task('svg', function () {
    return gulp.src('public/img/svg/**')
        .pipe(easysvg.stream())
        .pipe(gulp.dest('public/img/icons'))
});

gulp.task('minify', function () {
    gulp.src(['public/js/highlight.min.js', 'public/js/main.js'])
        .pipe(concat('main.min.js'))
        .pipe(gulp.dest('public/js'))
        .pipe(uglify())
        .pipe(gulp.dest('public/js'))
});

gulp.task('img', function () {
    gulp.src('public/img/blog/*')
        .pipe(imagemin())
        .pipe(gulp.dest('public/img/blog-dist/'))
});

gulp.task('build-all', ['sass', 'svg', 'minify', 'img']);

gulp.task('default', ['build-all']);


