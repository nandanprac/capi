<?php

namespace ConsultBundle\Utils;

/**
 * General purpose utility functions.
 */
class Utility
{

    /**
     * Build query.
     *
     * @param Array $params - Query params
     *
     * @return Array
     */
    public static function buildQuery($params)
    {
        $queryArray = array();
        foreach ($params as $key => $value) {
            if ($value === true) {
                $value = 'true';
                $queryArray[] = $key . '=' . $value;
            } elseif ($value === false) {
                $value = 'false';
                $queryArray[] = $key . '=' . $value;
            } else {
                $queryArray[] = $key . '=' . urlencode($value);
            }
        }
        $query = implode('&', $queryArray);

        return $query;
    }
}
