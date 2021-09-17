<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 8/02/18
 * Time: 1:52 PM
 */

class InstapaintDatabase
{
    public $dbConnection;
    
    public function __construct()
    {
        $dbHost = "127.0.0.1";
        $dbUser = "root";
        $dbPassword = "mysql";
        $dbDatabase = "instapaint";
        $dbPort = 1234;

        $dbConnection = new mysqli($dbHost, $dbUser, $dbPassword, $dbDatabase, $dbPort);
        
        $this->dbConnection = $dbConnection;
    }
}
