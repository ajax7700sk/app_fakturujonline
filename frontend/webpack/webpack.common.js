
const Path = require('path');

module.exports = {
	entry: {
		main: Path.resolve(__dirname, '../js/main.js')
	},
	output: {
		path: Path.join(__dirname, '../../dist'),
		filename: 'js/[name].js',
		publicPath: '/microsite/dist/'
	},
	resolve: {
		alias: {
			'~': Path.resolve(__dirname, '../')
		}
	},
	module: {
		rules: [
			{
				test: /\.mjs$/,
				include: /node_modules/,
				type: 'javascript/auto'
			}
		]
	}
};
