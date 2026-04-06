module.exports = (settings) => {

    // Files to bundle
    const projectFiles = {
        // BrowserSync settings
        browserSync: {
            enable: true,
            host: 'localhost',
            port: 3000,
            mode: 'proxy',
            server: {
                baseDir: settings.baseDir
            },
            proxy: settings.siteUrl,
            files: settings.watchFolders,
            reload: true,
            injectCss: true
        },
        // JS configurations for development and production
        projectJs: {
            eslint: false, // enable or disable eslint  | this is only enabled in development env.
            filename: 'js/[name].[contenthash].min.js',
            filename_dev: 'js/[name].min.js',
            entry: {
                footer: settings.projectJsPath + '/footer.js',
                header: settings.projectJsPath + '/header.js',
            },
            rules: {
                test: /\.m?js$/,
            }
        },
        // CSS configurations for development and production
        projectCss: {
            postCss: settings.projectWebpack + '/postcss.config.js',
            stylelint: false, // enable or disable stylelint | this is only enabled in development env.
            filename: 'css/[name].[contenthash].min.css',
            filename_dev: 'css/[name].min.css',
            entry: {
                screen: settings.projectScssPath + '/screen.scss',
                print: settings.projectScssPath + '/print.scss'
            },
            rules: {
                test: /\.s?[ac]ss$/i
            }
        },
        // Source Maps configurations
        projectSourceMaps: {
            // Sourcemaps are nice for debugging but takes lots of time to compile,
            // so we disable this by default and can be enabled when necessary
            enable: settings.enableSourceMaps,
            env: settings.enableSourceMapsFor, // dev | dev-prod | prod
            // ^ Enabled only for development on default, use "prod" to enable only for production
            // or "dev-prod" to enable it for both production and development
            devtool: 'source-map' // type of sourcemap, see more info here: https://webpack.js.org/configuration/devtool/
            // ^ If "source-map" is too slow, then use "cheap-source-map" which struck a good balance between build performance and debuggability.
        },
        // Images configurations for development and production
        projectImages: {
            rules: {
                test: /\.(jpe?g|png|gif|svg)$/i,
            },
        },
        // Fonts configurations for development and production
        projectFonts: {
            rules: {
                test: /\.(woff|woff2|eot|ttf|otf)$/i,
            }
        }
    }

    // Merging the projectFiles & settings objects
    return {
        ...settings,
        ...projectFiles,
        projectConfig: {
            // add extra options here
        }
    }
}