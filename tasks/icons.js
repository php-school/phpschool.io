var symbols = require('easy-svg').stream;

/**
 * Define the tasks that make up a build
 * @type {Object}
 */
var tasks = [makeSymbols];

/**
 * @param deferred
 * @param previous
 * @param ctx
 */
function makeSymbols(obs, opts, ctx) {
    return ctx.vfs.src(ctx.path.make(opts.input))
        .pipe(symbols())
        .on('error', obs.onError.bind(obs))
        .pipe(ctx.vfs.dest(ctx.path.make(opts.output)))
        .on('end', obs.onCompleted.bind(obs));
}

module.exports.tasks = tasks;

