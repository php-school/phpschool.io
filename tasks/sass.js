var sass     = require('node-sass');
var CleanCSS = require('clean-css');
var dirname  = require('path').dirname;
var relative = require('path').relative;
var Rx       = require('rx');

/**
 * Process SASS
 * @param deferred
 * @param previous
 * @param ctx
 */
function processSass (obs, opts, ctx) {

    var log      = obs.log;
    var min      = new CleanCSS({relativeTo: dirname(opts.input)});
    var process  = Rx.Observable.fromNodeCallback(sass.render);
    var prefixer = require('postcss')([require('autoprefixer')]);

    /**
     * Kick it all off by running through SASS first
     */
    process({file: ctx.path.make(opts.input)})
        .flatMap(function (x) {
            return Rx.Observable.fromPromise(prefixer.process(x.css))
        })
        .pluck('css')
        .map(min.minify.bind(min))
        .pluck('styles')
        .do(function (x) {
            log.fileInfo('CSS written: {yellow:%s}', opts.output);
            ctx.file.write(opts.output, x);
            obs.done();
        })
        .catch(function (err) {
            handleError(err);
            return Rx.Observable.empty();
        })
        .subscribe();

    /**
     * Handle SASS Errors nicely
     */
    function handleError (err) {
        if (err.file && err.line) {
            //err.silent = true;
            err.crossbowMessage = obs.compile([
                '{cyan:message ::} ' + String(err.message),
                '{cyan:   file ::} ' + relative(ctx.opts.cwd, String(err.file)),
                '{cyan:   line ::} ' + String(err.line),
                '{cyan: column ::} ' + String(err.column)
            ].join('\n'));
        }
        obs.onError(err);
    }
}

module.exports.tasks = [processSass];

