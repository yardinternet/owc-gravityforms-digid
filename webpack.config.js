module.exports = {
    entry: './resources/js/countdown.js',
    mode: 'production',
    module: {
      rules: [
        {
          test: /\.(js)$/,
          exclude: /node_modules/,
          use: ['babel-loader']
        }
      ]
    },
    resolve: {
      extensions: ['*', '.js']
    },
    output: {
      path: __dirname + '/resources/js/dist',
      publicPath: '/',
      filename: 'app.js'
    },
  };
