<?php

namespace GivePdfReceipt\PdfExport\API;

use GivePdfReceipt\PdfExport\Repositories\PdfExport;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * @since 2.4.0
 */
class ListExports extends BaseEndpoint
{
    /**
     * @var string
     */
    protected $endpoint = 'pdf-export/list';

    /**
     * @since 2.4.0
     */
    public function registerRoute()
    {
        register_rest_route('give-api/v2', $this->endpoint,
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this, 'handleRequest'],
                    'permission_callback' => [$this, 'permissionsCheck'],
                    'args' => [],
                ],
                'schema' => [$this, 'getSchema'],
            ]
        );
    }

    /**
     * @since 2.4.0
     */
    public function handleRequest(WP_REST_Request $request): WP_REST_Response
    {
        $exportsList = json_encode(give(PdfExport::class)->getExportsList());

        return new WP_REST_Response($exportsList);
    }

    /**
     * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#resource-schema
     *
     * @since 2.4.0
     */
    public function getSchema(): array
    {
        return [
            '$schema' => 'http://json-schema.org/draft-04/schema#',
            'title' => 'export',
            'type' => 'object',
            'properties' => [
                'id' => [
                    'description' => __('Unique identifier for the exported ZIP.', 'give-pdf-receipts'),
                    'type' => 'string',
                ],
                'preparing' => [
                    'description' => __('Indicates if the export is preparing or is ready for download.',
                        'give-pdf-receipts'),
                    'type' => 'boolean',
                ],
                'created_at' => [
                    'description' => __('The export creation date.', 'give-pdf-receipts'),
                    'type' => 'string',

                ],
                'file_name' => [
                    'description' => __('The export zip file name.',
                        'give-pdf-receipts'),
                    'type' => 'string',
                ],
                'file_url' => [
                    'description' => __('The export download URL.',
                        'give-pdf-receipts'),
                    'type' => 'string',
                ],
            ],
        ];
    }
}
