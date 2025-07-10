<?php

namespace GivePdfReceipt\PdfExport;

use Give\Helpers\Hooks;
use Give\ServiceProviders\ServiceProvider as GiveServiceProvider;
use GivePdfReceipt\PdfExport\API\CreateExport;
use GivePdfReceipt\PdfExport\API\DeleteExport;
use GivePdfReceipt\PdfExport\API\ListExports;
use GivePdfReceipt\PdfExport\EventHandlers\AddPdfReceiptsToZipFile;
use GivePdfReceipt\PdfExport\Repositories\PdfExport;

/**
 * @since 2.4.0
 */
class ServiceProvider implements GiveServiceProvider
{

    /**
     * @since 2.4.0
     */
    public function register()
    {
        give()->singleton(PdfExport::class, static function () {
            $chunkSize = (defined('GIVE_PDF_EXPORT_CHUNK_SIZE') && GIVE_PDF_EXPORT_CHUNK_SIZE)
                ? GIVE_PDF_EXPORT_CHUNK_SIZE
                : 30;

            return new PdfExport($chunkSize);
        });
    }

    /**
     * @since 2.4.0
     */
    public function boot()
    {
        Hooks::addFilter('give-tools_get_settings_pages', App::class, 'registerPdfExportPage', 999, 1);
        Hooks::addFilter('give-tools_tabs_array', App::class, 'addPdfExportPageOnProperPosition', 999, 1);
        Hooks::addFilter('give_payments_table_bulk_actions', App::class, 'addBulkActions');
        Hooks::addAction('init', App::class, 'processBulkActions', 999);
        Hooks::addAction('admin_enqueue_scripts', App::class, 'loadAssets');

        Hooks::addAction('rest_api_init', self::class, 'registerRoutes');
        Hooks::addAction('givewp_pdf_export', AddPdfReceiptsToZipFile::class, 'add', 10, 7);
        Hooks::addAction('admin_init', App::class, 'enableZipExtensionNotice');
    }

    /**
     * @since 2.4.0
     */
    public function registerRoutes()
    {
        $routes = [
            CreateExport::class,
            DeleteExport::class,
            ListExports::class,
        ];
        foreach ($routes as $route) {
            $route = give()->make($route);
            $route->registerRoute();
        }
    }
}
