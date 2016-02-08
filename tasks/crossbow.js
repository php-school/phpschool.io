var crossbow = require('crossbow');

function crossbowBuild (obs, opts, ctx) {

    var input = opts.input.map(function (item) {
        return ctx.resolve(item);
    });

    ctx.vfs.src(input)
        .pipe(crossbow.stream({
            config: {
                prettyUrls: true,
                base: opts.base,
                errorHandler: function (err, compiler) {
                    err.crossbowMessage = obs.compile(compiler.getErrorString(err)[0])
                    obs.onError(err);
                }
            },
            data: opts.data
        }))
        .pipe(ctx.vfs.dest(ctx.opts.cwd))
        .on("end", function () {
            obs.done();
        });
}

module.exports.tasks = [crossbowBuild];