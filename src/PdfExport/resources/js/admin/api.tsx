export const createExportByDonationIds = (donation_ids) => {
    if (!window.GivePdfReceiptsExportTool.zipEnabled) {
        window.location.href = window.GivePdfReceiptsExportTool.adminUrl;
        return;
    }
    const url = new URL(window.GivePdfReceiptsExportTool.apiEndpoints.create);
    for (const [param, value] of Object.entries(donation_ids)) {
        if (value !== '') {
            url.searchParams.set(param, value as string);
        }
    }
    return fetch(url.href, {
        method: 'POST',
        headers: {'X-WP-Nonce': window.GivePdfReceiptsExportTool.apiNonce},
    }).then((res) => {
        if (!res.ok) {
            throw new Error();
        }
        window.location.href = window.GivePdfReceiptsExportTool.adminUrl;
    });
};

export function createExportByDateInterval(start_date: Date, end_date: Date) {
    return {
        path: window.GivePdfReceiptsExportTool.apiEndpoints.create,
        method: 'POST',
        headers: {
            'X-WP-Nonce': window.GivePdfReceiptsExportTool.apiNonce,
        },
        data: {start_date: start_date.toISOString().split('T')[0], end_date: end_date.toISOString().split('T')[0]},
    };
}

export function deleteExport(id: string) {
    return {
        path: window.GivePdfReceiptsExportTool.apiEndpoints.delete,
        method: 'DELETE',
        headers: {
            'X-WP-Nonce': window.GivePdfReceiptsExportTool.apiNonce,
        },
        data: {id: id},
    };
}

export function getExportsList() {
    return {
        path: window.GivePdfReceiptsExportTool.apiEndpoints.list,
        method: 'GET',
        headers: {
            'X-WP-Nonce': window.GivePdfReceiptsExportTool.apiNonce,
        },
    };
}
