<?php

namespace GivePdfReceipt\FormExtension\FormBuilder\Actions;

use Give\DonationForms\Models\DonationForm;
use GivePdfReceipt\FormExtension\FormBuilder\DataTransferObjects\FormBuilderPdfSettings;
use GivePdfReceipt\Helpers\Admin\Settings;

/**
 * @since 3.1.0
 */
class UpdatePdfSettingsMeta
{
    /**
     * @since 3.1.0
     */
    public function __invoke(DonationForm $form)
    {
        $pdfSettings = FormBuilderPdfSettings::fromArray($form->settings->pdfSettings);

        give()->form_meta->update_meta($form->id, "give_pdf_receipts_enable_disable", $pdfSettings->enable);
        give()->form_meta->update_meta($form->id, "give_pdf_generation_method", $pdfSettings->generationMethod);
        give()->form_meta->update_meta($form->id, "give_pdf_colorpicker", $pdfSettings->colorPicker);
        give()->form_meta->update_meta($form->id, "give_pdf_templates", $pdfSettings->templateId);
        give()->form_meta->update_meta($form->id, "give_pdf_logo_upload", $pdfSettings->logoUpload);
        give()->form_meta->update_meta($form->id, "give_pdf_company_name", $pdfSettings->companyName);
        give()->form_meta->update_meta($form->id, "give_pdf_name", $pdfSettings->name);
        give()->form_meta->update_meta($form->id, "give_pdf_address_line1", $pdfSettings->addressLine1);
        give()->form_meta->update_meta($form->id, "give_pdf_address_line2", $pdfSettings->addressLine2);
        give()->form_meta->update_meta($form->id, "give_pdf_address_city_state_zip", $pdfSettings->cityStateZip);
        give()->form_meta->update_meta($form->id, "give_pdf_url", $pdfSettings->displayWebsiteUrl);
        give()->form_meta->update_meta($form->id, "give_pdf_email_address", $pdfSettings->emailAddress);
        give()->form_meta->update_meta($form->id, "give_pdf_header_message", $pdfSettings->headerMessage);
        give()->form_meta->update_meta($form->id, "give_pdf_footer_message", $pdfSettings->footerMessage);
        give()->form_meta->update_meta($form->id, "give_pdf_additional_notes", $pdfSettings->additionalNotes);
        give()->form_meta->update_meta($form->id, "give_pdf_receipt_template", $pdfSettings->customTemplateId);
        give()->form_meta->update_meta($form->id, "give_pdf_receipt_template_name", $pdfSettings->customTemplateName);
        give()->form_meta->update_meta($form->id, "give_pdf_builder_page_size", $pdfSettings->customPageSize);
        give()->form_meta->update_meta($form->id, "give_pdf_builder", $pdfSettings->customPdfBuilder);
        give()->form_meta->update_meta($form->id, "give_pdf_enable_char_support", "");
        give()->form_meta->update_meta($form->id, "give_pdf_builder_special_chars", "disabled");


        if ( ! empty($pdfSettings->customPdfBuilder)) {
            if (empty($pdfSettings->customTemplateName)) {
                $pdfSettings->customTemplateName = __('My Template', 'give-pdf-receipts') . ' ' . time();
            }

            $customTemplateId = Settings::SaveCustomTemplate($form->id, $pdfSettings->customTemplateId,
                $pdfSettings->customTemplateName,
                $pdfSettings->customPdfBuilder);

            $pdfSettings->customTemplateId = $customTemplateId;

            $form->settings->pdfSettings = $pdfSettings->toArray();
            $form->save();
        }
    }
}
