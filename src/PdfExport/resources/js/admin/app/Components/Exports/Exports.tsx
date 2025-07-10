import React from 'react';
import styles from './Exports.module.scss';
import ExportsHeader from './ExportsHeader';
import ExportsList from './ExportsList';
import ExportsEmptyList from './ExportsEmptyList';

interface ExportsProps {
    exportsList: Object;
    setExportsList: Function;
    setShowModal: Function;
}

const Exports = ({exportsList, setExportsList, setShowModal}: ExportsProps) => {
    return (
        <section className={styles['container']}>
            <ExportsHeader />
            {Object.keys(exportsList).length ? (
                <ExportsList setExportsList={setExportsList} items={exportsList} />
            ) : (
                <ExportsEmptyList setShowModal={setShowModal} />
            )}
        </section>
    );
};
export default Exports;
