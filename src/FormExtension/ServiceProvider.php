<?php

namespace GivePdfReceipt\FormExtension;

use Give\DonationForms\Models\DonationForm;
use Give\Helpers\Hooks;
use Give\ServiceProviders\ServiceProvider as GiveServiceProvider;
use GivePdfReceipt\FormExtension\FormBuilder\Actions\LoadFormExtensionAssets;
use GivePdfReceipt\FormExtension\FormBuilder\Actions\UpdatePdfSettingsMeta;
use GivePdfReceipt\FormExtension\Hooks\UpdateDonationConfirmationPageReceipt;

/**
 * @since 3.0.0
 */
class ServiceProvider implements GiveServiceProvider
{

    /**
     * @since 3.0.0
     */
    public function register()
    {
    }

    /**
     * @since 3.0.0
     */
    public function boot()
    {
        Hooks::addAction('givewp_form_builder_enqueue_scripts', LoadFormExtensionAssets::class, '__invoke', 10, 2);

        Hooks::addFilter('givewp_confirmation_page_receipt_settings_pdfReceiptLink',
            UpdateDonationConfirmationPageReceipt::class,
            '__invoke', 10, 2);

        add_action('givewp_form_builder_updated', static function (DonationForm $form) {
            give(UpdatePdfSettingsMeta::class)->__invoke($form);
        });
    }
}
