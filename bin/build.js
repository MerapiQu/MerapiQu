const fs = require('fs');
const path = require('path');
const archiver = require('archiver');
const ignore = require('ignore');

// Function to read and parse .gitignore
function getGitignorePatterns(gitignorePath) {
    const ig = ignore();
    const gitignoreContent = fs.readFileSync(gitignorePath, 'utf-8');
    // Split the content by lines and add non-empty, non-comment lines to the ignore patterns
    const patterns = gitignoreContent
        .split('\n')
        .map(line => line.trim()) // Remove whitespace from start and end
        .filter(line => line && !line.startsWith('#')); // Remove empty lines and comments

    ig.add(patterns);
    return ig;
}

// Function to handle files that start with `include/Module` or `include\\Module`
function scanModuleComponent(filePath, ig, filesArray, baseDir) {

    const relativePath = path.relative(baseDir, filePath);
    const moduleRootPath = path.relative(path.join(baseDir, "include", "Module"), filePath);
    const moduleName = moduleRootPath.replace(/(\\|\/).*$/i, '');
    const moduleRelativePath = moduleRootPath.replace(moduleName + '\\', '');
    const stats = fs.statSync(filePath);
    ig.add(['node_modules/', '.git', '.gitignore', '.active', '.git/', 'Assets/src']);

    // Check if the file should be excluded based on .gitignore or included based on custom rules
    if (ig.ignores(moduleRelativePath)) {
        return;
    }

    if (stats.isDirectory()) {
        // Recursively scan subdirectories
        scanDirectory(filePath, ig, filesArray, baseDir);
    } else {
        // Save the relative path for maintaining the folder structure in the ZIP
        filesArray.push(relativePath);
    }
}

// Function to scan a directory, check against exclusions from .gitignore and custom rules, and collect files
function scanDirectory(dirPath, ig, filesArray, baseDir) {
    // Set baseDir to dirPath if not already set
    baseDir = baseDir || dirPath;

    const files = fs.readdirSync(dirPath);

    files.forEach(file => {
        const filePath = path.join(dirPath, file);
        const relativePath = path.relative(baseDir, filePath);
        const stats = fs.statSync(filePath);

        // Always include the 'vendor/' folder
        if (relativePath.startsWith('vendor') || relativePath.startsWith('.htaccess')) {
            filesArray.push(relativePath);
        }

        // Handle paths starting with `include/Module/` or `include\\Module\\`
        if (relativePath.startsWith('include/Module/') || relativePath.startsWith('include\\Module\\')) {
            scanModuleComponent(filePath, ig, filesArray, baseDir);
            return;
        }

        // Check if the file should be excluded based on .gitignore or included based on custom rules
        if (ig.ignores(relativePath)) {
            return;
        }

        if (stats.isDirectory()) {
            // Recursively scan subdirectories
            scanDirectory(filePath, ig, filesArray, baseDir);
        } else {
            // Save the relative path for maintaining the folder structure in the ZIP
            filesArray.push(relativePath);
        }
    });
}

// Function to create a ZIP archive with progress on a single line
function createZip(filesArray, baseDir, outputZipPath) {
    const output = fs.createWriteStream(outputZipPath);
    const archive = archiver('zip', {
        zlib: { level: 9 } // Set the compression level
    });

    output.on('close', function () {
        console.log(`\nZIP archive created successfully. Total bytes: ${archive.pointer()}`);
    });

    archive.on('error', function (err) {
        throw err;
    });

    archive.on('progress', function (progress) {
        const percent = (progress.fs.processedBytes / progress.fs.totalBytes) * 100;
        process.stdout.write(`\rProgress: ${percent.toFixed(2)}% (${progress.fs.processedBytes} of ${progress.fs.totalBytes} bytes)`);
    });

    archive.pipe(output);

    filesArray.forEach(relativePath => {
        const fullPath = path.join(baseDir, relativePath);
        archive.file(fullPath, { name: relativePath });
    });

    archive.finalize();
}

// Usage
const gitignorePath = path.join('F:/www/MerapiPanel', '.gitignore');
const ig = getGitignorePatterns(gitignorePath); // Get the .gitignore patterns
ig.add([
    "node_modules/",
    'bin',
    'content/',
    'schema',
    '.*',
    'babel.config.json',
    'webpack.config.json',
    'webpack.config.js',
    'composer.json',
    'composer.lock',
    'package.json',
    'package-lock.json',
    'env.php'
]);

const filesArray = [];
const directoryToScan = 'F:/www/MerapiPanel';
const outputZipPath = 'F:/www/MerapiPanel/build.zip';

scanDirectory(directoryToScan, ig, filesArray);
createZip(filesArray, directoryToScan, outputZipPath);
