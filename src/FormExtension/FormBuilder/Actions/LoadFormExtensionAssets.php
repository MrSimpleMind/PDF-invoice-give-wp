<?php

namespace GivePdfReceipt\FormExtension\FormBuilder\Actions;

use GivePdfReceipt\FormExtension\FormBuilder\ViewModels\PdfReceiptsViewModel;

/**
 * @since 3.1.0
 */
class LoadFormExtensionAssets
{
    /**
     * @since 3.1.0
     */
    public function __invoke()
    {
        wp_enqueue_editor();

        $viewModel = new PdfReceiptsViewModel();

        wp_enqueue_style(
            'givewp-form-extension-pdf-receipts-style',
            $viewModel->getStylePath()
        );

        wp_enqueue_script(
            'givewp-form-extension-pdf-receipts-script',
            $viewModel->getScriptPath(),
            $viewModel->getScriptDependencies(),
            false,
            true
        );

        wp_localize_script('givewp-form-extension-pdf-receipts-script', 'pdfReceiptsFormBuilder',
            $viewModel->exports());
    }
}
