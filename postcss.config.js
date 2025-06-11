const purgecss = require('@fullhuman/postcss-purgecss')({
    content: [
        './*.php',
        './template-parts/*.php',
        './inc/*.php',
        './inc/**/*.php',
        './assets/js/*.js',
        './assets/js/**/*.js'
    ],
    safelist: {
      standard: [
        /^is-/,
        /^has-/,
        /^wp-/,
        /^woocommerce/,
        /^screen-reader-text/
      ],
      deep: [
        /^dropdown/,
        /^modal/,
        /^navbar/
      ]
    },
    blocklist: [/^\.fa-/]
});

module.exports = {
    plugins: [
        purgecss
    ]
};