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


    public static function toBool($var)
    {
        if(empty($var)) {
            return false; 
        }

        if(!is_string($var)) {
            return (bool)$var; 
        }

            switch (strtolower($var)) {
        case '1':
        case 'true':
        case 'on':
        case 'yes':
        case 'y':
            return true;
        default:
            return false;
            }

    }
}
