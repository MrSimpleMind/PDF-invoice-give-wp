<?php

namespace GivePdfReceipt\PdfExport\API;

use GivePdfReceipt\PdfExport\Repositories\PdfExport;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * @since 2.4.0
 */
class DeleteExport extends BaseEndpoint
{
    /**
     * @var string
     */
    protected $endpoint = 'pdf-export/delete';

    /**
     * @since 2.4.0
     */
    public function registerRoute()
    {
        register_rest_route('give-api/v2', $this->endpoint,
            [
                [
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => [$this, 'handleRequest'],
                    'permission_callback' => [$this, 'permissionsCheck'],
                    'args' => $this->getArguments(),
                ],
            ]
        );
    }

    /**
     * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
     *
     * @since 2.4.0
     */
    public function getArguments(): array
    {
        return [
            'id' => [
                'description' => __('The ID of the export ZIP to delete.', 'give-pdf-receipts'),
                'type' => 'string',
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ],
        ];
    }

    /**
     * @since 2.4.0
     */
    public function handleRequest(WP_REST_Request $request): WP_REST_Response
    {
        $exportId = $request->get_param('id');

        $exportDeleted = false;

        if (give(PdfExport::class)->deleteExport($exportId)) {
            $exportDeleted = json_encode(give(PdfExport::class)->getExportsList());
        }

        return new WP_REST_Response($exportDeleted);
    }
}
