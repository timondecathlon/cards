<?php

require '../Core/Patterns/Singleton.php';
require '../Core/Database/Connect.php';

class ConnectTest  extends \PHPUnit\Framework\TestCase
{
    private $connect;

    protected function setUp() : void
    {
        //$this->connect = new \Bitkit\Core\Database\Connect();
    }

    protected function tearDown() : void
    {
        $this->connect = NULL;
    }

    public function testGetInstance()
    {
        $instance_1 = \Bitkit\Core\Database\Connect::getInstance();
        $instance__2 = \Bitkit\Core\Database\Connect::getInstance();
        $this->assertSame($instance_1,$instance__2);

        return $instance_1;
    }

    /**
     * @depends testGetInstance
     */
    public function testGetConnection($instance)
    {

        define("DB_HOST", 'localhost');
        define("DB_NAME", 'cards');
        define("DB_USER", 'timon');
        define("DB_PASSWORD", '20091993dec');

        $connection_1 = $instance->getConnection();
        $connection_2 = $instance->getConnection();
        $this->assertSame($connection_1,$connection_2);
    }
}