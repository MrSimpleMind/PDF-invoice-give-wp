import React from 'react';
import styles from './ExportsEmptyList.module.scss';
import {EmptyListIcon} from '../Icons';
import {__} from '@wordpress/i18n';

interface ExportsEmptyListProps {
    setShowModal: Function;
}

const ExportsEmptyList = ({setShowModal}: ExportsEmptyListProps) => {
    function handleOnClick(event) {
        event.preventDefault();
        setShowModal(true);
        return false;
    }

    return (
        <ul className={styles['emptyList']}>
            <li>
                <span>
                    <EmptyListIcon
                        label={__('No receipt exports have been generated.', 'give-pdf-receipts')}
                        link={
                            <a href={'#'} onClick={handleOnClick}>
                                {__('Generate a ZIP of receipts', 'give-pdf-receipts')}
                            </a>
                        }
                    />
                </span>
            </li>
        </ul>
    );
};

export default ExportsEmptyList;
