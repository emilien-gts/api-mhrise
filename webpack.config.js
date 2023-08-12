const path = require('path');
const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    .addAliases({
        assets: path.join(__dirname, '/assets/app')
    })

    .addEntry('app', './assets/app.js')

    .enableSassLoader()

    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()

    .copyFiles([
        {
            from: './assets/images',
            to: 'images/[path][name].[ext]'
        },
    ])

    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    .configureDevServerOptions(options => {
        options.allowedHosts = 'all';
    });

const config = Encore.getWebpackConfig();

module.exports = config;