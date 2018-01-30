<?php

/**
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 */

namespace fly\helpers\tests;

use fly\helpers\ArrayHelper;

/**
 * @author Roman Rozinko <r.rozinko@gmail.com>
 * @since  0.1
 */
class ArrayHelperTest extends \PHPUnit_Framework_TestCase
{

    /* test ArrayHelper::isArray() */

    /**
     * @param $array
     * @param $expected
     *
     * @dataProvider dataProviderIsArray
     */
    public function testIsArray($array, $expected)
    {
        $this->assertEquals($expected, ArrayHelper::isArray($array));
    }

    /**
     * @return array of [array, expected]
     */
    public function dataProviderIsArray()
    {
        return array(
            array(1, FALSE),
            array(TRUE, FALSE),
            array('string', FALSE),
            array(array(), TRUE),
            array(array(123, 456, 789), TRUE),
            array(array(array(1, 2), array(3, 4)), TRUE),
        );
    }


    /* test ArrayHelper::isArrayXD() */

    /**
     * @param $array
     * @param $level
     * @param $expected
     *
     * @dataProvider dataProviderIsArrayXD
     *
     * @throws \Exception
     */
    public function testIsArrayXD($array, $level, $expected)
    {
        $this->assertEquals($expected, ArrayHelper::isArrayXD($array, $level));
    }

    /**
     * @return array of [array, level, expected]
     */
    public function dataProviderIsArrayXD()
    {
        return array(
            array(1, 1, FALSE),
            array(TRUE, 2, FALSE),
            array('string', 4, FALSE),
            array(array(), 1, TRUE),
            array(array(123, 456, 789), 2, FALSE),
            array(array(array(1, 2), array(3, 4)), 2, TRUE),
            array(array(array(1, 2), array(3, array())), 3, TRUE),
            array(array(array(1, 2), array(3, array(array()))), 4, TRUE),
            array(array(array(1, 2), array(3, array(array()))), 5, FALSE),
        );
    }


    /* test ArrayHelper::isArray2D() */

    /**
     * @param $array
     * @param $expected
     *
     * @dataProvider dataProviderIsArray2D
     *
     * @throws \Exception
     */
    public function testIsArray2D($array, $expected)
    {
        $this->assertEquals($expected, ArrayHelper::isArray2D($array));
    }

    /**
     * @return array of [array, expected]
     */
    public function dataProviderIsArray2D()
    {
        return array(
            array(1, FALSE),
            array(TRUE, FALSE),
            array('string', FALSE),
            array(array(), FALSE),
            array(array(123, 456, 789), FALSE),
            array(array(array(1, 2), 'str'), TRUE),
            array(array(100, array()), TRUE),
            array(array(array(1, 2), array(3, 4)), TRUE),
            array(array(array(1, 2), array(3, array())), TRUE),
            array(array(array(1, 2), array(3, array(array()))), TRUE),
            array(array('str', array(3, array(array()))), TRUE),
        );
    }


    /* test ArrayHelper::isArray3D() */

    /**
     * @param $array
     * @param $expected
     *
     * @dataProvider dataProviderIsArray3D
     *
     * @throws \Exception
     */
    public function testIsArray3D($array, $expected)
    {
        $this->assertEquals($expected, ArrayHelper::isArray3D($array));
    }

    /**
     * @return array of [array, expected]
     */
    public function dataProviderIsArray3D()
    {
        return array(
            array(1, FALSE),
            array(TRUE, FALSE),
            array('string', FALSE),
            array(array(), FALSE),
            array(array(123, 456, 789), FALSE),
            array(array(array(1, 2), 'str'), FALSE),
            array(array(100, array()), FALSE),
            array(array(array(1, 2), array(3, 4)), FALSE),
            array(array(array(1, 2), array(3, array())), TRUE),
            array(array(array(1, 2), array(3, array(array()))), TRUE),
            array(array('str', array(3, array(array()))), TRUE),
        );
    }


    /* test ArrayHelper::isArrayStrong() */

    /**
     * @param $array
     * @param $expected
     *
     * @dataProvider dataProviderIsArrayStrong
     */
    public function testIsArrayStrong($array, $expected)
    {
        $this->assertEquals($expected, ArrayHelper::isArrayStrong($array));
    }

    /**
     * @return array of [array, expected]
     */
    public function dataProviderIsArrayStrong()
    {
        return array(
            array(1, FALSE),
            array(TRUE, FALSE),
            array('string', FALSE),
            array(array(), TRUE),
            array(array(123, 456, 789), TRUE),
            array(array(500, array(3, 4)), FALSE),
            array(array(array(1, 2), array(3, 4)), FALSE),
        );
    }


    /* test ArrayHelper::isArrayXDStrong() */

    /**
     * @param $array
     * @param $level
     * @param $expected
     *
     * @dataProvider dataProviderIsArrayXDStrong
     *
     * @throws \Exception
     */
    public function testIsArrayXDStrong($array, $level, $expected)
    {
        $this->assertEquals($expected, ArrayHelper::isArrayXDStrong($array, $level));
    }

    /**
     * @return array of [array, level, expected]
     */
    public function dataProviderIsArrayXDStrong()
    {
        return array(
            array(1, 1, FALSE),
            array(TRUE, 2, FALSE),
            array('string', 4, FALSE),
            array(array(), 1, TRUE),
            array(array(123, 456, 789), 2, FALSE),
            array(array(array(), array()), 2, TRUE),
            array(array(array()), 3, FALSE),
            array(array(array(), array(array())), 3, FALSE),
            array(array(array(1, 2), array(3, 4)), 2, TRUE),
            array(array(array(1, 2), array(3, array())), 3, FALSE),
            array(array(array(1, 2), array(3, array(array()))), 4, FALSE),
            array(array(array(1, 2), array(3, array(array()))), 5, FALSE),
            array(array(array(array(1, 2), array(3, 4)), array(array(5, 6), array(7, 8)), array(array(9, 0))), 3, TRUE)
        );
    }


    /* test ArrayHelper::isArray2DStrong() */

    /**
     * @param $array
     * @param $expected
     *
     * @dataProvider dataProviderIsArray2DStrong
     *
     * @throws \Exception
     */
    public function testIsArray2DStrong($array, $expected)
    {
        $this->assertEquals($expected, ArrayHelper::isArray2DStrong($array));
    }

    /**
     * @return array of [array, expected]
     */
    public function dataProviderIsArray2DStrong()
    {
        return array(
            array(1, FALSE),
            array(TRUE, FALSE),
            array('string', FALSE),
            array(array(), FALSE),
            array(array(123, 456, 789), FALSE),
            array(array(array(1, 2), 'str'), FALSE),
            array(array(100, array()), FALSE),
            array(array(array(1, 2), array(3, 4)), TRUE),
            array(array(array(1, 2), array(3, array())), FALSE),
            array(array(array(1, 2), array(3, array(array()))), FALSE),
            array(array('str', array(3, array(array()))), FALSE),
        );
    }


    /* test ArrayHelper::isArray3DStrong() */

    /**
     * @param $array
     * @param $expected
     *
     * @dataProvider dataProviderIsArray3DStrong
     *
     * @throws \Exception
     */
    public function testIsArray3DStrong($array, $expected)
    {
        $this->assertEquals($expected, ArrayHelper::isArray3DStrong($array));
    }

    /**
     * @return array of [array, expected]
     */
    public function dataProviderIsArray3DStrong()
    {
        return array(
            array(1, FALSE),
            array(TRUE, FALSE),
            array('string', FALSE),
            array(array(), FALSE),
            array(array(123, 456, 789), FALSE),
            array(array(array(1, 2), 'str'), FALSE),
            array(array(100, array()), FALSE),
            array(array(array(), array()), FALSE),
            array(array(array(1, 2), array(3, 4)), FALSE),
            array(array(array(1, 2), array(3, array())), FALSE),
            array(array(array(1, 2), array(3, array(array()))), FALSE),
            array(array('str', array(3, array(array()))), FALSE),
            array(array(array(array(), array()), array(array(), array())), TRUE),
            array(array(array(array(), array()), array(array(array()))), FALSE),
            array(array(array(array(), array()), array(array(), array(array()))), FALSE),
        );
    }


    /* test ArrayHelper::isArrayXDStrongMin() */

    /**
     * @param $array
     * @param $level
     * @param $expected
     *
     * @dataProvider dataProviderIsArrayXDStrongMin
     *
     * @throws \Exception
     */
    public function testIsArrayXDStrongMin($array, $level, $expected)
    {
        $this->assertEquals($expected, ArrayHelper::isArrayXDStrongMin($array, $level));
    }

    /**
     * @return array of [array, level, expected]
     */
    public function dataProviderIsArrayXDStrongMin()
    {
        return array(
            array(1, 1, FALSE),
            array(TRUE, 2, FALSE),
            array('string', 4, FALSE),
            array(array(), 1, TRUE),
            array(array(123, 456, 789), 2, FALSE),
            array(array(array(), array()), 2, TRUE),
            array(array(array()), 3, FALSE),
            array(array(array(), array(array())), 3, FALSE),
            array(array(array(1, 2), array(3, 4)), 2, TRUE),
            array(array(array(1, 2), array(3, array(3, 5))), 2, TRUE),
            array(array(array(1, 2), array(3, array())), 3, FALSE),
            array(array(array(1, 2), array(3, array(array()))), 4, FALSE),
            array(array(array(1, 2), array(3, array(array()))), 5, FALSE),
            array(array(array(array(1, 2), array(3, 4)), array(array(5, 6), array(7, 8)), array(array(9, 0))), 3, TRUE)
        );
    }


    /* test ArrayHelper::isArray2DStrongMin() */

    /**
     * @param $array
     * @param $expected
     *
     * @dataProvider dataProviderIsArray2DStrongMin
     *
     * @throws \Exception
     */
    public function testIsArray2DStrongMin($array, $expected)
    {
        $this->assertEquals($expected, ArrayHelper::isArray2DStrongMin($array));
    }

    /**
     * @return array of [array, expected]
     */
    public function dataProviderIsArray2DStrongMin()
    {
        return array(
            array(1, FALSE),
            array(TRUE, FALSE),
            array('string', FALSE),
            array(array(), FALSE),
            array(array(123, 456, 789), FALSE),
            array(array(array(1, 2), 'str'), FALSE),
            array(array(100, array()), FALSE),
            array(array(array(1, 2), array(3, 4)), TRUE),
            array(array(array(1, 2), array(3, array())), TRUE),
            array(array(array(1, 2), array(3, array(array()))), TRUE),
            array(array('str', array(3, array(array()))), FALSE),
        );
    }


    /* test ArrayHelper::isArray3DStrongMin() */

    /**
     * @param $array
     * @param $expected
     *
     * @dataProvider dataProviderIsArray3DStrongMin
     *
     * @throws \Exception
     */
    public function testIsArray3DStrongMin($array, $expected)
    {
        $this->assertEquals($expected, ArrayHelper::isArray3DStrongMin($array));
    }

    /**
     * @return array of [array, expected]
     */
    public function dataProviderIsArray3DStrongMin()
    {
        return array(
            array(1, FALSE),
            array(TRUE, FALSE),
            array('string', FALSE),
            array(array(), FALSE),
            array(array(123, 456, 789), FALSE),
            array(array(array(1, 2), 'str'), FALSE),
            array(array(100, array()), FALSE),
            array(array(array(), array()), FALSE),
            array(array(array(1, 2), array(3, 4)), FALSE),
            array(array(array(1, 2), array(3, array())), FALSE),
            array(array(array(1, 2), array(3, array(array()))), FALSE),
            array(array('str', array(3, array(array()))), FALSE),
            array(array(array(array(), array()), array(array(), array())), TRUE),
            array(array(array(array(), array()), array(array(array()))), TRUE),
            array(array(array(array(), array()), array(array(), array(array()))), TRUE),
        );
    }

}
