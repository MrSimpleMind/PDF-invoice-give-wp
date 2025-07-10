/**
 * @since 3.1.0
 */
export interface IGivePdfReceiptsFormBuilder {
    ajaxUrl: string;
    globalOptionsUrl: string;
    customPdfPreviewUrl: string;
    setPdfPreviewUrl: string;
    templatesPdfTags: {tag: string; desc: string}[];
    customPdfTags: {tag: string; desc: string}[];
    defaultTemplates: {name: string; filepath: string; nonce: string}[];
    customTemplates: {id: string; title: string; nonce: string}[];
    nonces: {
        getCustomTemplates: string;
    };
}
