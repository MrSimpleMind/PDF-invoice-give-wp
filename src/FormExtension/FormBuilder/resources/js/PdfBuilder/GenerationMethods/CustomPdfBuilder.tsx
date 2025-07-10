import { PdfBuilderGenerationProps } from "../../types/PdfBuilderProps";
import { Button, PanelRow, SelectControl, TextControl } from "@wordpress/components";
import { ClassicEditor, SettingsSection } from "@givewp/form-builder-library";
import { __ } from "@wordpress/i18n";
import { useEffect } from "@wordpress/element";
import { deleteCustomTemplateRequest, getCustomTemplatesRequest, getTemplateContentRequest } from "../../fetch";
import { DeleteIcon, DeleteIconDisabled } from "./icons";
import { getWindowData } from "../../index";
import { useState } from "react";

/**
 * This component allows user to create their own custom PDF templates using as a base some starter templates that we provide.
 *
 * @since 3.1.0
 */
const CustomPdfBuilder = ({pdfSettings, updatePdfSettings, templateTagsRef}: PdfBuilderGenerationProps) => {
    // @ts-ignore
    const {isDirty} = window.givewp.form.settings.useFormState();
    const {defaultTemplates, customTemplates, nonces} = getWindowData();
    const [lastCustomTemplateId, setLastCustomTemplateId] = useState<any>(pdfSettings.customTemplateId);
    const [loadingTemplates, setLoadingTemplates] = useState<boolean>(false);
    const [deleting, setDeleting] = useState<boolean>(false);
    const [editorContent, setEditorContent] = useState(pdfSettings.customPdfBuilder);

    const getMyTemplates = (customTemplates: {title: string; id: string}[]) => {
        if (customTemplates.length === 0) {
            return [];
        }

        const myTemplates = customTemplates.map((template) => ({
            label: template.title,
            value: template.id,
        }));

        if (myTemplates.length === 0) {
            return [];
        }

        return [
            {
                label: __('My Templates', 'give-pdf-receipts'),
                value: 'my_templates',
                disabled: true,
            },
            ...myTemplates,
        ];
    };

    const [myTemplates, setMyTemplates] = useState(getMyTemplates(customTemplates));

    const starterTemplates = defaultTemplates.map((template) => ({
        label: template.name,
        value: template.filepath,
    }));

    const isStarterTemplate = (templateId: string) => isNaN(Number(templateId));

    /**
     * Used to delete items from the "My Templates" section in the "RECEIPT TEMPLATE" dropdown.
     */
    const deleteCustomTemplate = (templateId: string) => {
        const template = customTemplates.filter((template) => template.id == templateId);
        if (template.length !== 1) {
            return;
        }

        setDeleting(true);
        deleteCustomTemplateRequest(template[0].id, template[0].nonce).then((jsonResponse) => {
            if (!!jsonResponse.success) {
                setMyTemplates(getMyTemplates(jsonResponse.data.custom_templates));

                if (jsonResponse.data.custom_templates.length > 0) {
                    updatePdfSettings('customTemplateId', jsonResponse.data.custom_templates[0].id);
                } else {
                    updatePdfSettings('customTemplateId', 'create_new');
                }
            }
            setDeleting(false);
        });
    };

    /**
     * Used to update the "My Templates" section in the "RECEIPT TEMPLATE" dropdown.
     */
    const updateCustomTemplates = () => {
        const donationFormID = new URLSearchParams(window.location.search).get('donationFormID');
        setLoadingTemplates(true);
        getCustomTemplatesRequest(donationFormID, nonces.getCustomTemplates).then((jsonResponse) => {
            if (!!jsonResponse.data.custom_templates) {
                setMyTemplates(getMyTemplates(jsonResponse.data.custom_templates));
            }
            const customTemplateId = jsonResponse.data.form_custom_template_id;
            if (!!customTemplateId && !isNaN(Number(customTemplateId)) && Number(customTemplateId) > 0) {
                setLastCustomTemplateId(customTemplateId);
                updatePdfSettings('customTemplateId', customTemplateId);
            }
            setLoadingTemplates(false);
        });
    };

    /**
     * Used to load content from any kind of template (starter or custom) in the editor.
     */
    const loadTemplateContent = (templateId: string) => {
        if (templateId === 'create_new') {
            updatePdfSettings('customTemplateName', '');
            setEditorContent('');
            return;
        }

        if (isStarterTemplate(templateId)) {
            const template = defaultTemplates.filter((template) => template.filepath == templateId);
            setLoadingTemplates(true);
            getTemplateContentRequest(template[0].filepath, 'file', template[0].name, template[0].nonce).then(
                (jsonResponse) => {
                    if (!!jsonResponse.success) {
                        updatePdfSettings('customTemplateName', jsonResponse.data.post_title);
                        setEditorContent(jsonResponse.data.post_content);
                    }
                    setLoadingTemplates(false);
                }
            );
        } else {
            const template = customTemplates.filter((template) => template.id == templateId);
            if (template.length === 1) {
                setLoadingTemplates(true);
                getTemplateContentRequest(template[0].id, 'post', template[0].title, template[0].nonce).then(
                    (jsonResponse) => {
                        if (!!jsonResponse.success) {
                            updatePdfSettings('customTemplateName', jsonResponse.data.post_title);
                            setEditorContent(jsonResponse.data.post_content);
                        }
                        setLoadingTemplates(false);
                    }
                );
            }
        }
    };

    /**
     *  Update the custom templates when the Modal opens to retrieve possible new templates
     *  recently saved. We do not update it if the Form Builder is dirty because in cases
     *  like that maybe the user still is working on the PDF builder editor.
     */
    useEffect(() => {
        const interval = setTimeout(() => {
            if (!isDirty) {
                updateCustomTemplates();
            }
        }, 250);

        return () => {
            clearInterval(interval);
        };
    }, []);

    /**
     * Load the template content in the editor when it gets selected/changed in the "RECEIPT TEMPLATE" dropdown.
     */
    useEffect(() => {
        const interval = setTimeout(() => {
            if (lastCustomTemplateId !== pdfSettings.customTemplateId) {
                loadTemplateContent(pdfSettings.customTemplateId);
            }
        }, 250);

        return () => {
            clearInterval(interval);
        };
    }, [pdfSettings.customTemplateId]);

    /**
     * Update the custom PDF content when the user changes it in the editor.
     */
    useEffect(() => {
        if (pdfSettings.customPdfBuilder !== editorContent) {
            updatePdfSettings('customPdfBuilder', editorContent);
        }
    }, [editorContent]);

    return (
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
                    disabled={loadingTemplates}
                    options={
                        loadingTemplates
                            ? [
                                  {
                                      label: __('Loading...', 'give-pdf-receipts'),
                                      value: 'loading_templates',
                                  },
                              ]
                            : [
                                  ...myTemplates,
                                  {
                                      label: __('Starter Templates', 'give-pdf-receipts'),
                                      value: 'starter_templates',
                                      disabled: true,
                                  },
                                  {
                                      label: __('Blank Template', 'give-pdf-receipts'),
                                      value: 'create_new',
                                  },
                                  ...starterTemplates,
                              ]
                    }
                    value={pdfSettings.customTemplateId ?? 'create_new'}
                    onChange={(value) => {
                        setLastCustomTemplateId(pdfSettings.customTemplateId);
                        updatePdfSettings('customTemplateId', value);
                    }}
                />
            </PanelRow>
            <PanelRow
                className={!isStarterTemplate(pdfSettings.customTemplateId) && 'pdf-builder-settings__template_name'}
            >
                <TextControl
                    label={__('Template name', 'give-pdf-receipts')}
                    help={__('Please provide your customized receipt template a unique name.', 'give-pdf-receipts')}
                    value={pdfSettings.customTemplateName}
                    onChange={(value) => updatePdfSettings('customTemplateName', value)}
                />
                {!!pdfSettings.customTemplateId && !isStarterTemplate(pdfSettings.customTemplateId) && (
                    <button
                        disabled={deleting || loadingTemplates}
                        onClick={() => {
                            deleteCustomTemplate(pdfSettings.customTemplateId);
                        }}
                    >
                        {deleting || loadingTemplates ? <DeleteIconDisabled /> : <DeleteIcon />}
                    </button>
                )}
            </PanelRow>
            <PanelRow>
                <SelectControl
                    label={__('Page size', 'give-pdf-receipts')}
                    help={__(
                        'Select the page size you would like the PDF receipts to be generated at. Note: You may need to adjust the content width if you generate a page size smaller than the default size.',
                        'give-pdf-receipts'
                    )}
                    options={[
                        {label: __('Letter (8.5 by 11 inches)', 'give-pdf-receipts'), value: 'LETTER'},
                        {label: __('A4 (8.27 by 11.69 inches)', 'give-pdf-receipts'), value: 'A4'},
                        {label: __('A5 (5.8 by 8.3 inches)', 'give-pdf-receipts'), value: 'A5'},
                        {label: __('A6 (4.1 by 5.8 inches)', 'give-pdf-receipts'), value: 'A6'},
                    ]}
                    value={pdfSettings.customPageSize}
                    onChange={(value) => {
                        updatePdfSettings('customPageSize', value);
                    }}
                />
            </PanelRow>
            <PanelRow>
                <div style={{width: '100%'}}>
                    <ClassicEditor
                        id={'givewp-custom-pdf-builder'}
                        label={__('PDF Builder', 'give-pdf-receipts')}
                        content={editorContent}
                        setContent={setEditorContent}
                        showEditorTabs={true}
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
    );
};

export default CustomPdfBuilder;
