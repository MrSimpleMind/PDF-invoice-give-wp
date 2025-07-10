import React from 'react';
import styles from './ExportsHeader.module.scss';
import {__} from '@wordpress/i18n';

const ExportsHeader = () => {
    return (
        <header className={styles['header']}>
            <p>{__('Creation Date', 'give-pdf-receipts')}</p>
            <p>{__('File Name', 'give-pdf-receipts')}</p>
            <p>{__('Actions', 'give-pdf-receipts')}</p>
        </header>
    );
};

export default ExportsHeader;
