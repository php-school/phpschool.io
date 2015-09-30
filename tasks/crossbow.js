var crossbow = require('crossbow');

function crossbowBuild (obs, resolved, ctx) {

    var input = ctx.get('config.crossbow.input').map(function (item) {
        return ctx.resolve(item);
    });

    ctx.vfs.src(input)
        .pipe(crossbow.stream({
            config: {
                base: ctx.get('config.crossbow.base')
            },
            data: {
                site: "file:config.yml"
            }
        }))
        .pipe(ctx.vfs.dest(ctx.opts.cwd))
        .on("end", obs.onCompleted.bind(obs))
        .on('error', obs.onError.bind(obs));

}

module.exports.tasks = [crossbowBuild];