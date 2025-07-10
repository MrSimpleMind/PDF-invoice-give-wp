import {getWindowData} from './index';

/**
 * @since 3.1.0
 */
export const getTemplateContentRequest = async (
    template_id: string,
    template_location: string,
    template_name: string,
    nonce: string
) => {
    const {ajaxUrl} = getWindowData();
    const formData = new FormData();
    formData.append('action', 'get_builder_content');
    formData.append('template_id', template_id);
    formData.append('template_location', template_location);
    formData.append('template_name', template_name);
    formData.append('nonce', nonce);

    return await fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
    })
        .then((response) => {
            return response.json();
        })
        .then((json) => {
            return json;
        });
};

/**
 * @since 3.1.0
 */
export const getCustomTemplatesRequest = async (form_id: string, nonce: string) => {
    const {ajaxUrl} = getWindowData();
    const formData = new FormData();
    formData.append('action', 'get_custom_templates');
    formData.append('form_id', form_id);
    formData.append('nonce', nonce);

    return await fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
    })
        .then((response) => {
            return response.json();
        })
        .then((json) => {
            return json;
        });
};

/**
 * @since 3.1.0
 */
export const deleteCustomTemplateRequest = async (template_id: string, nonce: string) => {
    const {ajaxUrl} = getWindowData();
    const formData = new FormData();
    formData.append('action', 'delete_pdf_template');
    formData.append('template_id', template_id);
    formData.append('nonce', nonce);

    return await fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
    })
        .then((response) => {
            return response.json();
        })
        .then((json) => {
            return json;
        });
};
