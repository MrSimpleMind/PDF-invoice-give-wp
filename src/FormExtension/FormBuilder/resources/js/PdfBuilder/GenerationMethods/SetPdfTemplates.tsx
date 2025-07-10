import {PdfBuilderGenerationProps} from '../../types/PdfBuilderProps';
import {__} from '@wordpress/i18n';
import {
    Button,
    ColorPalette,
    PanelRow,
    SelectControl,
    TextareaControl,
    TextControl,
    ToggleControl,
} from '@wordpress/components';
import {SettingsSection} from '@givewp/form-builder-library';
import LogoUpload from '../components/LogoUpload';
import React from 'react';
import {moveCustomColorDropdown} from '../utils/moveCustomColorDropdown';

/**
 * @since 3.1.0
 */
const SetPdfTemplates = ({pdfSettings, updatePdfSettings, templateTagsRef}: PdfBuilderGenerationProps) => {
    const colors = [
        {name: '', color: '#000000'},
        {name: '', color: '#FFFFFF'},
        {name: '', color: '#1E8CBE'},
        {name: '', color: '#f9e3ca'},
        {name: '', color: '#9fd0f8'},
        {name: '', color: '#4492dd'},
        {name: '', color: '#9058d8'},
        {name: '', color: '#f6f6f6'},
        {name: '', color: '#00451d'},
        {name: '', color: '#adb8c2'},
        {name: '', color: '#e792a7'},
        {name: '', color: '#bd3d36'},
        {name: '', color: '#eb712e'},
        {name: '', color: '#f1bb40'},
        {name: '', color: '#95dab7'},
        {name: '', color: '#63cc8a'},
    ];

    moveCustomColorDropdown();

    return (
        <>
            <SettingsSection
                title={__('Template details', 'give-pdf-receipts')}
                description={__('Set the content structure for the PDF Receipt', 'give-pdf-receipts')}
            >
                <PanelRow>
                    <SelectControl
                        label={__('Receipt template', 'give-pdf-receipts')}
                        help={__(
                            'Please select a template for your PDF Receipts for this donation form.',
                            'give-pdf-receipts'
                        )}
                        options={[
                            {label: __('Default', 'give-pdf-receipts'), value: 'default'},
                            {label: __('Stacked', 'give-pdf-receipts'), value: 'blue_stripe'},
                            {label: __('Lines', 'give-pdf-receipts'), value: 'lines'},
                            {label: __('Minimal', 'give-pdf-receipts'), value: 'minimal'},
                            {label: __('Traditional', 'give-pdf-receipts'), value: 'traditional'},
                        ]}
                        value={pdfSettings.templateId ?? 'default'}
                        onChange={(value) => {
                            updatePdfSettings('templateId', value);
                        }}
                    />
                </PanelRow>
                <PanelRow className={'pdf-builder-settings__logo_upload'}>
                    <LogoUpload
                        value={pdfSettings.logoUpload}
                        onChange={(value) => updatePdfSettings('logoUpload', value)}
                    />
                </PanelRow>
                <PanelRow>
                    <TextControl
                        label={__('Header message', 'give-pdf-receipts')}
                        help={__(
                            'Enter the message you would like to be shown on the header of the receipt.',
                            'give-pdf-receipts'
                        )}
                        value={pdfSettings.headerMessage}
                        onChange={(value) => updatePdfSettings('headerMessage', value)}
                    />
                </PanelRow>

                {pdfSettings.templateId !== 'default' && pdfSettings.templateId !== 'blue_stripe' && (
                    <PanelRow>
                        <TextControl
                            label={__('Footer message', 'give-pdf-receipts')}
                            help={__(
                                'Enter the message you would like to be shown on the footer of the receipt.',
                                'give-pdf-receipts'
                            )}
                            value={pdfSettings.footerMessage}
                            onChange={(value) => updatePdfSettings('footerMessage', value)}
                        />
                    </PanelRow>
                )}

                <PanelRow>
                    <div>
                        <TextareaControl
                            label={__('Additional notes', 'give-pdf-receipts')}
                            help={__(
                                'Enter any messages you would to be displayed at the end of the receipt. Only plain text is currently supported. Any HTML will not be shown on the receipt',
                                'give-pdf-receipts'
                            )}
                            value={pdfSettings.additionalNotes}
                            onChange={(value) => updatePdfSettings('additionalNotes', value)}
                            rows={10}
                        />
                        <Button
                            variant={'secondary'}
                            onClick={() => templateTagsRef.current.scrollIntoView({behavior: 'smooth'})}
                            style={{
                                width: '100%',
                                marginTop: '0.5rem',
                                height: '2.5rem',
                                justifyContent: 'center',
                            }}
                        >
                            {__('View template tags', 'give')}
                        </Button>
                    </div>
                </PanelRow>
            </SettingsSection>
            <SettingsSection
                title={__('Individual/Organization Details', 'give-pdf-receipts')}
                description={__(
                    'Enter individual or organization details that will show on the receipt',
                    'give-pdf-receipts'
                )}
            >
                <PanelRow>
                    <TextControl
                        label={__('Name', 'give-pdf-receipts')}
                        help={__(
                            'Enter the organization or individual name that will be shown on the receipt.',
                            'give-pdf-receipts'
                        )}
                        value={pdfSettings.name}
                        onChange={(value) => updatePdfSettings('name', value)}
                    />
                </PanelRow>
                <PanelRow>
                    <TextControl
                        label={__('Address Line 1', 'give-pdf-receipts')}
                        help={__('Enter the first address line that will appear on the receipt.', 'give-pdf-receipts')}
                        value={pdfSettings.addressLine1}
                        onChange={(value) => updatePdfSettings('addressLine1', value)}
                    />
                </PanelRow>
                <PanelRow>
                    <TextControl
                        label={__('Address Line 2', 'give-pdf-receipts')}
                        help={__('Enter the second address line that will appear on the receipt.', 'give-pdf-receipts')}
                        value={pdfSettings.addressLine2}
                        onChange={(value) => updatePdfSettings('addressLine2', value)}
                    />
                </PanelRow>
                <PanelRow>
                    <TextControl
                        label={__('City, State and Zip code', 'give-pdf-receipts')}
                        help={__(
                            'Enter the city, state/province/county and zip/postal code that will appear on the receipt.',
                            'give-pdf-receipts'
                        )}
                        value={pdfSettings.cityStateZip}
                        onChange={(value) => updatePdfSettings('cityStateZip', value)}
                    />
                </PanelRow>
                <PanelRow className={'no-extra-gap'}>
                    <ToggleControl
                        label={__('Display website URL', 'give-pdf-receipts')}
                        help={__('Enable the option to display your website url on the receipt', 'give-pdf-receipts')}
                        checked={pdfSettings.displayWebsiteUrl}
                        onChange={(value) => updatePdfSettings('displayWebsiteUrl', value)}
                    />
                </PanelRow>
                <PanelRow>
                    <TextControl
                        label={__('Email address', 'give-pdf-receipts')}
                        help={__('Enter the email address that will appear on the receipt.', 'give-pdf-receipts')}
                        value={pdfSettings.emailAddress}
                        onChange={(value) => updatePdfSettings('emailAddress', value)}
                    />
                </PanelRow>
            </SettingsSection>
            <SettingsSection
                title={__('Color picker', 'give-pdf-receipts')}
                description={__(
                    'Customize the main color used for headings and some backgrounds within the PDF receipt template.',
                    'give-pdf-receipts'
                )}
            >
                <PanelRow>
                    <ColorPalette
                        colors={colors}
                        value={pdfSettings.colorPicker ?? '#1E8CBE'}
                        onChange={(value) => updatePdfSettings('colorPicker', value)}
                    />
                </PanelRow>
            </SettingsSection>
        </>
    );
};

export default SetPdfTemplates;
