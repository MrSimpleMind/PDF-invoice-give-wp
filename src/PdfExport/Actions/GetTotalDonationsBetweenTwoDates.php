<?php

namespace GivePdfReceipt\PdfExport\Actions;

use DateTimeInterface;

/**
 * @since 2.4.0
 */
class GetTotalDonationsBetweenTwoDates
{
    /**
     * @since 2.4.0
     */
    public function __invoke(DateTimeInterface $startDate, DateTimeInterface $endDate): int
    {
        $query = (new GetPaginatedDonationsBetweenTwoDates())($startDate, $endDate, 1, 1);

        return $query->found_posts;
    }
}
