<?php

require 'Calculator.php';

class DataTest extends \PHPUnit\Framework\TestCase
{
    private $calculator;

    protected function setUp() : void
    {
        $this->calculator = new Calculator();
    }

    protected function tearDown() : void
    {
        $this->calculator = NULL;
    }

    public function testAdd()
    {
        //получаем результат через экземпляр класса с тестовыми данными
        $result = $this->calculator->add(1, 2);
        //проверяем ожидание и реальность
        $this->assertEquals(3, $result);
    }


    /**
     * @dataProvider additionProvider
     */
    public function testMultiply($a, $b, $expected)
    {
        $this->assertSame($expected, $a * $b);
    }

    public function additionProvider()
    {
        return [
            [3, 4, 12],
            [4, 3, 14],
            [5, 3, 15],
            [3, 5, 15]
        ];
    }
}