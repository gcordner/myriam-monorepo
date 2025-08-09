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

// Ensure the SCSS vars file exists and write it
const scssDir = path.dirname("./css/src/base/_theme-values.scss");
if (!fs.existsSync(scssDir)) {
  fs.mkdirSync(scssDir, { recursive: true });
}
fs.writeFileSync("./css/src/base/_theme-values.scss", scssVars);

module.exports = {
  entry: {
    main: ["./js/src/index.js", "./css/src/main.scss"],
    overrides: "./css/src/overrides.scss",
  },
  output: {
    path: path.resolve(__dirname),
    filename: "js/build/[name].min.[fullhash].js", // <-- use [name]
    publicPath: "/wp-content/themes/myriam/",
  },
  module: {
    rules: [
      // JS/JSX
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
      // CSS/SCSS
      {
        test: /\.(s[ac]ss|css)$/i,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: "css-loader",
            options: {
              // keep default url handling so assets referenced in CSS work
              sourceMap: true,
            },
          },
          {
            loader: "sass-loader",
            options: {
              implementation: require("sass"), // fixes legacy JS API deprecation
              sourceMap: true,
              sassOptions: {
                includePaths: [path.resolve(__dirname, "css/src")],
              },
            },
          },
        ],
      },
      // Fonts
      {
        test: /\.(woff2?|eot|ttf|otf)$/i,
        type: "asset/resource",
        generator: {
          filename: "font/[name][ext]",
        },
      },
      // Images (used from CSS)
      {
        test: /\.(png|jpe?g|gif|svg)$/i,
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
      filename: (pathData) =>
        pathData.chunk.name === "overrides"
          ? "css/build/overrides.css" // stable name for your late overrides
          : "css/build/theme.min.[fullhash].css", // hashed main bundle
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
  resolve: {
    extensions: [".js", ".jsx", ".json"],
  },
  devtool: "source-map",
  mode: process.env.NODE_ENV === "production" ? "production" : "development",
  stats: {
    errorDetails: true,
  },
};
