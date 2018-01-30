<?php

/**
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 */

namespace fly\helpers\tests;

use fly\helpers\FileHelper;

/**
 * @author Roman Rozinko <r.rozinko@gmail.com>
 * @since  0.1
 */
class FileHelperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var $filepath - temp filepath for tests
     */
    private $filepath;


    /**
     * Create temp file for tests
     */
    protected function setUp()
    {
        $this->filepath = tempnam(sys_get_temp_dir(), 'php-');
    }

    /**
     * Delete temp file
     */
    protected function tearDown()
    {
        unlink($this->filepath);
    }


    /* test FileHelper::checkExists() */

    /**
     * @param $path
     * @param $expected
     *
     * @dataProvider dataProviderCheckExists1
     *
     * @throws \Exception
     */
    public function testCheckExists1($path, $expected)
    {
        $this->assertEquals($expected, FileHelper::checkExists($path));
    }

    /**
     * @return array of [path, result]
     */
    public function dataProviderCheckExists1()
    {
        return array(
            array(__DIR__, TRUE),
            array(__DIR__ . '/../samples', TRUE),
            array(__DIR__ . '/../samples/', TRUE),
            array(__DIR__ . '/../samples/../samples', TRUE),
            array(__DIR__ . '/../samples/../samples/helpers/file-helper', TRUE),
            array(__DIR__ . '/../samples/sample-no-exists', FALSE),
            array(__DIR__ . '/../samples/helpers/file-helper/sample-one.txt', TRUE),
            array(__DIR__ . '/../samples/helpers/file-helper/sample-one-no-exist.txt', FALSE),
        );
    }

    /**
     * @param $path
     *
     * @dataProvider dataProviderCheckExists2
     * @expectedException \Exception
     *
     * @throws \Exception
     */
    public function testCheckExists2($path)
    {
        FileHelper::checkExists($path);
    }

    /**
     * @return array of [path]
     */
    public function dataProviderCheckExists2()
    {
        return array(
            array(array()),
            array(123),
            array(123.123),
            array(TRUE),
            array(FALSE),
            array(NULL),
        );
    }


    /* test FileHelper::checkExistsFile() */

    /**
     * @param $path
     * @param $expected
     *
     * @dataProvider dataProviderCheckExistsFile
     *
     * @throws \Exception
     */
    public function testCheckExistsFile($path, $expected)
    {
        $this->assertEquals($expected, FileHelper::checkExistsFile($path));
    }

    /**
     * @return array of [path, result]
     */
    public function dataProviderCheckExistsFile()
    {
        return array(
            array(__DIR__, FALSE),
            array(__DIR__ . '/../samples', FALSE),
            array(__DIR__ . '/../samples/', FALSE),
            array(__DIR__ . '/../samples/../samples', FALSE),
            array(__DIR__ . '/../samples/../samples/helpers/file-helper', FALSE),
            array(__DIR__ . '/../samples/sample-no-exists', FALSE),
            array(__DIR__ . '/../samples/helpers/file-helper/sample-one.txt', TRUE),
            array(__DIR__ . '/../samples/helpers/file-helper/sample-one-no-exist.txt', FALSE),
        );
    }


    /* test FileHelper::checkExistsDir() */

    /**
     * @param $path
     * @param $expected
     *
     * @dataProvider dataProviderCheckExistsDir
     *
     * @throws \Exception
     */
    public function testCheckExistsDir($path, $expected)
    {
        $this->assertEquals($expected, FileHelper::checkExistsDir($path));
    }

    /**
     * @return array of [path, result]
     */
    public function dataProviderCheckExistsDir()
    {
        return array(
            array(__DIR__, TRUE),
            array(__DIR__ . '/../samples', TRUE),
            array(__DIR__ . '/../samples/', TRUE),
            array(__DIR__ . '/../samples/../samples', TRUE),
            array(__DIR__ . '/../samples/../samples/helpers/file-helper', TRUE),
            array(__DIR__ . '/../samples/sample-no-exists', FALSE),
            array(__DIR__ . '/../samples/helpers/file-helper/sample-one.txt', FALSE),
            array(__DIR__ . '/../samples/helpers/file-helper/sample-one-no-exist.txt', FALSE),
            array('123', FALSE),
        );
    }


    /* test FileHelper::getFileContent() */

    /**
     * @param $path
     * @param $required
     * @param $expected
     *
     * @dataProvider dataProviderGetFileContent1
     *
     * @throws \Exception
     */
    public function testGetFileContent1($path, $required, $expected)
    {
        $this->assertEquals($expected, FileHelper::getFileContent($path, $required));
    }

    /**
     * @return array of [path, result]
     */
    public function dataProviderGetFileContent1()
    {
        return array(
            array(__DIR__, FALSE, ''),
            array(__DIR__ . '/../samples/helpers/file-helper/sample-one-no-exists.txt', FALSE, ''),
            array(__DIR__ . '/../samples/helpers/file-helper/sample-one.txt', TRUE, 'some content one'),
            array(__DIR__ . '/../samples/helpers/file-helper/sample-one.txt', FALSE, 'some content one'),
        );
    }

    /**
     * @param $path
     * @param $required
     *
     * @dataProvider dataProviderGetFileContent2
     * @expectedException \Exception
     *
     * @throws \Exception
     */
    public function testGetFileContent2($path, $required)
    {
        FileHelper::getFileContent($path, $required);
    }

    /**
     * @return array of [path]
     */
    public function dataProviderGetFileContent2()
    {
        return array(
            array(__DIR__, TRUE),
            array(__DIR__ . '/../samples/helpers/file-helper/sample-one.txt', 1),
            array(__DIR__ . '/../samples/helpers/file-helper/sample-one-no-exists.txt', TRUE),
        );
    }


    /* test FileHelper::getFilesContent() */

    /**
     * @param $paths
     * @param $required
     * @param $separator
     * @param $expected
     *
     * @dataProvider dataProviderGetFilesContent1
     *
     * @throws \Exception
     */
    public function testGetFilesContent1($paths, $required, $separator, $expected)
    {
        $this->assertEquals($expected, FileHelper::getFilesContent($paths, $required, $separator));
    }

    /**
     * @return array of [path, result]
     */
    public function dataProviderGetFilesContent1()
    {
        return array(

            array(array(), TRUE, '', ''),

            array(array('no.txt', 'no.txt', 'no.txt'), FALSE, ' + ', ' +  + '),

            array(array(
                __DIR__ . '/../samples/helpers/file-helper/sample-one.txt',
                __DIR__ . '/../samples/helpers/file-helper/sample-two.txt'
            ), TRUE, '', 'some content onefile two'),

            array(array(
                __DIR__ . '/../samples/helpers/file-helper/sample-one.txt',
                __DIR__ . '/../samples/helpers/file-helper/sample-two.txt'
            ), TRUE, ' / ', 'some content one / file two'),

            array(array(
                __DIR__ . '/../samples/helpers/file-helper/sample-one.txt',
                __DIR__ . '/../samples/helpers/file-helper/sample-two.txt',
                __DIR__ . '/../samples/helpers/file-helper/sample-three.txt'
            ), TRUE, ' / ', 'some content one / file two / hello
world'),

            array(array(
                __DIR__ . '/../samples/helpers/file-helper/sample-one.txt',
                __DIR__ . '/../samples/helpers/file-helper/sample-no-exists.txt',
                __DIR__ . '/../samples/helpers/file-helper/sample-two.txt',
                __DIR__ . '/../samples/helpers/file-helper/sample-no-exists.txt'
            ), FALSE, ' / ', 'some content one /  / file two / ')

        );
    }

    /**
     * @param $paths
     * @param $required
     *
     * @dataProvider dataProviderGetFilesContent2
     * @expectedException \Exception
     *
     * @throws \Exception
     */
    public function testGetFilesContent2($paths, $required)
    {
        FileHelper::getFilesContent($paths, $required);
    }

    /**
     * @return array of [path]
     */
    public function dataProviderGetFilesContent2()
    {
        return array(
            array(__DIR__, FALSE),

            array(array(
                __DIR__ . '/../samples/helpers/file-helper/sample-one.txt',
                __DIR__ . '/../samples/helpers/file-helper/sample-one.txt',
                __DIR__ . '/../samples/helpers/file-helper/sample-one-no-exists.txt'
            ), TRUE),
        );
    }


    /* test FileHelper::saveFile() */

    public function testSaveFile()
    {
        FileHelper::saveFile($this->filepath, 'test content');
        $this->assertEquals('test content', file_get_contents($this->filepath));
    }

}