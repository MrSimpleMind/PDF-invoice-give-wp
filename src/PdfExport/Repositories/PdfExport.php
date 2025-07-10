<?php

namespace GivePdfReceipt\PdfExport\Repositories;

use ActionScheduler_Store;
use DateTime;
use DateTimeInterface;
use GivePdfReceipt\PdfExport\Actions\GetFirstDonationIdBetweenTwoDates;
use GivePdfReceipt\PdfExport\Actions\GetLastDonationIdBetweenTwoDates;
use GivePdfReceipt\PdfExport\Actions\GetTotalDonationsBetweenTwoDates;
use GivePdfReceipt\PdfExport\Utilities\ZipFile;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use WP_Filesystem_Direct;

/**
 * @since 2.4.0
 */
class PdfExport
{
    /**
     * @var string
     */
    private $exportsDir;

    /**
     * @var string
     */
    private $exportsUrl;

    /**
     * @var string
     */
    private $tempDir;

    /**
     * @var int
     */
    private $chunkSize;

    /**
     * @var WP_Filesystem_Direct
     */
    public $fileSystem;

    /**
     * @since 2.4.0
     */
    public function __construct(int $chunkSize)
    {
        $this->chunkSize = $chunkSize;
        $upload = wp_get_upload_dir();
        $dir = $upload['basedir'];
        $url = $upload['baseurl'];
        $this->tempDir = trailingslashit($dir) . 'give/pdf-receipts/temp/';
        $this->exportsDir = trailingslashit($dir) . 'give/pdf-receipts/exports/';
        $this->exportsUrl = trailingslashit($url) . 'give/pdf-receipts/exports/';
        $this->fileSystem = new WP_Filesystem_Direct([]);
    }

    /**
     * @since 2.4.0
     */
    public function getExportsList(): array
    {
        $exports = [];

        if ( ! $this->fileSystem->is_dir($this->exportsDir)) {
            return $exports;
        }

        $dateTimeFormat = get_option('date_format') . ' ' . get_option('time_format');

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->exportsDir)) as $file) {
            if ($file->isDir() || 'zip' !== $file->getExtension()) {
                continue;
            }

            $createdAt = $this->getUniqueCreatedAtValue($file->getCtime(), $exports);
            $fileName = $file->getFilename();
            $preparing = false;

            if (strpos($fileName, '.preparing') !== false) {
                $preparing = true;
                $fileName = $this->removePreparingFromFileName($fileName);
            }

            $exports[$createdAt] = [
                'id' => str_replace('.zip', '', $fileName),
                'preparing' => $preparing,
                'created_at' => date_i18n($dateTimeFormat, $createdAt),
                'file_name' => $fileName,
                'file_url' => $this->exportsUrl . $fileName,
            ];
        }

        return $exports;
    }

    /**
     * @since 2.4.0
     */
    public function getUniqueCreatedAtValue(int $createdAt, array $exports): int
    {
        if ( ! array_key_exists($createdAt, $exports)) {
            return $createdAt;
        }

        $tempDate = new DateTime();
        while (array_key_exists($createdAt, $exports)) {
            $tempDate->setTimestamp($createdAt);
            $tempDate->modify('+1 second');
            $createdAt = $tempDate->getTimestamp();
        }

        return $createdAt;
    }

    /**
     * @since 2.4.0
     */
    public function deleteExport(string $exportId): bool
    {
        $zipFilePath = $this->exportsDir . $exportId . '.zip';

        if ( ! $this->fileSystem->exists($zipFilePath)) {
            return false;
        }

        $this->fileSystem->delete($zipFilePath);

        return true;
    }

    /**
     * @since 2.4.0
     */
    public function createExportByDateInterval(DateTimeInterface $startDate, DateTimeInterface $endDate): bool
    {
        $totalDonations = (new GetTotalDonationsBetweenTwoDates())($startDate, $endDate);
        $firstDonationId = (new GetFirstDonationIdBetweenTwoDates())($startDate, $endDate);
        $lastDonationId = (new GetLastDonationIdBetweenTwoDates())($startDate, $endDate);
        $donationIdsRange = range($firstDonationId, $lastDonationId);
        $exportId = $this->getExportIdByDateInterval($startDate, $endDate);

        return $this->createExport($donationIdsRange, $exportId, $totalDonations);
    }

    /**
     * @since 2.4.0
     */
    public function createExportByDonationIds(array $donationIds, string $exportId = ''): bool
    {
        if (empty($exportId)) {
            $exportIdPrefix = __('bulk-actions', 'give-pdf-receipts');
            $exportId = $this->getExportId($exportIdPrefix);
        }
        $totalDonations = count($donationIds);

        return $this->createExport($donationIds, $exportId, $totalDonations);
    }

    /**
     * @since 2.4.0
     */
    public function getExportId(string $prefix): string
    {
        $dateTimeFormat = get_option('date_format') . '-' . get_option('time_format');
        $exportId = $prefix . '-' . str_replace('--', '-',
                str_replace(['/', '\\', ' ', ',', ':'], '-', date_i18n($dateTimeFormat, time())));

        if ( ! $this->exportIdExist($exportId)) {
            return $exportId;
        }

        $count = 2;
        while ($this->exportIdExist($exportId . '-' . $count)) {
            $count++;
        }

        $exportId .= '-' . $count;

        return $exportId;
    }

    /**
     * @since 2.4.0
     */
    public function getExportIdByDateInterval(DateTimeInterface $startDate, DateTimeInterface $endDate): string
    {
        $startDate = date_i18n(get_option('date_format'), $startDate->getTimestamp());
        $endDate = date_i18n(get_option('date_format'), $endDate->getTimestamp());
        $startDate = str_replace('--', '-', str_replace(['/', '\\', ' ', ',',], '-', $startDate));
        $endDate = str_replace('--', '-', str_replace(['/', '\\', ' ', ',',], '-', $endDate));
        $exportId = $startDate . '-' . $endDate;

        if ( ! $this->exportIdExist($exportId)) {
            return $exportId;
        }

        $count = 2;
        while ($this->exportIdExist($exportId . '-' . $count)) {
            $count++;
        }

        $exportId .= '-' . $count;

        return $exportId;
    }

    /**
     * @since 2.4.0
     */
    public function createDirs(string $tempExportDir, string $exportDir)
    {
        if ( ! $this->fileSystem->exists($tempExportDir)) {
            wp_mkdir_p($tempExportDir);
        }

        if ( ! $this->fileSystem->exists($exportDir)) {
            wp_mkdir_p($exportDir);
        }
    }

    /**
     * @since 2.4.0
     */
    public function exportIdExist(string $exportId): bool
    {
        return $this->fileSystem->is_file($this->exportsDir . $exportId . '.zip') || $this->fileSystem->is_file($this->exportsDir . $exportId . '.preparing.zip');
    }

    /**
     * @since 2.4.0
     */
    public function getTempExportDir(string $exportId): string
    {
        return $this->tempDir . trailingslashit($exportId);
    }

    /**
     * @since 2.4.0
     */
    public function getExportsDir(): string
    {
        return $this->exportsDir;
    }

    /**
     * @since 2.4.0
     */
    public function getZipFilePath(string $exportId): string
    {
        return $this->getExportsDir() . $exportId . '.preparing.zip';
    }

    /**
     * @since 2.4.0
     */
    public function isExportFinished(string $groupID, $totalChunks): bool
    {
        $actions = as_get_scheduled_actions([
            'group' => 'give-pdf-export-' . $groupID,
            'status' => ActionScheduler_Store::STATUS_COMPLETE,
            'per_page' => 0,
        ]);

        return count($actions) === ($totalChunks - 1);
    }

    /**
     * @since 2.4.0
     */
    public function makeExportReadyForDownload(string $zipFilePath, string $tempExportDir)
    {
        $this->fileSystem->move($zipFilePath, $this->removePreparingFromFileName($zipFilePath), true);
        $this->fileSystem->delete($tempExportDir);
    }

    /**
     * @since 2.4.0
     */
    public function removePreparingFromFileName(string $fileName): string
    {
        return str_replace('.preparing.zip', '.zip', $fileName);
    }

    /**
     * @since 2.4.0
     */
    private function createExport(array $donationIdsRange, string $exportId, int $totalDonations): bool
    {
        $chunks = array_chunk($donationIdsRange, $this->chunkSize);
        $totalChunks = count($chunks);

        $tempExportDir = $this->getTempExportDir($exportId);
        $this->createDirs($tempExportDir, $this->exportsDir);

        $zipFilePath = $this->getZipFilePath($exportId);
        $logMessage = 'Started ' . $totalChunks . ' job(s) in the background which will add ' . $totalDonations . ' PDF receipts files to the ' . $exportId . '.preparing.zip archive. Each chunk will handle a maximum of' . $this->chunkSize . ' files, which will limit the server load during the process. The resulting archive will be named ' . $exportId . '.zip.';

        if (ZipFile::create($zipFilePath, $logMessage)) {
            $groupID = uniqid();
            $currentChunk = 1;
            foreach ($chunks as $donationIds) {
                as_enqueue_async_action('givewp_pdf_export',
                    [
                        $exportId,
                        $groupID,
                        $tempExportDir,
                        $zipFilePath,
                        $currentChunk,
                        $totalChunks,
                        $donationIds,
                    ],
                    'give-pdf-export-' . $groupID
                );
                $currentChunk++;
            }

            ZipFile::addLogMessage($zipFilePath,
                'Action Scheduler Group ID: ' . $groupID, false);

            return true;
        }

        return false;
    }
}
