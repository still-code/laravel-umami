<?php

namespace Umami;

if (! function_exists('Umami\formatDate')) {
    /**
     * set the Carbon dates and convert them to milliseconds.
     */
    function formatDate($data): float|int|string|null
    {
        if (is_numeric($data)) {
            return $data;
        }

        if (is_object($data)) {
            return $data->getTimestampMs();
        }

        return null;
    }
}
