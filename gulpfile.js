var gulp        = require('gulp');
var connect     = require('gulp-connect-php');
var bs          = require('browser-sync');
var sourcemaps  = require('gulp-sourcemaps');
var post        = require('gulp-postcss');
var cssnano     = require('cssnano');
var pre         = require('autoprefixer');
var sass        = require('gulp-sass');
var imp         = require('postcss-import');
var vfs         = require('vinyl-fs');
var easysvg     = require('easy-svg');

gulp.task('serve', ['sass', 'svg'], function() {
    connect.server({
        base: 'public',
        stdio: 'ignore',
        bin: process.env.GULPPHP !== 'undefined' ? process.env.GULPPHP : 'php',
        port: 8000
    }, function () {
        bs({
            proxy: '127.0.0.1:8000'
        });
    });

    gulp.watch('scss/**', ['sass']);
    gulp.watch('public/img/icons/**', ['svg'])

    gulp.watch('templates/**/*.phtml').on('change', function () {
        bs.reload();
    });
});

gulp.task('sass', function () {
    return gulp.src('scss/core.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(post([imp, pre, cssnano]))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('public/css'))
        .pipe(bs.stream());
});

gulp.task('svg', function () {
    return gulp.src('public/img/svg/**')
        .pipe(easysvg.stream())
        .pipe(gulp.dest('public/img/icons'))
})

gulp.task('default', ['serve']);


