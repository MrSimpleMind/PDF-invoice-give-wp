import {Button, PanelRow, RadioControl} from '@wordpress/components';
import {__} from '@wordpress/i18n';
import {SettingsSection} from '@givewp/form-builder-library';
import {PdfBuilderProps} from '../types/PdfBuilderProps';
import SetPdfTemplates from './GenerationMethods/SetPdfTemplates';
import CustomPdfBuilder from './GenerationMethods/CustomPdfBuilder';
import CopyToClipboardButton from './components/CopyToClipboardButton';
import {getWindowData} from '../index';
import {useRef} from 'react';

/**
 * @since 3.1.1 Load SetPdfTemplates component if generationMethod is undefined.
 * @since 3.1.0
 */
const PdfBuilder = ({pdfSettings, updatePdfSettings}: PdfBuilderProps) => {
    const donationFormID = new URLSearchParams(window.location.search).get('donationFormID');
    const {setPdfPreviewUrl, customPdfPreviewUrl, templatesPdfTags, customPdfTags} = getWindowData();
    const templateTagsRef = useRef<HTMLUListElement>(null);

    return (
        <>
            <SettingsSection
                title={__('Generation method', 'give-pdf-receipts')}
                description={__(
                    'The Set PDF Templates option will generate PDFs using preconfigured templates. The Custom PDF Builder allows you to customize your own templates using a rich editor that allows for custom text, images and HTML to be easily inserted.',
                    'give-pdf-receipts'
                )}
            >
                <PanelRow>
                    <div>
                        <RadioControl
                            className="radio-control--pdf-builder-options"
                            label={__('PDF Builder options', 'give-pdf-receipts')}
                            hideLabelFromVision={true}
                            selected={pdfSettings.generationMethod ?? 'set_pdf_templates'}
                            options={[
                                {label: __('Set PDF Templates', 'give-pdf-receipts'), value: 'set_pdf_templates'},
                                {label: __('Custom PDF Builder', 'give-pdf-receipts'), value: 'custom_pdf_builder'},
                            ]}
                            onChange={(value) => updatePdfSettings('generationMethod', value)}
                        />
                    </div>
                </PanelRow>
            </SettingsSection>

            {'set_pdf_templates' === pdfSettings.generationMethod ||
            typeof pdfSettings.generationMethod === 'undefined' ? (
                <SetPdfTemplates
                    pdfSettings={pdfSettings}
                    updatePdfSettings={updatePdfSettings}
                    templateTagsRef={templateTagsRef}
                />
            ) : (
                <CustomPdfBuilder
                    pdfSettings={pdfSettings}
                    updatePdfSettings={updatePdfSettings}
                    templateTagsRef={templateTagsRef}
                />
            )}

            <SettingsSection
                title={__('Template tags', 'give-pdf-receipts')}
                description={__(
                    'The following template tags will work for the Header message, Footer message and Additional Notes',
                    'give-pdf-receipts'
                )}
            >
                <PanelRow>
                    <ul className={'pdf-builder-settings-template-tags'} ref={templateTagsRef}>
                        {('set_pdf_templates' === pdfSettings.generationMethod ? templatesPdfTags : customPdfTags).map(
                            (tag) => (
                                <li key={tag.tag}>
                                    <strong>{'{' + tag.tag + '}'}</strong>
                                    <p style={{fontSize: '.75rem'}}>{tag.desc}</p>
                                    <CopyToClipboardButton textToCopy={'{' + tag.tag + '}'} />
                                </li>
                            )
                        )}
                    </ul>
                </PanelRow>
            </SettingsSection>
            <Button
                className={'pdf-builder-settings__pdf-builder-btn'}
                variant={'secondary'}
                onClick={() =>
                    window.open(
                        'set_pdf_templates' === pdfSettings.generationMethod
                            ? setPdfPreviewUrl + '&form_id=' + donationFormID
                            : customPdfPreviewUrl + '&form_id=' + donationFormID,
                        '_blank',
                        'noopener,noreferrer'
                    )
                }
            >
                {__('Preview PDF Template', 'give-pdf-receipts')}
            </Button>
        </>
    );
};;

export default PdfBuilder;
