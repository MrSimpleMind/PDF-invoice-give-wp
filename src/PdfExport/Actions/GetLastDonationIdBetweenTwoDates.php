<?php

namespace GivePdfReceipt\PdfExport\Actions;

use DateTimeInterface;

/**
 * @since 2.4.0
 */
class GetLastDonationIdBetweenTwoDates
{
    /**
     * @since 2.4.0
     */
    public function __invoke(DateTimeInterface $startDate, DateTimeInterface $endDate): int
    {
        $query = (new GetPaginatedDonationsBetweenTwoDates())($startDate, $endDate, 1, 1, 'DESC');

        if ( ! $query->have_posts()) {
            return 0;
        }

        return $query->posts[0]->ID;
    }
}
