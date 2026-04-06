/**
 * This is a main entrypoint for Webpack config.
 *
 * @since 1.0.0
 */

 const path = require('path');

 const settings = {
 
     //Project Paths
     projectDir: __dirname, // Current project directory absolute path.
     projectJsPath: path.resolve(__dirname, 'assets/src/js'),
     projectScssPath: path.resolve(__dirname, 'assets/src/scss'),
     projectFontsPath: path.resolve(__dirname, 'assets/src/fonts'),
     projectOutput: path.resolve(__dirname, 'assets/public'),
     projectWebpack: path.resolve(__dirname, 'webpack'),
 
     //Browser Sync Settings
     siteUrl: 'http://local.wpmailblox.com:8888/',
     baseDir: ['public'],
     watchFolders: ['**/**/**.php', '**/**/**.css', '**/**/**.js'],
 
     //Source Maps
     enableSourceMaps: true,
     enableSourceMapsFor: 'dev'
     // ^ Enabled only for development on default, use "prod" to enable only for production
     // or "dev-prod" to enable it for both production and development
 
 };
 
 const projectOptions = require('./webpack/projectOptions.config')(settings);
 
 // Get the development or production setup based
 // on the script from package.json
 module.exports = env => {
     if (env.NODE_ENV === 'production') {
         return require('./webpack/config.production')(projectOptions);
     } else {
         return require('./webpack/config.development')(projectOptions);
     }
 };