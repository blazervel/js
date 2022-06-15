const path = require('path')
      dir = process.env.BLAZERVELOPMENT == true ? '../packages/blazervel/blazervel' : './vendor/blazervel/blazervel'

module.exports = {
  resolve: {
    alias: {
      '@blazervel/blazervel': path.resolve(`${dir}/resources/js/utils/dist/index.js`),
      '@blazervel/react': path.resolve(`${dir}/resources/js/react/dist/index.js`),
      '@app': path.resolve('./resources/js')
    },
  },
  output: {
    chunkFilename: 'js/[name].js?id=[chunkhash]',
  }
}