<?php

namespace GivePdfReceipt\PdfExport\Utilities;

use GivePdfReceipt\PdfExport\Log\PdfExportLog;
use ZipArchive;

/**
 * @since 2.4.0
 */
class ZipFile
{
    /**
     * @since 2.4.0
     */
    static public function create(string $zipFilePath, string $logMessage = ''): bool
    {
        $zip = new ZipArchive;
        $zipResponse = $zip->open($zipFilePath, ZipArchive::CREATE);

        if ($zipResponse !== true) {
            PdfExportLog::error(
                sprintf('There was a problem creating the %s export archive. Error: %s ',
                    $zipFilePath, $zipResponse),
                [
                    'Error' => $zipResponse,
                    'Export ID' => self::getExportID($zipFilePath),
                    'Zip File Path' => $zipFilePath,
                ]
            );

            return false;
        }

        $zip->addEmptyDir(self::getExportID($zipFilePath));
        $zip->close();

        self::addLogMessage($zipFilePath, 'Zip File Creation Initialized. ' . $logMessage, false);

        return true;
    }

    /**
     * @since 2.4.0
     */
    static public function addNewFile(string $newFilePath, string $zipFilePath, string $logMessage = ''): bool
    {
        $zip = new ZipArchive;

        $zipResponse = $zip->open($zipFilePath);

        if ($zipResponse !== true) {
            PdfExportLog::error(
                sprintf('Error %s - There was a problem adding the %s file to the final %s export archive.',
                    $zipResponse, $newFilePath, $zipFilePath),
                [
                    'Error' => $zipResponse,
                    'Export ID' => self::getExportID($zipFilePath),
                    'Zip File Path' => $zipFilePath,
                    'Total files on the zip export archive' => self::numFiles($zipFilePath) - 1,
                    'New File Path' => $newFilePath,
                ]
            );

            return false;
        }

        $entryName = self::getExportID($zipFilePath) . '/' . self::getFileName($newFilePath);
        $zip->addFile($newFilePath, $entryName);
        $zip->close();

        self::addLogMessage($zipFilePath, 'New "' . self::getFileName($newFilePath) . '" file added. ' . $logMessage);

        return true;
    }

    /**
     * @since 2.4.0
     */
    static public function addLogMessage(
        string $zipFilePath,
        string $logMessage,
        bool $debug = true,
        bool $success = false
    ) {

        $exportID = self::getExportID($zipFilePath);

        if ($success) {
            PdfExportLog::success(
                $logMessage,
                [
                    'Export ID' => $exportID,
                    'Zip File Path' => $zipFilePath,
                    'Total files on the zip export archive' => self::numFiles($zipFilePath) - 1,
                ]
            );
        } elseif ($debug) {
            PdfExportLog::debug(
                $logMessage,
                [
                    'Export ID' => $exportID,
                    'Zip File Path' => $zipFilePath,
                    'Total files on the zip export archive' => self::numFiles($zipFilePath) - 1,
                ]
            );
        } else {
            PdfExportLog::info(
                $logMessage,
                [
                    'Export ID' => $exportID,
                    'Zip File Path' => $zipFilePath,
                    'Total files on the zip export archive' => self::numFiles($zipFilePath) - 1,
                ]
            );
        }
    }

    /**
     * @since 2.4.0
     */
    static public function numFiles(string $zipFilePath): int
    {
        $zip = new ZipArchive;

        $res = $zip->open($zipFilePath);

        if ($res !== true) {
            return 0;
        }

        return $zip->numFiles;
    }

    /**
     * @since 2.4.0
     */
    static private function getExportID(string $zipFilePath): string
    {
        $exportID = explode('/', $zipFilePath);
        $exportID = end($exportID);

        $exportID = str_replace('.preparing.zip', '', $exportID);
        $exportID = str_replace('.zip', '', $exportID);

        return $exportID;
    }

    /**
     * @since 2.4.0
     */
    static private function getFileName(string $newFilePath): string
    {
        $fileName = explode('/', $newFilePath);

        return end($fileName);
    }
}
