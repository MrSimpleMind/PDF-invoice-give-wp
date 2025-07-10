<?php

namespace GivePdfReceipt\PdfExport\Log;

use Give\Log\Log;

/**
 * @since 2.4.0
 */
class PdfExportLog extends Log
{
    /**
     * @inheritDoc
     */
    public static function __callStatic($name, $arguments)
    {
        $arguments[1]['category'] = 'PDF Export';
        $arguments[1]['source'] = 'PDF Export';

        if (
            array_key_exists('Export ID', $arguments[1])
        ) {
            $arguments[1]['source'] = $arguments[1]['Export ID'];
        }

        parent::__callStatic($name, $arguments);
    }
}
