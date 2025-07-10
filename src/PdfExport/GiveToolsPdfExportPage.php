<?php

namespace GivePdfReceipt\PdfExport;

use Give_Settings_Page;

/**
 * @since 2.4.0
 */
class GiveToolsPdfExportPage extends Give_Settings_Page
{
    protected $enable_save = false;

    /**
     * @since 2.4.0
     */
    public function __construct()
    {
        $this->id = 'pdf-export';
        $this->label = esc_html__('PDF Export', 'give-pdf-receipts');

        parent::__construct();

        // Do not use main form for this tab.
        if (give_get_current_setting_tab() === $this->id) {
            add_action('give-tools_open_form', '__return_empty_string');
            add_action('give-tools_close_form', '__return_empty_string');
        }
    }

    /**
     * @since 2.4.0
     */
    public function output()
    {
        $GLOBALS['give_hide_save_button'] = true;
        ?>
        <div id="give-receipts-pdf-export-app"></div>
        <?php
    }
}
