import {useEffect, useState} from 'react';
import Exports from './Components/Exports/Exports';
import Modal from './Components/Modal';
import Button from './Components/Form/Button';
import Form from './Components/Form/Form';
import {getExportsList} from '../api';
import apiFetch from '@wordpress/api-fetch';
import {NewIcon} from './Components/Icons';
import {__} from '@wordpress/i18n';

function hasPreparingItems(exportsList) {
    return Boolean(Object.keys(exportsList).filter((key) => exportsList[key].preparing).length);
}
function App() {
    const [showModal, setShowModal] = useState(false);
    const [exportsList, setExportsList] = useState(JSON.parse(window.GivePdfReceiptsExportTool.exportsList));

    useEffect(() => {
        if (!hasPreparingItems(exportsList)) {
            return;
        }

        let interval = setInterval(() => {
            apiFetch(getExportsList()).then((response: string) => {
                setExportsList(JSON.parse(response));
            });
        }, 10000);

        return () => {
            clearInterval(interval);
        };
    }, [exportsList]);

    return (
        <div className="App">
            {window.GivePdfReceiptsExportTool.zipEnabled ? (
                <Button small={true} onClick={() => setShowModal(true)}>
                    <NewIcon label={__('Generate New ZIP', 'give-pdf-receipts')} />
                </Button>
            ) : (
                <Button disabled={true} small={true}>
                    <NewIcon label={__('Generate New ZIP', 'give-pdf-receipts')} />
                </Button>
            )}

            <Exports exportsList={exportsList} setExportsList={setExportsList} setShowModal={setShowModal} />

            <Modal
                title={__('Generate an archive ZIP of receipts', 'give-pdf-receipts')}
                showModal={showModal}
                setShowModal={setShowModal}
            >
                <Form setExportsList={setExportsList} setShowModal={setShowModal} />
            </Modal>
        </div>
    );
}

export default App;
