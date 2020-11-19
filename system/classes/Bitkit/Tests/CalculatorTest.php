<?php

require 'Calculator.php';

class CalculatorTest extends \PHPUnit\Framework\TestCase
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
     * @dataProvider multiplyProvider
     */
    public function testMultiply($a,$b,$expected)
    {
        $result = $this->calculator->multiply($a, $b);
        $this->assertSame($expected, $result);
    }

    public function multiplyProvider()
    {
        return [
            'first_right'=>[2,2,4],
            'second_right'=>[4,3,12],
            'this_one_fail'=>[3,4,14],
            'its_ok'=>[5,3,15],
            'good'=>[3,5,15],
        ];
    }


}