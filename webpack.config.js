const path = require('path')
      dir = './vendor/blazervel/blazervel'

module.exports = {
  resolve: {
    alias: {
      '@blazervel/blazervel': path.resolve(`${dir}/resources/js/utils/dist`),
      '@blazervel/react': path.resolve(`${dir}/resources/js/react/dist`),
      '@app': path.resolve('./resources/js')
    },
  },
  output: {
    chunkFilename: 'js/[name].js?id=[chunkhash]',
  }
}