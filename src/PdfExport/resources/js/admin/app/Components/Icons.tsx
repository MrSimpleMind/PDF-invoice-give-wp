import React from 'react';
import styles from './Icons.module.scss';
import {assetUrl} from '../utils';

export const NewIcon = ({label, ...props}) => {
    return (
        <span className={styles['iconContainer']}>
            <img src={assetUrl('NewIcon.svg')} alt={label} />
            <label>{label}</label>
        </span>
    );
};
export const EmptyListIcon = ({label, link, ...props}) => {
    return (
        <span className={styles['iconContainer']}>
            <img src={assetUrl('EmptyListIcon.svg')} alt={label} />
            <label>
                {label} {link}
            </label>
        </span>
    );
};

export const DownloadIcon = ({label, ...props}) => {
    return (
        <span className={styles['iconContainer']}>
            <img src={assetUrl('DownloadIcon.svg')} alt={label} />
            <label>{label}</label>
        </span>
    );
};

export const DeleteIcon = ({label, ...props}) => {
    return (
        <span className={styles['iconContainer']}>
            <img src={assetUrl('DeleteIcon.svg')} alt={label} />
            <label>{label}</label>
        </span>
    );
};

export const DeletingIcon = ({label, ...props}) => {
    return (
        <span className={styles['iconContainer']}>
            <img src={assetUrl('DeletingIcon.svg')} alt={label} />
            <label>{label}</label>
        </span>
    );
};

export const PreparingIcon = ({label, ...props}) => {
    return (
        <span className={styles['iconContainer']}>
            <img src={assetUrl('PreparingIcon.svg')} alt={label} />
            <label>{label}</label>
        </span>
    );
};

export const InfoIcon = ({label, ...props}) => {
    return (
        <span className={styles['iconContainer']}>
            <img src={assetUrl('InfoIcon.svg')} alt={label} />
            <label>{label}</label>
        </span>
    );
};

export const CloseIcon = () => {
    return <img src={assetUrl('CloseIcon.svg')} alt="Close" />;
};
