<?php

if (!empty($aBreadCrumbs) && empty($aBreadCrumbTitle)) {
    list($value, $key) = array(end($aBreadCrumbs), key($aBreadCrumbs));
    unset($aBreadCrumbs[$key]);
    $aBreadCrumbTitle = [$value, $key, 1];
}