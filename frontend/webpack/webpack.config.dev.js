const Path = require('path');
const Webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const merge = require('webpack-merge');
const common = require('./webpack.common.js');

module.exports = merge(common, {
  watch: true,
  mode: 'development',
  devtool: 'cheap-eval-source-map',
  devServer: {
    port: 4000,
    proxy: {
      "http://[::1]:4000" : "http://[::1]:8080",
      // "secure": false,
      // "changeOrigin": true
    }
  },
  plugins: [
    new Webpack.DefinePlugin({
      'process.env.NODE_ENV': JSON.stringify('development')
    }),
    new MiniCssExtractPlugin({
      path: Path.join(__dirname, '../../dist'),
      filename: 'css/[name].css'
    }),
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
          loader: MiniCssExtractPlugin.loader,
        }, 'css-loader?sourceMap=true', 'sass-loader']
      }
    ]
  }
});
