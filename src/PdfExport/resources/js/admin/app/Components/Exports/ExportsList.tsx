import React from 'react';
import styles from './ExportsList.module.scss';
import ExportsListItem from './ExportsListItem';

interface ExportsListProps {
    items: Object;
    setExportsList: Function;
}

const ExportsList = ({items, setExportsList}: ExportsListProps) => {
    return (
        <ul className={styles['list']}>
            {Object.keys(items)
                .reverse()
                .map((key, index) => (
                    <ExportsListItem
                        key={key}
                        item={items[key]}
                        setExportsList={setExportsList}
                        greyBackground={index % 2 !== 0}
                    />
                ))}
        </ul>
    );
};

export default ExportsList;
