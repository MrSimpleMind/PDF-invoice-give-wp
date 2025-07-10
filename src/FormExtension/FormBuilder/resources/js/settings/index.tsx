import { PanelRow, ToggleControl } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { SettingsSection } from "@givewp/form-builder-library";
import { PdfSettingsProps } from "../types/PdfSettingsProps";
import { getWindowData } from "../index";
import { createInterpolateElement } from "@wordpress/element";
import PdfBuilder from "../PdfBuilder";

/**
 * @since 3.2.0 Updated props with settings and setSettings for compatability with GiveWP 3.3.0+.
 * @since 3.1.1 Show global settings link if enabled is undefined.
 * @since 3.1.0
 */
export default function PdfReceiptsSettings({settings, setSettings}) {
    const pdfSettings: PdfSettingsProps = settings.pdfSettings ?? {};
    const {globalOptionsUrl} = getWindowData();

    const updatePdfSettings = (property: string, value: any) => {
        setSettings({
            pdfSettings: {
                ...pdfSettings,
                [property]: value,
            },
        });
    };

    const customizePdfReceiptsDescription = createInterpolateElement(
        __('Uses <a>global settings</a> when disabled.', 'give-pdf-receipts'),
        {
            a: <a href={globalOptionsUrl} target="_blank" />,
        }
    );

    return (
        <div className={'give-form-settings__pdf-receipts'}>
            <SettingsSection
                title={__('PDF Receipts', 'give')}
                description={__(
                    'This allows you to customize the PDF Receipts settings for just this donation form.',
                    'give'
                )}
            >
                <PanelRow className={'no-extra-gap'}>
                    <ToggleControl
                        label={__('Customize PDF Receipts', 'give-pdf-receipts')}
                        help={customizePdfReceiptsDescription}
                        checked={pdfSettings.enable === 'enabled'}
                        onChange={(value) => {
                            updatePdfSettings('enable', value ? 'enabled' : 'global');
                        }}
                    />
                </PanelRow>
            </SettingsSection>

            {pdfSettings.enable === 'enabled' && (
                <PdfBuilder pdfSettings={pdfSettings} updatePdfSettings={updatePdfSettings} />
            )}
        </div>
    );
}
