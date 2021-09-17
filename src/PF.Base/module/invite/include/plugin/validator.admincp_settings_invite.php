<?php

$aValidation = [
    'pendings_to_show_per_page'=>[
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"How Many Pendings To Show" must be greater than 0'),
    ]
];