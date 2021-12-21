const path = require('path');

module.exports = {
  entry: './resources/js/index.js',
	mode: (process.env.NODE_ENV ? process.env.NODE_ENV : 'development'),
	output: {
    library: 'Countdown',
    libraryTarget: 'umd',
    globalObject: '(typeof self !== "undefined" ? self : this)',
    libraryExport: 'default',
    path: path.resolve(__dirname, 'resources/js/dist'),
    filename: 'app.js',
    publicPath: '/'
  },
  module: {
    rules: [
      {
        test: /\.(js)$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env']
          }
        }
      }
    ]
  },
  resolve: {
    extensions: ['*', '.js']
  },
};
