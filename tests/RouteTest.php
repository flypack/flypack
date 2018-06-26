<?php

/**
 * RouteTest
 *
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 *
 * @author    Roman Rozinko <r.rozinko@gmail.com>
 * @since     0.2
 */

namespace fly\tests;

use PHPUnit\Framework\TestCase;
use fly\Route;

class RouteTest extends TestCase
{

    private $testConfig = [
        [
            'route' => '/^helloworld$/',
            'file' => __DIR__ . '/samples/route/inc-file-one.php',
        ],
        [
            'route' => '/^page$/i',
            'file' => __DIR__ . '/samples/route/inc-file-two.php',
            'data' => [
                'VAR_ONE' => 'reg2',
            ],
        ],
        [
            'route' => '/^p(\d+)$/i',
            'file' => __DIR__ . '/samples/route/inc-file-one.php',
            'data' => [
                'VAR_ONE' => 'reg3vars',
                'VAR_TWO' => '$1',
            ],
        ],
        [
            'route' => '/^test(\d+)$/i',
            'file' => __DIR__ . '/samples/route/inc-file-two.php',
            'data' => [
                'VAR_ONE' => 'reg4test',
                'VAR_TWO' => '$1',
            ],
        ],
        [
            'route' => '/^string\-(.)(.)(.)(.)(.)$/i',
            'file' => __DIR__ . '/samples/route/inc-file-two.php',
            'data' => [
                'VAR_ONE' => 'reg5.1',
                'VAR_TWO' => '$1-$2-$3-$4-$5',
            ],
        ],
        [
            'route' => '/^string\-(.)(.)(.)(.)(.)(.)(.)(.)(.)(.)(.)(.)(.)(.)(.)$/i',
            'file' => __DIR__ . '/samples/route/inc-file-two.php',
            'data' => [
                'VAR_ONE' => 'reg5.2',
                'VAR_TWO' => '$1-$2-$3-$4-$5-$6-$7-$8-$9-$10-$11-$12-$13-$14-$15',
            ],
        ],
        [
            'route' => '/^(.*)$/i',
            'file' => __DIR__ . '/samples/route/inc-file-two.php',
            'data' => [
                'VAR_ONE' => 'LAST',
                'VAR_TWO' => '$1',
            ],
        ],
    ];

    private $testConfigMultiFile = [
        [
            'route' => '/^test-multi-one$/',
            'file' => [
                __DIR__ . '/samples/route/inc-file-one.php',
            ],
        ],
        [
            'route' => '/^test-multi-two$/',
            'file' => [
                __DIR__ . '/samples/route/inc-file-one.php',
                __DIR__ . '/samples/route/inc-file-two.php',
            ],
        ],
        [
            'route' => '/^test-multi-three$/',
            'file' => [
                __DIR__ . '/samples/route/inc-file-one.php',
                __DIR__ . '/samples/route/inc-file-two.php',
                __DIR__ . '/samples/route/inc-file-two.php',
            ],
        ],
        [
            'route' => '/^test-multi-two-with-vars$/',
            'file' => [
                __DIR__ . '/samples/route/inc-file-one.php',
                __DIR__ . '/samples/route/inc-file-two.php',
            ],
            'data' => [
                'VAR_ONE' => 'reg7',
            ],
        ],
        [
            'route' => '/^test-multi-two-with-vars-(\d+)$/',
            'file' => [
                __DIR__ . '/samples/route/inc-file-one.php',
                __DIR__ . '/samples/route/inc-file-two.php',
            ],
            'data' => [
                'VAR_ONE' => 'reg8',
                'VAR_TWO' => '$1',
            ],
        ],
    ];

    /**
     * @param array  $config
     * @param string $route
     *
     * @return string
     * @throws \Exception
     */
    private function getContentAfterRoute($config, $route)
    {
        $_GET['route'] = $route;
        ob_start();
        Route::Init($config);
        return ob_get_clean();
    }


    /* test Route::Init() */

    /**
     * @param $query
     * @param $expected
     *
     * @dataProvider dataProviderInit
     *
     * @throws \Exception
     */
    public function testInit($query, $expected)
    {
        $this->assertEquals($expected, $this->getContentAfterRoute($this->testConfig, $query));
    }

    /**
     * @return array of [query, result]
     */
    public function dataProviderInit()
    {
        return array(
            array('helloworld', 'INCLUDE ONE - _NO_VAR_ONE_'),
            array('page', 'INCLUDE TWO - reg2_NO_VAR_TWO_'),
            array('p300', 'INCLUDE ONE - reg3vars'),
            array('test550', 'INCLUDE TWO - reg4test550'),
            array('string-HGFDS', 'INCLUDE TWO - reg5.1H-G-F-D-S'),
            array('string-HGFDSZXCVBQWERT', 'INCLUDE TWO - reg5.2H-G-F-D-S-Z-X-C-V-B-Q-W-E-R-T'),
        );
    }

    /**
     * @param $query
     * @param $expected
     *
     * @dataProvider dataProviderInitMultiFile
     *
     * @throws \Exception
     */
    public function testInitMultiFile($query, $expected)
    {
        $this->assertEquals($expected, $this->getContentAfterRoute($this->testConfigMultiFile, $query));
    }

    /**
     * @return array of [query, result]
     */
    public function dataProviderInitMultiFile()
    {
        return array(
            array('test-multi-one', 'INCLUDE ONE - _NO_VAR_ONE_'),
            array('test-multi-two', 'INCLUDE ONE - _NO_VAR_ONE_INCLUDE TWO - _NO_VAR_ONE__NO_VAR_TWO_'),
            array('test-multi-three', 'INCLUDE ONE - _NO_VAR_ONE_INCLUDE TWO - _NO_VAR_ONE__NO_VAR_TWO_INCLUDE TWO - _NO_VAR_ONE__NO_VAR_TWO_'),
            array('test-multi-two-with-vars', 'INCLUDE ONE - reg7INCLUDE TWO - reg7_NO_VAR_TWO_'),
            array('test-multi-two-with-vars-725', 'INCLUDE ONE - reg8INCLUDE TWO - reg8725'),
        );
    }

    /**
     * @throws \Exception
     */
    public function testGetQuery()
    {
        $this->getContentAfterRoute($this->testConfig, 'p123');
        $this->assertEquals('p123', Route::getRoute());
    }

    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Route::Init(): Expects parameter 1 to be a valid config array
     *
     * @throws \Exception
     */
    public function testExceptionOnInvalidConfig()
    {
        Route::Init('123');
    }


    /**
     * @expectedException        \Exception
     * @expectedExceptionMessage Route::Init(): No route file exists
     *
     * @throws \Exception
     */
    public function testExceptionOnInvalidFilePath()
    {
        $_GET['route'] = 'helloworld';
        Route::Init([
            [
                'route' => '/^helloworld$/',
                'file' => __DIR__ . '/samples/route/no-file-exists.php',
            ],
        ]);
    }

    /**
     * @throws \Exception
     */
    public function testCustomPageForError404()
    {
        $config = [
            [
                'route' => '/^helloworld$/',
                'file' => __DIR__ . '/samples/route/inc-file-one.php',
            ],
            'Error404' => __DIR__ . '/samples/route/inc-error-404.php',
        ];

        $this->assertEquals('ERROR PAGE NOT FOUND 404', $this->getContentAfterRoute($config, 'qwe789'));
    }

}
