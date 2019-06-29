const path = require('path');
const CopyPlugin = require('copy-webpack-plugin');
const CssWrap = require('css-wrap');
const uglifycss = require('uglifycss');

module.exports = {
  output: {
    path: path.resolve(__dirname, 'src/assetbundles/prismsyntaxhighlighting/dist/js'),
    filename: 'PrismSyntaxHighlighting.js'
  },
  module: {
    rules: [
      {
        test: /\.m?js$/,
        exclude: /(node_modules|bower_components)/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env']
          }
        }
      }
    ]
  },
  plugins: [

    // Copy themes and namespace CSS
    new CopyPlugin([
      {
        from: path.resolve(__dirname, 'vendor/prismjs/prism/themes'),
        to: path.resolve(__dirname, 'src/assetbundles/prismsyntaxhighlighting/dist/css/prism/themes/[name].min.[ext]'),
        transform: function(fileContent, filePath) {
          let transformedCss = CssWrap(fileContent.toString(), {
            selector: `.${path.parse(filePath).name}`
          });
          return uglifycss.processString(transformedCss);
        }
      },
    ]),

    // Copy components (languages)
    new CopyPlugin([
      {
        from: path.resolve(__dirname, 'vendor/prismjs/prism/components'),
        to: path.resolve(__dirname, 'src/assetbundles/prismsyntaxhighlighting/dist/js/prism/components'),
        test: /\.min\.js$/
      },
    ]),

    // Copy plugins
    new CopyPlugin([
      {
        from: path.resolve(__dirname, 'vendor/prismjs/prism/plugins'),
        to: path.resolve(__dirname, 'src/assetbundles/prismsyntaxhighlighting/dist/js/prism/plugins'),
        test: /\.min\.js$/
      },
    ]),

    // Copy components JSON definition
    new CopyPlugin([
      {
        from: path.resolve(__dirname, 'vendor/prismjs/prism/components.json'),
        to: path.resolve(__dirname, 'src/assetbundles/prismsyntaxhighlighting/dist/js/prism/components.json'),
        test: /\.min\.js$/
      },
    ]),
  ],
};
