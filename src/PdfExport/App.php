<?php

namespace GivePdfReceipt\PdfExport;

use GivePdfReceipt\PdfExport\Repositories\PdfExport;

/**
 * @since 2.4.0
 */
class App
{
    /**
     * @since 2.4.0
     */
    public function registerPdfExportPage(array $pages): array
    {
        $pdfExportPage = [new GiveToolsPdfExportPage()];
        array_splice($pages, 1, 0, $pdfExportPage);

        return $pages;
    }

    /**
     * @since 2.4.0
     */
    public function addPdfExportPageOnProperPosition(array $pages): array
    {
        if (isset($pages['pdf-export'])) {
            $pdfTab = ['pdf-export' => $pages['pdf-export']];
            unset($pages['pdf-export']);
            $pages = array_slice($pages, 0, 1) + $pdfTab + array_slice($pages, 1);
        }

        return $pages;
    }

    /**
     * @since 2.4.0
     */
    public function addBulkActions(array $actions)
    {
        $actions += ['download-receipt' => __('Download Receipts', 'give-pdf-receipts')];

        return $actions;
    }

    /**
     * @since 2.4.0
     */
    public function processBulkActions()
    {
        $currentAction = $_GET['action'] ?? false;

        if (
            ! is_admin() ||
            empty($currentAction) ||
            'download-receipt' !== $currentAction ||
            ! current_user_can('export')
        ) {
            return;
        }

        $donationIds = $_GET['payment'] ?? false;

        if (is_array($donationIds) && ! empty($donationIds)) {
            give(PdfExport::class)->createExportByDonationIds($donationIds);
            $url = admin_url('edit.php?post_type=give_forms&page=give-tools&tab=pdf-export');
            wp_redirect($url);
            exit();
        }
    }

    /**
     * @since 2.4.0
     */
    public function enableZipExtensionNotice()
    {
        if ( ! $this->isPdfExportPage()) {
            return;
        }

        if ($this->isZipEnabled()) {
            return;
        }

        $notice = [
            'id' => 'give-pdf-receipts-enable-zip-extension',
            'type' => 'error',
            'dismissible' => false,
            'description' => __('Your web server does not have the ZipArchive PHP class enabled. This class is required to generate ZIPs of PDF Receipts. Contact your web host to enable the ZipArchive class.',
                'give-pdf-receipts'),
        ];

        Give()->notices->register_notice($notice);
    }

    /**
     * @since 2.4.0
     */
    public function loadAssets()
    {
        if ( ! $this->isPdfExportPage() && ! $this->isDonationListPage()) {
            return;
        }

        $baseUrl = rest_url('give-api/v2/pdf-export');

        $givePdfReceiptsExportToolObject = [
            'zipEnabled' => $this->isZipEnabled(),
            'apiRoot' => esc_url_raw($baseUrl),
            'apiEndpoints' => [
                'create' => esc_url_raw(trailingslashit($baseUrl) . 'create'),
                'delete' => esc_url_raw(trailingslashit($baseUrl) . 'delete'),
                'list' => esc_url_raw(trailingslashit($baseUrl) . 'list'),
            ],
            'apiNonce' => wp_create_nonce('wp_rest'),
            'exportsList' => json_encode(give(PdfExport::class)->getExportsList()),
            'locale' => str_replace('_', '-', get_locale()),
            'assetsUrl' => GIVE_PDF_PLUGIN_URL . 'public/assets/',
            'adminUrl' => esc_url_raw(admin_url('edit.php?post_type=give_forms&page=give-tools&tab=pdf-export')),
        ];

        if ($this->isDonationListPage()) {
            $path = GIVE_PDF_PLUGIN_URL . 'public/js/give-receipts-pdf-export-donation-list.js';
            wp_enqueue_script('give-receipts-pdf-export-donation-list', $path, [], GIVE_PDF_PLUGIN_VERSION, true);

            wp_localize_script(
                'give-receipts-pdf-export-donation-list',
                'GivePdfReceiptsExportTool',
                $givePdfReceiptsExportToolObject
            );
        }

        if ($this->isPdfExportPage()) {
            wp_enqueue_style('givewp-design-system-foundation');

            $path = GIVE_PDF_PLUGIN_URL . 'public/js/give-receipts-pdf-export-app.js';
            wp_enqueue_script('give-receipts-pdf-export-app', $path, [], GIVE_PDF_PLUGIN_VERSION, true);

            wp_localize_script(
                'give-receipts-pdf-export-app',
                'GivePdfReceiptsExportTool',
                $givePdfReceiptsExportToolObject
            );
        }
    }

    /**
     * @since 2.4.0
     */
    private function isPdfExportPage(): bool
    {
        return is_admin() && isset($_GET['tab']) && 'pdf-export' === $_GET['tab'];
    }

    /**
     * @since 2.4.0
     */
    private function isDonationListPage(): bool
    {
        return is_admin() && isset($_GET['page']) && 'give-payment-history' === $_GET['page'];
    }

    /**
     * @since 2.4.0
     */
    private function isZipEnabled(): bool
    {
        return class_exists('ZipArchive');
    }
}
