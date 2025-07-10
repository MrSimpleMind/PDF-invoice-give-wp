import {PdfSettingsProps} from './PdfSettingsProps';

/**
 * @since 3.1.0
 */
export type PdfBuilderProps = {
    pdfSettings: PdfSettingsProps;
    updatePdfSettings: (property: string, value: any) => void;
};

export type PdfBuilderGenerationProps = PdfBuilderProps & {
    templateTagsRef: {
        current: HTMLUListElement;
    };
};
