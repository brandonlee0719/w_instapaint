<?php

namespace Core\Database;

class Database
{
    public static function run()
    {
        (new UploadTemp())->install();
    }
}