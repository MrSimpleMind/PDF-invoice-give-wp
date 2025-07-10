import React, {useState} from 'react';
import styles from './Form.module.scss';
import Button from './Button';
import {createExportByDateInterval} from '../../../api';
import apiFetch from '@wordpress/api-fetch';
import {InfoIcon} from '../Icons';
import {__} from '@wordpress/i18n';
import PeriodSelector from './PeriodSelector';

interface FormProps {
    setExportsList: Function;
    setShowModal: Function;
}

const Form = ({setExportsList, setShowModal}: FormProps) => {
    const [startDate, setStartDate] = useState(null);
    const [endDate, setEndDate] = useState(null);
    const [submitting, setSubmitting] = useState<boolean>(false);
    const locale = window.GivePdfReceiptsExportTool.locale;
    const zipEnabled = window.GivePdfReceiptsExportTool.zipEnabled;

    function handleSubmit(event) {
        event.preventDefault();
        setSubmitting(true);
        apiFetch(createExportByDateInterval(startDate, endDate)).then((response: string) => {
            setSubmitting(false);
            setShowModal(false);
            setStartDate(null);
            setEndDate(null);
            setExportsList(JSON.parse(response));
        });
        return false;
    }

    function setDates(startDate, endDate) {
        setStartDate(startDate);
        setEndDate(endDate);
    }

    return (
        <form className={styles['formContainer']} onSubmit={handleSubmit}>
            <fieldset className={styles['periodSelectorContainer']}>
                <PeriodSelector startDate={startDate} endDate={endDate} setDates={setDates} locale={locale} />
            </fieldset>
            <div className={styles['submitButtonContainer']}>
                {!zipEnabled || submitting || !startDate || !endDate ? (
                    <Button disabled={true}>Generate ZIP</Button>
                ) : (
                    <Button>Generate ZIP</Button>
                )}
            </div>
            <div className={styles['formNoticeWrapper']}>
                <InfoIcon
                    label={__(
                        'Donation receipts for all the donations within the selected date range will be generated. Depending on the number of donations it may take a while to generate the ZIP. You can navigate away from the page in the meantime and check back in later.',
                        'give-pdf-receipts'
                    )}
                />
            </div>
        </form>
    );
};

export default Form;
