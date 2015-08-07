<?php
namespace tests\helper;

use phphound\helper\ArrayHelper;

class ArrayHelperTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function ensure_returns_empty_array_if_value_is_not_an_array()
    {
        $this->assertEquals([], ArrayHelper::ensure(null));
        $this->assertEquals([], ArrayHelper::ensure('Invalid'));
        $this->assertEquals([], ArrayHelper::ensure(1904));
    }

    /** @test */
    public function ensure_returns_value_itself_if_it_is_an_array()
    {
        $this->assertEquals(['the', 'array'], ArrayHelper::ensure(['the', 'array']));
    }
}