/**
 * Webpack configurations for the production environment
 * based on the script from package.json
 * Run with: "npm run prod" or or "npm run prod:watch"
 *
 * @since 1.0.0
 */
const glob = require('glob-all');
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const TerserPlugin = require("terser-webpack-plugin");


module.exports = (projectOptions) => {

    process.env.NODE_ENV = 'production'; // Set environment level to 'production'

    /**
     * The base skeleton
     */
    const Base = require('./config.base')(projectOptions);

    /**
     * CSS rules
     */
    const cssRules = {
        ...Base.cssRules,
        ...{
            // add CSS rules for production here
        }
    };

    /**
     * Vue rules
     */
     const vueRules = {
        ...Base.vueRules,
        ...{
            // add CSS rules for development here
        }
    };

    /**
     * JS rules
     */
    const jsRules = {
        ...Base.jsRules,
        ...{
            // add JS rules for production here
        }
    };

    /**
     * Image rules
     */
    const imageRules = {
        ...Base.imageRules,
        ...{
            // add image rules for production here
        }
    }

    /**
     * Fonts rules
     */
    const fontRules = {
        ...Base.fontRules,
        ...{
            // add image rules for production here
        }
    }

    /**
     * Optimizations rules
     */
    const optimizations = {
        ...Base.optimizations,
        ...{
            minimize: true,
            minimizer: [
                new CssMinimizerPlugin({
                    minimizerOptions: {
                        preset: [
                            "default",
                            {
                                discardComments: {
                                    removeAll: true
                                },
                            },
                        ],
                    },
                }),
                new TerserPlugin({
                    extractComments: false,
                    terserOptions: {
                        output: {
                            comments: false
                        },
                        mangle: {
                            // Find work around for Safari 10+
                            safari10: true,
                        },
                    },

                })
            ],
        }
    }

    /**
     * Plugins
     */
    const plugins = [
        ...Base.plugins, ...[
            
        ]
    ]

    /**
     * Add sourcemap for production if enabled
     */
    const sourceMap = {
        devtool: false,
    };
    if (projectOptions.projectSourceMaps.enable === true && (
            projectOptions.projectSourceMaps.env === 'prod' || projectOptions.projectSourceMaps.env === 'dev-prod'
        )) {
        sourceMap.devtool = projectOptions.projectSourceMaps.devtool;
    }

    /**
     * The configuration that's being returned to Webpack
     */
    return {
        mode: 'production',
        entry: {
            ...projectOptions.projectJs.entry,
            ...projectOptions.projectCss.entry
        }, // Define the starting point of the application.
        output: {
            path: projectOptions.projectOutput,
            filename: projectOptions.projectJs.filename,
        },
        devtool: sourceMap.devtool,
        optimization: optimizations,
        module: {
            rules: [cssRules, jsRules, imageRules, fontRules, vueRules],
        },
        resolve: {
            alias: {
                vue: "vue/dist/vue.esm-bundler.js"
            },
        },
        plugins: plugins,
        externals: {
            jquery: 'jQuery',
        },
    }
}