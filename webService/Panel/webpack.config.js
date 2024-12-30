const glob = require('glob');
const path = require("path");

const servicesEntry = () => {
    const root = path.resolve(__dirname, "library")
    return glob.sync(root + "/**/main.{js,ts,[jt]sx}", {
        ignore: ['**/node_modules/**', '**/dist/**', '**/.git/**', '**/.vscode/**', '**/.vscode-test/**', '**/vendor/**', '**/tests/**'],
    }).reduce((acc, file) => {
        const libName = path.basename(path.dirname(file))
        const fullPath = path.resolve(file)
        acc[`library/${libName}`] = {
            import: fullPath,
            dependOn: "main",
            filename: `library/${libName}.js`
        }
        return acc;
    }, {})
}


module.exports =
{
    // devtool: "source-map",
    entry: {
        vendor: ["react", "react-dom"],
        main: {
            import: "./main",
            dependOn: "vendor",
            filename: "[name].bundle.js"
        },
        ...servicesEntry(),
    },
    output: {
        filename: "[name].bundle.js",
        path: path.resolve(__dirname, "assets"),
        library: ['Panel', '[name]'],
        libraryTarget: 'umd',
        umdNamedDefine: true,
    }
}
