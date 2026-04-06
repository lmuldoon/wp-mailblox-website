/**
 * PostCSS configuration file
 * as configured under cssRules.use['postcss-loader'] in development.js and production.js
 *
 * @docs https://postcss.org/
 * @since 1.0.0
 */

module.exports = (projectOptions) => {
    const postcssOptions = {};
    if (process.env.NODE_ENV === 'production') {
        Object.assign(postcssOptions, {
            plugins: [
                require('tailwindcss')({
                    config: './webpack/tailwind.config.js'
                }),
                require('autoprefixer')(),
            ]
        })
    } else {
        Object.assign(postcssOptions, {
            plugins: [
                require('tailwindcss')({
                    config: './webpack/tailwind.config.js'
                }),
                require('autoprefixer')(),
                require('postcss-watch-folder')({
                    folder: projectOptions.projectScssPath,
                    main: projectOptions.projectCss.entry.screen
                }),
            ]
        })
    }

    return {
        postcssOptions: postcssOptions
    }
}