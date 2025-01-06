const path = require("path");
const { glob } = require("glob");
const webpack = require("webpack");
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const { BundleAnalyzerPlugin } = require("webpack-bundle-analyzer");
const TerserPlugin = require("terser-webpack-plugin");
const stylesHandler = MiniCssExtractPlugin.loader;

// Function to scan for other webpack config files
const scanAnotherConfig = async () => {
    const pattern = "./**/webpack.config.js"; // Search for all webpack.config.js files
    try {
        const files = glob.sync(pattern, {
            ignore: [
                "node_modules/**", // Ignore node_modules
                "dist/**",          // Ignore dist folder
            ],
        });
        return files.map(file => {
            if (file.match(/(node_modules\/|vendor\/)/g)) {
                return false;
            }
            return path.resolve(__dirname, file); // Add return statement
        }).filter(Boolean);  // Remove false values from the array
    } catch (error) {
        console.error("Error scanning configuration files:", error);
        return [];
    }
};

const isAnalyze = process.argv.includes("--analyze");
const isDevelopment = process.env.NODE_ENV === "development";

const implementDefault = (config, file) => {
    config.mode = isDevelopment ? "development" : "production";
    if (!config.context) {
        config.context = path.resolve(path.dirname(file), "src");
    }
    config.plugins = [
        new MiniCssExtractPlugin({
            filename: '[name].css',
        }),
        new webpack.ProvidePlugin({
            $: "jquery",
            jquery: "jQuery",
            "window.jQuery": "jquery"
        }),
        ...(isAnalyze ? [new BundleAnalyzerPlugin()] : []),
        ...(config.plugins || []),
    ];
    config.module = {
        rules: [
            {
                test: /\.(js|ts|tsx|jsx)$/i,
                exclude: /node_modules/,
                use: "babel-loader",
            },
            {
                test: /\.s[ac]ss$/i,
                use: [stylesHandler, 'css-loader', 'postcss-loader', 'sass-loader'],
            },
            {
                test: /\.css$/i,
                use: [stylesHandler, 'css-loader', 'postcss-loader'],
            },
            {
                test: /\.svg$/,
                use: ['@svgr/webpack'],
            },
            {
                test: /\.(eot|ttf|woff|woff2|png|jpg|gif)$/i,
                type: 'asset',
            },
            ...(config.module?.rules || []),
        ],
    };
    config.resolve = {
        extensions: ['.tsx', '.ts', '.js', '.jsx'],
        ...(config.resolve || {}),
    };
    config.watch = process.argv.includes("--watch");
    config.watchOptions = {
        ignored: "**/node_modules/**",
        aggregateTimeout: 300,
        poll: 1000,
    };
    config.optimization = {
        minimize: true,
        minimizer: [new TerserPlugin({
            terserOptions: {
                keep_classnames: true,
                keep_fnames: true
            }
        })],
    };
    config.externals = {
        ...config.externals,
    };
    return config;
};

// Function to load and aggregate configurations
const loadConfigs = async () => {
    const configList = [];
    const fileConfig = await scanAnotherConfig();

    fileConfig.forEach(file => {
        try {
            if (!file) return;
            console.log(`Loading configuration: "${file}"`);
            const config = require(file); // Dynamically require the config file
            if (!config) {
                console.error(`Configuration file "${file}" did not export a valid configuration.`);
                return;
            }
            if (Array.isArray(config)) {
                config.forEach(cfg => {
                    implementDefault(cfg, file);
                    configList.push(cfg);
                });
            } else {
                implementDefault(config, file);
                configList.push(config);
            }

        } catch (error) {
            console.error(`Failed to load configuration from "${file}":`, error.stack || error.message);
        }
    });

    return configList;
};

// Main function to bundle configurations with Webpack
(async () => {
    try {
        const configs = await loadConfigs();
        if (configs.length === 0) {
            console.log("No configurations found.");
            return;
        }

        // Run Webpack with the loaded configurations
        webpack(configs, (err, stats) => {
            if (err) {
                console.error("Webpack encountered a fatal error:", err.stack || err);
                if (err.details) {
                    console.error("Error details:", err.details);
                }
                return;
            }

            const info = stats.toJson();

            if (stats.hasErrors()) {
                console.error("Webpack errors:", info.errors);
            }

            console.log("Webpack build completed:");
            console.log(stats.toString({ colors: true }));
        });
    } catch (error) {
        console.error("Unexpected error:", error.message);
    }
})();
