<?php

namespace GivePdfReceipt\FormExtension\FormBuilder\ViewModels;

use GivePdfReceipt\FormExtension\FormBuilder\Actions\GetCustomTemplates;

/**
 * @since 3.1.0
 */
class PdfReceiptsViewModel
{
    /**
     * @since 3.1.0
     */
    public function exports(): array
    {
        $defaultTemplates = give_get_pdf_builder_default_templates();

        foreach ($defaultTemplates as $key => $template) {
            $nonce_value = sanitize_title($template['name']);
            $defaultTemplates[$key]['nonce'] = wp_create_nonce("give_can_edit_template_{$nonce_value}");
        }

        return [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'globalOptionsUrl' => esc_url_raw(admin_url('edit.php?post_type=give_forms&page=give-settings&tab=pdf_receipts')),
            'customPdfPreviewUrl' => esc_url_raw(add_query_arg(array('give_pdf_receipts_action' => 'preview_pdf'),
                admin_url())),
            'setPdfPreviewUrl' => esc_url_raw(add_query_arg(array('give_pdf_receipts_action' => 'preview_set_pdf_template'),
                admin_url())),
            'templatesPdfTags' => $this->getTemplatesPdfTags(),
            'customPdfTags' => $this->getCustomPdfTags(),
            'defaultTemplates' => $defaultTemplates,
            'customTemplates' => (new GetCustomTemplates())(),
            'nonces' => [
                'getCustomTemplates' => wp_create_nonce("give_can_read_custom_templates"),
            ],
        ];
    }

    /**
     * @since 3.1.0
     *
     * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-dependency-extraction-webpack-plugin/#wordpress
     */
    public function getScriptDependencies(): array
    {
        $path = GIVE_PDF_PLUGIN_DIR . 'build/pdfReceiptsFormBuilderExtensionScript.asset.php';

        $scriptAsset = file_exists($path) ? require $path : ['dependencies' => [], 'version' => filemtime($path)];

        return $scriptAsset['dependencies'];
    }

    /**
     * @since 3.1.0
     */
    public function getStylePath(): string
    {
        return GIVE_PDF_PLUGIN_URL . 'build/pdfReceiptsFormBuilderExtensionStyle.css';
    }

    /**
     * @since 3.1.0
     */
    public function getScriptPath(): string
    {
        return GIVE_PDF_PLUGIN_URL . 'build/pdfReceiptsFormBuilderExtensionScript.js';
    }

    /**
     * @since 3.1.0
     */
    private function getTemplatesPdfTags(): array
    {
        $templatesPdfTags = [
            ['tag' => 'page', 'desc' => __('Page Number', 'give-pdf-receipts')],
            ['tag' => 'sitename', 'desc' => __('Site Name', 'give-pdf-receipts')],
            ['tag' => 'today', 'desc' => __('Date of Receipt Generation', 'give-pdf-receipts')],
            ['tag' => 'date', 'desc' => __('Receipt Date', 'give-pdf-receipts')],
            ['tag' => 'receipt_id', 'desc' => __('Receipt ID', 'give-pdf-receipts')],
        ];

        return $templatesPdfTags;
    }

    /**
     * @since 3.1.0
     */
    private function getCustomPdfTags(): array
    {
        $customPdfTags = [];
        $tags = get_supported_pdf_tags(true);
        foreach ($tags as $tag => $desc) {
            $customPdfTags[] = ['tag' => $tag, 'desc' => $desc];
        }

        return $customPdfTags;
    }
}
