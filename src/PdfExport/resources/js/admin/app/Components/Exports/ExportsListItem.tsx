import React, {useState} from 'react';
import styles from './ExportsListItem.module.scss';
import {deleteExport} from '../../../api';
import apiFetch from '@wordpress/api-fetch';
import {DeleteIcon, DeletingIcon, DownloadIcon, PreparingIcon} from '../Icons';
import {__} from '@wordpress/i18n';

interface ExportsListItemProps {
    key: any;
    item: Array<any>;
    setExportsList: Function;
    greyBackground: boolean;
}

const ExportsListItem = ({item, setExportsList, greyBackground}: ExportsListItemProps) => {
    const [deleting, setDeleting] = useState(false);

    function handleOnClick(event) {
        event.preventDefault();
        setDeleting(true);
        apiFetch(deleteExport(item['id'])).then((response: string) => {
            setDeleting(false);
            setExportsList(JSON.parse(response));
        });

        return false;
    }

    return (
        <li className={greyBackground ? styles['listItem'] + ' ' + styles['grey'] : styles['listItem']}>
            <span>{item['created_at']}</span>
            <span>{__('donation-receipts', 'give-pdf-receipts') + '-' + item['file_name']}</span>
            {item['preparing'] ? (
                <span className={styles['preparing']}>
                    <PreparingIcon label={__('Preparing for download', 'give-pdf-receipts')} />
                </span>
            ) : (
                <span className={styles['actions']}>
                    <a className={styles['download']} href={item['file_url']} download>
                        <DownloadIcon label={__('Download', 'give-pdf-receipts')} />
                    </a>

                    {deleting ? (
                        <a className={styles['disabledDelete']} href={'#'} onClick={(event) => event.preventDefault()}>
                            <DeletingIcon label={__('Deleting...', 'give-pdf-receipts')} />
                        </a>
                    ) : (
                        <a className={styles['delete']} href={'#'} onClick={handleOnClick}>
                            <DeleteIcon label={__('Delete', 'give-pdf-receipts')} />
                        </a>
                    )}
                </span>
            )}
        </li>
    );
};

export default ExportsListItem;
