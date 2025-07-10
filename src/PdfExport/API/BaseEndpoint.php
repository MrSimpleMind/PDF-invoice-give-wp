<?php

namespace GivePdfReceipt\PdfExport\API;

use Give\API\RestRoute;
use WP_Error;

/**
 * @since 2.4.0
 */
abstract class BaseEndpoint implements RestRoute
{
    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @since 2.4.0
     *
     * @return bool|WP_Error
     */
    public function permissionsCheck()
    {
        if ( ! current_user_can('export')) {
            return new WP_Error(
                'rest_forbidden',
                esc_html__('You don\'t have permission to access the PDF Export tool. Contact a site administrator and have them change your access level.',
                    'give-pdf-receipts'),
                ['status' => $this->authorizationStatusCode()]
            );
        }

        return true;
    }

    /**
     * @since 2.4.0
     */
    public function authorizationStatusCode(): int
    {
        if (is_user_logged_in()) {
            return 403;
        }

        return 401;
    }
}
