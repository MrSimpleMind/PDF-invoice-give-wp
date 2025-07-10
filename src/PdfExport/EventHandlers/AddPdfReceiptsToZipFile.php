<?php

namespace GivePdfReceipt\PdfExport\EventHandlers;

use Give\Donations\Models\Donation;
use GivePdfReceipt\PdfExport\Repositories\PdfExport;
use GivePdfReceipt\PdfExport\Utilities\ZipFile;

/**
 * @since 3.2.2 Add renewal donations to the zip file
 * @since 2.4.0
 */
class AddPdfReceiptsToZipFile
{

    /**
     * @since 2.4.0
     */
    public function Add(
        string $exportId,
        string $groupID,
        string $tempExportDir,
        string $zipFilePath,
        int $currentChunk,
        int $totalChunks,
        array $donationIds
    )
    {
        $pdfExport = give(PdfExport::class);

        if ( ! $pdfExport->fileSystem->exists($zipFilePath)) {
            return;
        }

        $pdfCount = 0;
        foreach ($donationIds as $donationId) {
            $donation = Donation::find($donationId);

            if (!$donation || (!$donation->status->isComplete() && !$donation->status->isRenewal())) {
                continue;
            }

            $pdfFilePath = give_pdf_receipts()->engine->generate_pdf_receipt($donationId, $tempExportDir);
            if (ZipFile::addNewFile($pdfFilePath, $zipFilePath)) {
                $pdfExport->fileSystem->delete($pdfFilePath);
                $pdfCount++;
            }
        }

        ZipFile::addLogMessage($zipFilePath,
            '[CHUNK ' . $currentChunk . '/' . $totalChunks . ']' . ' finished, which added  ' . $pdfCount . ' pdf files to the ' . $exportId . '.preparing.zip archive.',
            false);

        if ($pdfExport->isExportFinished($groupID, $totalChunks)) {
            ZipFile::addLogMessage($zipFilePath,
                'All ' . $totalChunks . ' jobs are complete. The ' . $exportId . '.zip archive is ready for download.',
                false, true);
            $pdfExport->makeExportReadyForDownload($zipFilePath, $tempExportDir);
        }
    }
}
