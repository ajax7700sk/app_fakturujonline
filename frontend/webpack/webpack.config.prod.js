const Path = require('path');
const glob = require('glob-all');
const Webpack = require('webpack');
const merge = require('webpack-merge');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const PurgecssPlugin = require('purgecss-webpack-plugin');
const common = require('./webpack.common.js');

const PATHS = {
    customJS: Path.join(__dirname, '../js/**/*.js'),
    latte: Path.join(__dirname, '../../../../app/MicrositeModule/templates/**/*.latte'),
};


const files = glob.sync([
    PATHS.customJS,
    PATHS.latte
]);

console.log('files:');
//console.log(files);
module.exports = merge(common, {
    mode: 'production',
    devtool: 'source-map',
    stats: 'errors-only',
    bail: true,
    plugins: [
        new Webpack.DefinePlugin({
            'process.env.NODE_ENV': JSON.stringify('production')
        }),
        new Webpack.optimize.ModuleConcatenationPlugin(),
        new MiniCssExtractPlugin({
            filename: 'css/[name].css'
        }),
        // new PurgecssPlugin({
        //     paths: files,
        // }),
    ],
    module: {
        rules: [
            {
                test: /\.(js)$/,
                include: Path.resolve(__dirname, '../'),
                enforce: 'pre',
                loader: 'eslint-loader',
                options: {
                    emitWarning: true,
                }
            },
            {
                test: /\.(js)$/,
                include: Path.resolve(__dirname, '../'),
                loader: 'babel-loader'
            },
            {
                test: /\.s?css$/i,
                include: Path.resolve(__dirname, '../'),
                use: [{
                    loader: MiniCssExtractPlugin.loader
                }, 'css-loader?sourceMap=true', 'sass-loader']
            }
        ]
    }
});
