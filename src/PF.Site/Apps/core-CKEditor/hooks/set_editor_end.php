<?php
$sPackage = setting('ckeditor_package');

switch ($sPackage) {
    case 'full':
        $file = 'ckeditor_full/ckeditor.js';
        break;
    case 'basic':
        $file = 'ckeditor_basic/ckeditor.js';
        break;
    default:
        $file = 'ckeditor/ckeditor.js';
        break;
}
$this->setHeader([
    $file => 'app_core-CKEditor'
]);
