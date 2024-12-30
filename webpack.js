const path = require("path");
const { glob } = require("glob"); // Use the native Promise-based glob
const webpack = require("webpack");
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const { BundleAnalyzerPlugin } = require("webpack-bundle-analyzer");
const stylesHandler = MiniCssExtractPlugin.loader;

// Function to scan for other webpack config files
const scanAnotherConfig = async () => {
    const pattern = "./**/webpack.config.js"; // Search for all webpack.config.js files
    try {
        const files = await glob(pattern, {
            ignore: [
                "**/node_modules/**", // Ignore node_modules
                "**/dist/**",         // Ignore dist folder
                "./*",                // Ignore current folder
            ],
        });
        return files.map(file => path.resolve(__dirname, file));
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
        new MiniCssExtractPlugin(),
        ...(isAnalyze ? [new BundleAnalyzerPlugin()] : []),
        ...(config.plugins || []),
    ]
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
                test: /\.(eot|svg|ttf|woff|woff2|png|jpg|gif)$/i,
                type: 'asset',
            },
            ...(config.module?.rules || []),
        ]
    }
    config.resolve = {
        extensions: ['.tsx', '.ts', '.js', '.jsx'],
        ...(config.resolve || {}),
    }
    config.watch = process.argv.includes("--watch");
    config.watchOptions = {
        ignored: "**/node_modules/**",
        aggregateTimeout: 300,
        poll: 1000,
    };
    return config;
}

// Function to load and aggregate configurations
const loadConfigs = async () => {
    const configList = [];
    const fileConfig = await scanAnotherConfig();

    fileConfig.forEach(file => {
        try {
            console.log(`Loading configuration: "${file}"`);
            const config = require(file); // Dynamically require the config file
            if (Array.isArray(config)) {
                config.forEach(cfg => {
                    implementDefault(cfg, file);
                    configList.push(cfg);
                })
            } else {
                implementDefault(config, file);
                configList.push(config);
            }

        } catch (error) {
            console.error(`Failed to load configuration from "${file}":`, error.message);
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

            if (stats.hasWarnings()) {
                console.warn("Webpack warnings:", info.warnings);
            }

            console.log("Webpack build completed:");
            console.log(stats.toString({ colors: true }));
        });
    } catch (error) {
        console.error("Unexpected error:", error.message);
    }
})();
