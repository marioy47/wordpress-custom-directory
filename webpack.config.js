const path = require("path")

module.exports = {
	entry: {
		frontend: "./src/js/frontend.js"
	},
	output: {
		filename: "[name].js",
		path: path.join(__dirname, 'js')
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: "babel-loader"
			}
		]
	}
}
