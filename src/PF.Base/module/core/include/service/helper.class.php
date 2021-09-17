<?php

class Core_Service_Helper extends Phpfox_Service
{

    /**
     * Get format number string
     *
     * @param integer $number
     *
     * @return string
     * @since 4.6.0
     * @author OvalSky
     */
    public function shortNumber($number)
    {
        $n_format =  '';
        $suffix = '';
        if ($number > 0 && $number < 1000) {
            // 1 - 999
            $n_format = floor($number);
            $suffix = '';
        } else if ($number >= 1000 && $number < 1000000) {
            // 1k-999k
            $n_format = floor($number / 1000);
            $suffix = 'shorten_K+';
        } else if ($number >= 1000000 && $number < 1000000000) {
            // 1m-999m
            $n_format = floor($number / 1000000);
            $suffix = 'shorten_M+';
        } else if ($number >= 1000000000 && $number < 1000000000000) {
            // 1b-999b
            $n_format = floor($number / 1000000000);
            $suffix = 'shorten_B+';
        } else if ($number >= 1000000000000) {
            // 1t+
            $n_format = floor($number / 1000000000000);
            $suffix = 'shorten_T+';
        }

        if(!empty($suffix)){
            $suffix =  _p($suffix);
        }

        return !empty($n_format . $suffix) ? $n_format . $suffix : 0;

    }

    /**
     * @param string $class
     * @param string $asc
     * @param string $desc
     * @param string $sorting
     * @param string $query
     * @param string $first
     *
     * @return string
     */
    public function tableSort($class, $asc, $desc, $sorting, $query, $first)
    {
        $sorting = strtolower(empty($sorting) ? Phpfox::getLib('search')->getSort() : $sorting);
        $tableSort = [
            'first'   => strtolower($first ? $first : 'asc'),
            'asc'     => $asc,
            'desc'    => $desc,
            'query'   => $query,
            'sorting' => $sorting,
        ];

        if (strtolower($asc) == $sorting) {
            $status = 'asc';
        } elseif (strtolower($desc) == $sorting) {
            $status = 'desc';
        } else {
            $status = '';
        }

        return ' data-cmd="core.table_sort" class="' . $class . ' sortable ' . $status . '" data-table_sort="' . implode('|',
                $tableSort) . '"';
    }
}
