const fs = require("fs");
const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");

// Load settings from theme.json
const themeJson = require("./theme.json");
const layout = themeJson.settings?.layout || {};
const colors = themeJson.settings?.color?.palette || [];

const contentWidth = layout.contentSize || "700px";
const wideWidth = layout.wideSize || "1200px";

// Generate SCSS variables from theme.json
let scssVars = `$content-width: ${contentWidth};
$wide-width: ${wideWidth};

// Colors from theme.json
`;

colors.forEach((color) => {
  const varName = color.slug.replace(/-/g, "_"); // Convert kebab-case to snake_case for SCSS
  scssVars += `$${varName}: ${color.color};\n`;
});

// Ensure the SCSS vars file exists and write it
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
    filename: "js/build/[name].min.[fullhash].js",
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
              sourceMap: true,
            },
          },
          {
            loader: "sass-loader",
            options: {
              implementation: require("sass"),
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
      // Images
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
  resolve: {
    extensions: [".js", ".jsx", ".json"],
  },
  devtool: "source-map",
  mode: process.env.NODE_ENV === "production" ? "production" : "development",
  stats: {
    errorDetails: true,
  },
};
