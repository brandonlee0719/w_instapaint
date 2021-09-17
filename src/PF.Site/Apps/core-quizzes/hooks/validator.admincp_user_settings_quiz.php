<?php

$aValidation = [
    'max_questions' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Max questions can a new quiz have" must be greater than 0'),
    ],
    'min_questions' => [
        'def' => 'int:required',
        'min' => '1',
        'title' => _p('"Min questions can a new quiz have" must be greater than 0'),
    ],
    'max_answers' => [
        'def' => 'int:required',
        'min' => '2',
        'title' => _p('"Max answers each question in a quiz have" must be greater than 1'),
    ],
    'min_answers' => [
        'def' => 'int:required',
        'min' => '2',
        'title' => _p('"Min answers each question in a quiz have" must be greater than 1'),
    ],
    'points_quiz' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Activity Points" must be greater or equal to 0'),
    ],
    'quiz_max_upload_size' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Max file size for quiz photo" must be greater or equal to 0'),
    ],
    'quiz_sponsor_price' => [
        'def' => 'currency',
        'min' => '0',
        'title' => _p('"Sponsor quiz price" must be greater than or equal to 0'),
    ],
];
