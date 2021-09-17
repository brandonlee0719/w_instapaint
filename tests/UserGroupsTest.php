<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 8/02/18
 * Time: 3:29 PM
 */

use PHPUnit\Framework\TestCase;

require_once 'InstapaintDatabase.php';

final class UserGroupsTest extends TestCase
{
    private $dbConnection;

    public function __construct()
    {
        $instapaintDatabase = $this->dbConnection = new InstapaintDatabase();
        $this->dbConnection = $instapaintDatabase->dbConnection;
    }

    public function testClientUserGroupHasIdNumber6InDatabase()
    {
        $sql = "SELECT user_group_id FROM phpfox_user_group WHERE title = 'user_group_title_01dc0d4a6f3d002925cbe5292456df8a';";
        $result = $this->dbConnection->query($sql);
        $row = $result->fetch_assoc();

        $this->assertEquals(
            6,
            $row['user_group_id']
        );
    }

    public function testPainterUserGroupHasIdNumber7InDatabase()
    {
        $sql = "SELECT user_group_id FROM phpfox_user_group WHERE title = 'user_group_title_a27051d222eacb66877cd0c6e4f20190';";
        $result = $this->dbConnection->query($sql);
        $row = $result->fetch_assoc();

        $this->assertEquals(
            7,
            $row['user_group_id']
        );
    }

    public function testApprovedPainterUserGroupHasIdNumber8InDatabase()
    {
        $sql = "SELECT user_group_id FROM phpfox_user_group WHERE title = 'user_group_title_14ba355a1ca254460a0b75e1bc3fd456';";
        $result = $this->dbConnection->query($sql);
        $row = $result->fetch_assoc();

        $this->assertEquals(
            8,
            $row['user_group_id']
        );
    }
}
