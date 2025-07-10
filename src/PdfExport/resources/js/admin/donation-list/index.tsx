import {bulkActions} from './bulkActions';
import {IGivePdfReceiptsExportTool} from '../interfaces';

declare global {
    interface Window {
        GivePdfReceiptsExportTool: IGivePdfReceiptsExportTool;
    }
}

// @ts-ignore
if (window.GiveDonations && window.GiveDonations.addonsBulkActions) {
    // @ts-ignore
    window.GiveDonations.addonsBulkActions.push(...bulkActions);
}
