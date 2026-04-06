# Webpack Starter for Wordpress Websites

If there was a previous webpack installation in your theme it needs deleting from the git index and from the file system.

Make sure you keep a backup in case there were theme specific settings. 

## Cloning the webpack git module

Navigate inside your theme folder and run the following. 

```
git rm --cached -r webpack
rm -rf webpack 
git clone git@bitbucket.org:core-snippets/webpack.git webpack
```

## Update .gitignore

Don't forget to add the webpack folder into the .gitignore

## Add/Update webpack.config.js

If your project was previousy set up with webpack the webpack.config.js file should be present in the theme folder. If not, please add it and update its contents in any case with the following:


```
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
     siteUrl: '',
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
```