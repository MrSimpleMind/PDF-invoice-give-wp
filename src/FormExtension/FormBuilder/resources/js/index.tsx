import {__} from '@wordpress/i18n';
import {IGivePdfReceiptsFormBuilder} from './interfaces/IGivePdfReceiptsFormBuilder';
import PdfReceiptsSettings from './settings';

declare const window: {
    pdfReceiptsFormBuilder: IGivePdfReceiptsFormBuilder;
} & Window;
export const getWindowData = () => {
    return window.pdfReceiptsFormBuilder;
};

/**
 * @since 3.2.0 Updated to use form builder settings filter compatible with GiveWP 3.3.0+
 * @since 3.1.0
 */
const addPdfReceiptsSettings = (settings) => {
    return [
        ...settings,
        {
            name: __('PDF Receipts', 'give-pdf-receipts'),
            path: 'pdf-receipts',
            element: PdfReceiptsSettings,
        },
    ];
};
wp.hooks.addFilter('givewp_form_builder_settings_additional_routes', 'give-pdf-receipts', addPdfReceiptsSettings);
