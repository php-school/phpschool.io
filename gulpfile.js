var gulp        = require('gulp');
var bs          = require('browser-sync');
var sourcemaps  = require('gulp-sourcemaps');
var post        = require('gulp-postcss');
var cssnano     = require('cssnano');
var pre         = require('autoprefixer');
var sass        = require('gulp-sass');
var imp         = require('postcss-import');
var execSync    = require('child_process').execSync;
var spawn       = require('child_process').spawn;
var easysvg     = require('easy-svg');
var minify      = require('gulp-minify');
var concat      = require('gulp-concat');
var rename      = require('gulp-rename');
var uglify      = require('gulp-uglify');
var imagemin    = require('gulp-imagemin');
var babel       = require('gulp-babel');
var browserify  = require('browserify');
var source      = require('vinyl-source-stream');
var buffer      = require('vinyl-buffer');
var gutil       = require('gulp-util');

gulp.task('serve', ['build-all'], function(cb) {
    
    const dc = spawn('docker-compose', ['up', '-d'], {
        stdio: 'inherit' // pipe stdout/stderr to process
    });

    // Catch errors from prev command docker
    dc.on('error', function (err) {
        throw err;
    });

    // 'close' indicates docker-compose command ended;
    dc.on('close', function (code) {

        if (code !== 0) throw new Error('docker-compose did not complete!');

        gulp.start('build-db');

        bs.init({
            proxy: '127.0.0.1:8000',
            open: false
        }, function () {
            cb(); // all good, signal to gulp that the serve task is complete
        });
    });

    gulp.watch('public/js/*.js', ['js', () => bs.reload()]);
    gulp.watch('scss/**', ['sass', 'clear-cache', 'generate-blog', () => bs.reload()]);
    gulp.watch('public/img/icons/**', ['svg']);

    gulp.watch('templates/**/*.phtml', ['clear-cache', () => bs.reload()]);
    gulp.watch('vendor/php-school/php-workshop/src/**/*.php', ['rebuild-doc-cache', 'clear-cache', () => bs.reload()])
    gulp.watch('posts/*.md', ['generate-blog', () => bs.reload()])
});

gulp.task('generate-blog', ['sass'], function () {
    execSync('php bin/app generate-blog')
});

gulp.task('clear-cache', function () {
    execSync('docker exec php-school-fpm php bin/app clear-cache');
});

gulp.task('rebuild-doc-cache', function () {
    execSync('docker exec php-school-fpm php bin/app generate-docs');
});

gulp.task('validate-db', function () {
    execSync('docker exec php-school-fpm vendor/bin/doctrine orm:validate-schema');
});

gulp.task('build-db', function () {
    execSync('docker exec php-school-fpm vendor/bin/doctrine orm:clear-cache:metadata')
    execSync('docker exec php-school-fpm vendor/bin/doctrine orm:schema-tool:update -f');
});

gulp.task('sass', function () {
    gulp.src('scss/core.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(post([imp, pre, cssnano]))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('public/css'))
        .pipe(bs.stream());

    return gulp.src('scss/page-login.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(post([imp, pre, cssnano]))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('public/css'))
        .pipe(bs.stream());
});

gulp.task('svg', function () {
    return gulp.src('public/img/svg/**')
        .pipe(easysvg.stream())
        .pipe(gulp.dest('public/img/icons'))
});

gulp.task('js', function () {
    // Main pages
    gulp.src(['public/js/highlight.min.js', 'public/js/main.js'])
        .pipe(babel())
        .pipe(concat('main.min.js'))
        .pipe(gulp.dest('public/js/dist'))
        .pipe(uglify())
        .pipe(gulp.dest('public/js/dist'));

    // Install page
    var installJS = 'public/js/install.js';
    var b = browserify({
        entries: installJS,
        debug: true
    });

    return b.bundle()
        .pipe(source(installJS))
        .pipe(buffer())
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(babel())
        .pipe(concat('install.min.js'))
        .pipe(gulp.dest('public/js/dist'))
        .pipe(uglify())
        .on('error', gutil.log)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('public/js/dist'));
});

gulp.task('img', function () {
    gulp.src('public/img/blog/*')
        .pipe(imagemin())
        .pipe(gulp.dest('public/img/blog-dist/'))
});

gulp.task('build-all', ['sass', 'svg', 'js', 'img']);

gulp.task('deploy', function () {
    execSync('cap production deploy');
});

gulp.task('default', ['serve']);


