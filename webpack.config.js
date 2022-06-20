const path = require('path'),
      dir = process.env.BLAZERVELOPMENT == true ? '../packages/blazervel' : './vendor/blazervel/blazervel'

module.exports = {
  resolve: {
    alias: {
      '@app': path.resolve('./resources/js'),
      '@blazervel/blazervel': path.resolve(`${dir}/resources/js/utils/dist`),
      '@blazervel/react': path.resolve(`${dir}/resources/js/react/dist`),
      '@inertiajs/inertia-react': path.resolve('./node_modules/@inertiajs/inertia-react'),
      '@inertiajs/inertia': path.resolve('./node_modules/@inertiajs/inertia'),
      'axios': path.resolve('./node_modules/axios'),
      'react-dom': path.resolve('./node_modules/react-dom'),
      'react': path.resolve('./node_modules/react'),
    },
  },
  output: {
    chunkFilename: 'js/[name].js?id=[chunkhash]',
  }
}