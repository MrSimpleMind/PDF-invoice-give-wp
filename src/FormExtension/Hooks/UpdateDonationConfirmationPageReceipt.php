<?php

namespace GivePdfReceipt\FormExtension\Hooks;

use Give\Framework\Receipts\DonationReceipt;

/**
 * @since 3.0.0
 */
class UpdateDonationConfirmationPageReceipt
{
    /**
     * @since 3.0.0
     */
    public function __invoke(string $pdfReceiptLink, DonationReceipt $receipt)
    {
        $pdfReceiptLink = sprintf(
            '<a id="give-pdf-receipt-link" title="%3$s" href="%1$s">%2$s</a>',
            give_pdf_receipts()->engine->get_pdf_receipt_url($receipt->donation->id),
            give_pdf_receipts_download_pdf_text(false),
            give_pdf_receipts_download_pdf_text(false)
        );

        return $pdfReceiptLink;
    }
}
