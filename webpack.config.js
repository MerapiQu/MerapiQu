#!/usr/bin/env node
/* eslint-disable @typescript-eslint/no-require-imports */
/* eslint-disable @typescript-eslint/no-unused-vars */
/* eslint-disable no-undef */
const path = require('path');
const webpack = require("webpack");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const { BundleAnalyzerPlugin } = require("webpack-bundle-analyzer");
const TerserPlugin = require("terser-webpack-plugin");

const stylesHandler = MiniCssExtractPlugin.loader;
const isAnalyze = process.argv.includes("--analyze");
const isDevelopment = process.env.NODE_ENV === "development";

const scanPageEntry = (options) => {

    options = Object.assign({
        ignore: []
    }, options)
    const ignores = [
        '**/node_modules/**',
        ...(Array.isArray(options.ignore) ? options.ignore : [])
    ];
    const root = path.resolve(__dirname, "..", "ui", "pages");

    return glob.sync(path.join(root, "**", "*.{js,ts,[jt]sx}"), { ignores }).reduce((acc, file) => {
        const name = path.basename(file).replace(/\.\w+$/, "")
        if ((options.ignore || []).includes(name)) return acc;
        const fullPath = path.resolve(file)
        const pathNames = fullPath.substring(root.length).split(path.sep).filter(v => v.length > 0)
        pathNames.pop();
        pathNames.push(name);
        const payload = {
            import: fullPath,
            dependOn: 'vendor',
            filename: `pages/${pathNames.join('/')}.bundle.js`,
            library: {
                name: ['modules', 'pages', ...pathNames],
                type: 'umd'
            }
        }
        const key = `pages-${name}`;

        acc[`${key}`] = payload;
        return acc;
    }, {})
}

module.exports = {
    devtool: 'source-map',
    mode: isDevelopment ? "development" : "production",
    entry: {
        vendor: ['react', 'react-dom'],
        "main": {
            import: './src/main.tsx',
            dependOn: 'vendor'
        },
        // ...scanPageEntry()
    },
    output: {
        filename: '[name].bundle.js',
        path: path.resolve(__dirname, "app", "static"),
        libraryTarget: 'umd',
        umdNamedDefine: true,
        clean: true
    },
    cache: {
        type: 'filesystem',
    },
    plugins: [
        new MiniCssExtractPlugin({ filename: "[name].css" }),
        new webpack.ProvidePlugin({
            $: "jquery",
            jquery: "jQuery",
            "window.jQuery": "jquery",
        }),
        ...(isAnalyze ? [new BundleAnalyzerPlugin()] : []),
    ],
    module: {
        noParse: /jquery|lodash/,
        rules: [
            {
                test: /\.(js|ts|tsx|jsx)$/i,
                exclude: /node_modules/,
                use: [
                    "thread-loader",
                    {
                        loader: "babel-loader",
                        options: { cacheDirectory: true },
                    },
                ],
            },
            {
                test: /\.s[ac]ss$/i,
                use: [stylesHandler, "css-loader", "postcss-loader", "sass-loader"],
            },
            {
                test: /\.css$/i,
                use: [stylesHandler, "css-loader", "postcss-loader"],
            },
            {
                test: /\.svg$/,
                use: ["@svgr/webpack"],
            },
            {
                test: /\.(eot|ttf|woff|woff2|png|jpg|gif)$/i,
                type: "asset",
            },
        ],
    },
    resolve: {
        extensions: [".tsx", ".ts", ".js", ".jsx"],
        alias: {
            "@/*": path.resolve(__dirname, 'src/*'),
        }
    },
    watch: process.argv.includes("--watch"),
    watchOptions: {
        ignored: "**/node_modules/**",
        aggregateTimeout: 300,
        poll: 1000,
    },
    optimization: {
        minimize: true,
        minimizer: [
            new TerserPlugin({
                parallel: true, // Memanfaatkan CPU core lebih banyak
                terserOptions: {
                    keep_classnames: true,
                    keep_fnames: true,
                },
            }),
        ],
    },
};
