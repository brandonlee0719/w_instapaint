<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 8/02/18
 * Time: 12:40 PM
 */

use PHPUnit\Framework\TestCase;

require_once 'InstapaintDatabase.php';

final class UserTypeTest extends TestCase
{
    private $dbConnection;
    
    public function __construct()
    {
        $instapaintDatabase = $this->dbConnection = new InstapaintDatabase();
        $this->dbConnection = $instapaintDatabase->dbConnection;
    }

    public function testUserTypeFieldHasIdNumber2InDatabase()
    {
        $sql = "SELECT field_id FROM phpfox_custom_field WHERE field_name = 'user_type';";
        $result = $this->dbConnection->query($sql);
        $row = $result->fetch_assoc();

        $this->assertEquals(
            2,
            $row['field_id']
        );
    }

    public function testUserTypeFieldClientOptionHasIdNumber1InDatabase()
    {
        $sql = "SELECT option_id FROM phpfox_custom_option WHERE phrase_var_name = 'custom.cf_option_2_1';";
        $result = $this->dbConnection->query($sql);
        $row = $result->fetch_assoc();

        $this->assertEquals(
            1,
            $row['option_id']
        );
    }

    public function testUserTypeFieldPainterOptionHasIdNumber2InDatabase()
    {
        $sql = "SELECT option_id FROM phpfox_custom_option WHERE phrase_var_name = 'custom.cf_option_2_2';";
        $result = $this->dbConnection->query($sql);
        $row = $result->fetch_assoc();

        $this->assertEquals(
            2,
            $row['option_id']
        );

    }
}