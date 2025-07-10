<?php

namespace GivePdfReceipt\PdfExport\Actions;

use DateTimeInterface;
use WP_Query;

/**
 * @since 2.4.0
 */
class GetPaginatedDonationsBetweenTwoDates
{
    /**
     * @since 2.4.0
     */
    public function __invoke(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        int $paged = 1,
        int $postsPerPage = 10,
        $order = 'ASC'
    ): WP_Query {
        $startDate = $startDate->getTimestamp();
        $endDate = $endDate->getTimestamp();
        $args = [
            'date_query' => [
                [
                    'after' => [
                        'year' => date('Y', $startDate),
                        'month' => date('m', $startDate),
                        'day' => date('d', $startDate),
                    ],
                    'before' => [
                        'year' => date('Y', $endDate),
                        'month' => date('m', $endDate),
                        'day' => date('d', $endDate),
                    ],
                    'inclusive' => true,
                ],
            ],
            'paged' => $paged,
            'posts_per_page' => $postsPerPage,
            'order' => $order,
            'post_type' => 'give_payment',
            'post_status' => 'publish',
        ];

        return new WP_Query($args);
    }
}
