import {__} from '@wordpress/i18n';
import {createExportByDonationIds} from '../api';
import {Interweave} from 'interweave';

export const bulkActions = [
    {
        label: __('Download Receipts', 'give-pdf-receipts'),
        value: 'downloadEmailReceipt',
        action: async (selected) => await createExportByDonationIds({donation_ids: selected.join(',')}),
        confirm: (selected, names) => (
            <>
                <p>
                    {__(
                        'Select "Confirm" to prepare a ZIP containing receipts for the following donations. Depending on the number of donations selected, this can take several minutes. Feel free to navigate away from the page and check back later.',
                        'give-pdf-receipts'
                    )}
                </p>
                <ul role="document" tabIndex={0}>
                    {selected.map((donationId, index) => (
                        <li key={donationId}>
                            <div className={'idBadge'}>{donationId}</div>
                            <span>{__('from', 'give')}</span>
                            <Interweave content={names[index]} />
                        </li>
                    ))}
                </ul>
            </>
        ),
    },
];
