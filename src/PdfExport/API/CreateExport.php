<?php

namespace GivePdfReceipt\PdfExport\API;

use Give\Framework\Support\Facades\DateTime\Temporal;
use GivePdfReceipt\PdfExport\Repositories\PdfExport;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * @since 2.4.0
 */
class CreateExport extends BaseEndpoint
{
    /**
     * @var string
     */
    protected $endpoint = 'pdf-export/create';

    /**
     * @since 2.4.0
     */
    public function registerRoute()
    {
        register_rest_route('give-api/v2', $this->endpoint,
            [
                [
                    'methods' => WP_REST_Server::CREATABLE,
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
            'start_date' => [
                'description' => __('The start date of the export. Expected Format: YYYY-MM-DD', 'give-pdf-receipts'),
                'type' => 'string',
                'required' => false,
                'validate_callback' => [$this, 'validateDate'],
                'sanitize_callback' => [$this, 'sanitizeDate'],
            ],
            'end_date' => [
                'description' => __('The end date of the export. Expected Format: YYYY-MM-DD', 'give-pdf-receipts'),
                'type' => 'string',
                'required' => false,
                'validate_callback' => [$this, 'validateDate'],
                'sanitize_callback' => [$this, 'sanitizeDate'],
            ],
            'donation_ids' => [
                'description' => __('The donation IDs to export. If set, the start date and the end date will be ignored.',
                    'give-pdf-receipts'),
                'type' => 'array',
                'required' => false,
            ],
        ];
    }

    /**
     * @since 2.4.0
     *
     * @return WP_REST_Response|WP_Error
     */
    public function handleRequest(WP_REST_Request $request)
    {
        $donationIds = $request->get_param('donation_ids');
        $donationIds = $donationIds ? $this->sanitizeArray($donationIds) : [];
        $startDate = $request->get_param('start_date');
        $endDate = $request->get_param('end_date');

        if (empty($donationIds) && ( ! $startDate || ! $endDate)) {
            return new WP_Error(
                'rest_forbidden',
                esc_html__('The export request needs either donation IDs or start/end dates to create a ZIP of receipts. Your request did not have one or the other.',
                    'give-pdf-receipts'),
                422
            );
        }

        $response = false;

        if ( ! empty($donationIds)) {
            if (give(PdfExport::class)->createExportByDonationIds($donationIds)) {
                $response = json_encode(give(PdfExport::class)->getExportsList());
            }
        } elseif ($startDate && $endDate) {
            $startDate = Temporal::toDateTime($startDate);
            $endDate = Temporal::toDateTime($endDate);
            if (give(PdfExport::class)->createExportByDateInterval($startDate, $endDate)) {
                $response = json_encode(give(PdfExport::class)->getExportsList());
            }
        }

        return new WP_REST_Response($response);
    }

    /**
     * @since 2.4.0
     */
    public function validateDate(string $param, WP_REST_Request $request, string $key): bool
    {
        list($year, $month, $day) = explode('-', $param);
        $valid = checkdate($month, $day, $year);

        if ('end_date' === $key) {
            $start = date_create($request->get_param('start_date'));
            $end = date_create($request->get_param('end_date'));
            $valid = $start <= $end ? $valid : false;
        }

        return $valid;
    }

    /**
     * @since 2.4.0
     */
    public function sanitizeDate(string $param, WP_REST_Request $request, string $key): string
    {
        $exploded = explode('-', $param);

        $sanitizedDate = "{$exploded[0]}-{$exploded[1]}-{$exploded[2]}";

        if ('start_date' === $key) {
            $sanitizedDate .= ' 00:00:00';
        }

        if ('end_date' === $key) {
            $sanitizedDate .= ' 23:59:59';
        }

        return $sanitizedDate;
    }

    /**
     * @since 2.4.0
     */
    public function sanitizeArray(array $arr): array
    {
        $sanitizedArr = [];
        if ($arr) {
            foreach ($arr as $key => $value) {
                $sanitizedArr[$key] = sanitize_text_field($value);
            }
        }

        return $sanitizedArr;
    }
}
