var gulp        = require('gulp');
var bs          = require('browser-sync');
var sourcemaps  = require('gulp-sourcemaps');
var post        = require('gulp-postcss');
var cssnano     = require('cssnano');
var pre         = require('autoprefixer');
var sass        = require('gulp-sass');
var imp         = require('postcss-import');
var execSync    = require('child_process').execSync;
var exec        = require('child_process').exec;
var easysvg     = require('easy-svg');

gulp.task('serve', ['sass', 'svg'], function() {
    
    exec('docker-compose build && docker-compose up -d', function () {
        bs.init({
            proxy: '127.0.0.1:8000',
            open: false
        });
    });

    gulp.watch('scss/**', ['sass']);
    gulp.watch('public/img/icons/**', ['svg'])

    gulp.watch('templates/**/*.phtml', ['clear-cache', bs.reload]);
    gulp.watch('vendor/php-school/php-workshop/src/**/*.php', ['rebuild-doc-cache', 'clear-cache', bs.reload])
});

gulp.task('clear-cache', function () {
    execSync('docker exec php-school-fpm php bin/app clear-cache');
});

gulp.task('rebuild-doc-cache', function () {
    execSync('docker exec php-school-fpm php bin/app generate-docs');
});

gulp.task('orm:validate-schema', function () {
    execSync('docker exec php-school-fpm vendor/bin/doctrine orm:validate-schema');
})

gulp.task('orm:validate-schema', function () {
    execSync('docker exec php-school-fpm vendor/bin/doctrine orm:schema-tool:update -f');
})

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

gulp.task('build-all', ['sass', 'svg']);

gulp.task('deploy', function () {
    execSync('cap production deploy');
});

gulp.task('default', ['serve']);


