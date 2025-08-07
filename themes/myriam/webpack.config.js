const fs = require("fs");
const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");

// Load layout settings from theme.json
const themeJson = require("./theme.json");
const layout = themeJson.settings?.layout || {};
const contentWidth = layout.contentSize || "700px";
const wideWidth = layout.wideSize || "1200px";

// Generate SCSS variables from theme.json
const scssVars = `$content-width: ${contentWidth};
$wide-width: ${wideWidth};
`;
// ADD THESE 4 LINES HERE:
const scssDir = path.dirname("./css/src/base/_theme-values.scss");
if (!fs.existsSync(scssDir)) {
  fs.mkdirSync(scssDir, { recursive: true });
}
fs.writeFileSync("./css/src/base/_theme-values.scss", scssVars);

module.exports = {
  entry: {
    main: ["./js/src/index.js", "./css/src/main.scss"],
  },
  output: {
    path: path.resolve(__dirname),
    filename: "js/build/main.min.[fullhash].js",
    publicPath: "/",
  },
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader",
          options: {
            presets: ["@babel/preset-env", "@babel/preset-react"],
          },
        },
      },
      {
        test: /\.(s[ac]ss|css)$/i,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: "css-loader",
            options: {
              // url: false removed - now CSS can reference assets
            },
          },
          {
            loader: "sass-loader",
            options: {
              sassOptions: {
                includePaths: [path.resolve(__dirname, "css/src")],
              },
            },
          },
        ],
      },
      // ADD THESE TWO NEW RULES:
      {
        test: /\.(woff|woff2|eot|ttf|otf)$/,
        type: "asset/resource",
        generator: {
          filename: "font/[name][ext]",
        },
      },
      {
        test: /\.(png|jpg|gif)$/,
        type: "asset/resource",
        generator: {
          filename: "css/build/img/[name][ext]",
        },
      },
    ],
  },
  plugins: [
    new CleanWebpackPlugin({
      cleanOnceBeforeBuildPatterns: ["js/build/*", "css/build/*"],
    }),
    new MiniCssExtractPlugin({
      filename: "css/build/theme.min.[fullhash].css",
    }),
  ],
  optimization: {
    minimizer: ["...", new CssMinimizerPlugin()],
  },
  externals: {
    "@wordpress/hooks": ["wp", "hooks"],
    "@wordpress/compose": ["wp", "compose"],
    "@wordpress/element": ["wp", "element"],
    "@wordpress/block-editor": ["wp", "blockEditor"],
    "@wordpress/components": ["wp", "components"],
  },
  devtool: "source-map",
  mode: "development",
};
