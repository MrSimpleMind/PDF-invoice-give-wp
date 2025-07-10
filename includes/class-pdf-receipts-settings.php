<?php
/**
 * PDF Receipt Settings.
 *
 * Registers all the settings required for the plugin.
 *
 * @package     Give PDF Receipts
 * @since       1.0
 */

// Exit if accessed directly
use GivePdfReceipt\Helpers\Admin\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Give_PDF_Receipts_Settings
 */
class Give_PDF_Receipts_Settings extends Give_Settings_Page {

	/**
	 * Give_PDF_Receipts_Settings constructor.
	 */
	public function __construct() {

		$this->id          = 'pdf_receipts';
		$this->label       = __( 'PDF Receipts', 'give-pdf-receipts' );
		$this->default_tab = 'pdf_receipts';

		// Register custom fields.
		add_action( 'give_admin_field_pdf_receipts_preview_button', array( $this, 'preview_button_callback' ), 10, 2 );
		add_action( 'give_admin_field_pdf_receipt_template_name', 'give_pdf_receipt_template_name', 10, 2 );
		add_action( 'give_admin_field_pdf_receipt_template_select', 'give_pdf_receipt_template_select', 10, 2 );
		add_action( 'give_admin_field_set_pdf_receipts_preview_button', array( $this, 'set_pdf_preview_button_callback' ), 10, 2 );

		// Save template data only when generation method is custom_pdf_builder
		if ( isset( $_POST['give_pdf_generation_method'] ) && 'custom_pdf_builder' === $_POST['give_pdf_generation_method'] ) {
			add_action( "give-settings_save_{$this->id}", array( $this, 'save_custom_pdf_template' ), 999 );
		}

		parent::__construct();
	}

	/**
	 * Add setting sections.
	 *
	 * @return array
	 */
	function get_sections() {

		$sections = array(
			'pdf_receipts' => __( 'PDF Receipts Settings', 'give-pdf-receipts' ),
			'bulk_downloads' => __( 'Bulk Downloads', 'give-pdf-receipts' ),
		);

		return $sections;
	}

	/**
	 * Get setting.
	 *
	 * @return array
	 */
	function get_settings() {

        if ( isset($_GET['section']) && 'bulk_downloads' === $_GET['section']) {
            do_action( 'give_pdf_receipts_pre_bulk_downloads' );

            $template_id = give_get_meta( 0, 'give_pdf_receipt_template', true );
            $template    = get_post( $template_id );

            if ( $template ) {
                $post_title   = $template->post_title;
                $post_content = $template->post_content;
            }

            $options        = array();
            $default_option = 'global';

            $options['enabled']  = __( 'Enabled', 'give-pdf-receipts' );
            $options['disabled'] = __( 'Disabled', 'give-pdf-receipts' );
            $default_option      = 'enabled';

            $bulk_downloads_page = array(
                array(
                    'id'   => 'give_pdf_settings',
                    'type' => 'title',
                ),
                array(
                    'name'          => __( 'PDF Receipts', 'give-pdf-receipts' ),
                    'desc'          => $is_global ? __( 'This enables the PDF Receipts feature for all your website\'s donation forms. Note: You can disable the global options and enable and customize options per form as well.', 'give-pdf-receipts' ) : __( 'This allows you to customize the PDF Receipts settings for just this donation form. You can disable PDF Receipts for just this form as well or simply use the global settings.', 'give-pdf-receipts' ),
                    'id'            => 'give_pdf_receipts_enable_disable',
                    'wrapper_class' => 'give_pdf_receipts_enable_disable',
                    'type'          => 'radio_inline',
                    'default'       => $default_option,
                    'options'       => $options,
                ),
                array(
                    'id'            => 'give_pdf_generation_method',
                    'name'          => __( 'Generation Method', 'give-pdf-receipts' ),
                    'description'   => __( 'Choose the method you would like to generate PDF receipts. The Custom PDF Builder allows you to customize your own templates using a rich editor that allows for custom text, images and HTML to be easily inserted. The Set PDF Templates option will generate PDFs using preconfigured templates.', 'give-pdf-receipts' ),
                    'default'       => 'set_pdf_templates',
                    'type'          => 'radio_inline',
                    'wrapper_class' => 'pdf-receipts-fields give_pdf_generation_method give-hidden',
                    'options'       => array(
                        'set_pdf_templates'  => __( 'Set PDF Templates', 'give-pdf-receipts' ),
                        'custom_pdf_builder' => __( 'Custom PDF Builder', 'give-pdf-receipts' ),
                    ),
                ),
            );

            return apply_filters( 'give_pdf_receipts_bulk_downloads', $bulk_downloads_page );
        }

		do_action( 'give_pdf_receipts_pre_settings' );

        $is_global = true; // Set Global flag.

		// Build global settings.
		return apply_filters( 'give_settings_pdf_receipts', give_pdf_receipts_settings( $is_global, 0 ) );
	}

	/**
	 * Save the Custom PDF Template.
	 */
	function save_custom_pdf_template() {
		Settings::SaveCustomTemplate(0);
	}

	/**
	 * Custom PDF Receipt Preview button.
	 *
	 * @since 2.0.9
	 *
	 * @param array  $value        field array.
	 * @param string $option_value field value.
	 */
	function preview_button_callback( $value, $option_value ) {
		ob_start(); ?>
		<tr valign="top give-pdf-receipts-preview" <?php echo ! empty( $value['wrapper_class'] ) ? 'class="' . $value['wrapper_class'] . '"' : '' ?>>
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_attr( $value['name'] ); ?></label>
			</th>
			<td class="give-pdf-receipts-preview-button-td" colspan="2">
				<a href="<?php echo esc_url( add_query_arg( array( 'give_pdf_receipts_action' => 'preview_pdf' ), admin_url() ) ); ?>"
				   class="button-secondary" target="_blank"
				   title="<?php _e( 'Preview PDF', 'give-pdf-receipts' ); ?> "><?php _e( 'Preview PDF', 'give-pdf-receipts' ); ?></a>
				<p class="give-field-description"><?php echo give_get_field_description( $value ); ?></p>
			</td>
		</tr>
		<?php
		echo ob_get_clean();
	}

	/**
	 * Set PDF Receipt Preview button.
	 *
	 * @since 2.0.9
	 *
	 * @param array  $value        field array.
	 * @param string $option_value field value.
	 */
	function set_pdf_preview_button_callback( $value, $option_value ) {
		ob_start(); ?>
		<tr valign="top give-pdf-receipts-preview" <?php echo ! empty( $value['wrapper_class'] ) ? 'class="' . $value['wrapper_class'] . '"' : '' ?>>
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_attr( $value['name'] ); ?></label>
			</th>
			<td class="give-pdf-receipts-preview-button-td" colspan="2">
				<a href="<?php echo esc_url( add_query_arg( array( 'give_pdf_receipts_action' => 'preview_set_pdf_template' ), admin_url() ) ); ?>"
				   class="button-secondary" target="_blank"
				   title="<?php _e( 'Preview Set PDF Template', 'give-pdf-receipts' ); ?> "><?php _e( 'Preview Set PDF Template', 'give-pdf-receipts' ); ?></a>
				<p class="give-field-description"><?php echo give_get_field_description( $value ); ?></p>
			</td>
		</tr>
		<?php
		echo ob_get_clean();
	}
}

