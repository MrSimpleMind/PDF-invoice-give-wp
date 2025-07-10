import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import {IGivePdfReceiptsExportTool} from '../interfaces';

declare global {
    interface Window {
        GivePdfReceiptsExportTool: IGivePdfReceiptsExportTool;
    }
}

ReactDOM.render(
    <React.StrictMode>
        <App />
    </React.StrictMode>,
    document.getElementById('give-receipts-pdf-export-app')
);
