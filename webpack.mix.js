const mix = require('laravel-mix');
const wpPot = require('wp-pot');

mix.setPublicPath('public')
    .ts('src/PdfExport/resources/js/admin/app/index.tsx', 'public/js/give-receipts-pdf-export-app.js')
    .ts(
        'src/PdfExport/resources/js/admin/donation-list/index.tsx',
        'public/js/give-receipts-pdf-export-donation-list.js'
    )
    .copyDirectory('src/PdfExport/resources/js/admin/app/Assets/', 'public/assets');

mix.webpackConfig({
    externals: {
        $: 'jQuery',
        jquery: 'jQuery',
    },
});

mix.options({
    // Don't perform any css url rewriting by default
    processCssUrls: false,

    // Prevent LICENSE files from showing up in JS builds
    terser: {
        extractComments: (astNode, comment) => false,
        terserOptions: {
            format: {
                comments: false,
            },
        },
    },
});

if (mix.inProduction()) {
    wpPot({
        package: 'Give - PDF Receipts',
        domain: 'give-pdf-receipts',
        destFile: 'languages/give-pdf-receipts.pot',
        relativeTo: './',
        bugReport: 'https://github.com/impress-org/give-pdf-receipts/issues/new/choose',
        team: 'GiveWP <info@givewp.com>',
    });
}
