const path = require('path')

module.exports = {
  resolve: {
    alias: {
      '@blazervel/react': path.resolve('resources/js/react/dist'),
      '@blazervel/blazervel': path.resolve('resources/js/utils/dist'),
      '@app': path.resolve('resources/js'),
    },
  },
  output: {
    chunkFilename: 'js/[name].js?id=[chunkhash]',
  }
}