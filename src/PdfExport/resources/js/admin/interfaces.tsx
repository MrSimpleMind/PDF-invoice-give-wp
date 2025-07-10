export interface IGivePdfReceiptsExportTool {
    zipEnabled: boolean;
    apiRoot: string;
    apiEndpoints: {
        create: string;
        delete: string;
        list: string;
    };
    apiNonce: string;
    exportsList: string;
    locale: string;
    assetsUrl: string;
    adminUrl: string;
}
