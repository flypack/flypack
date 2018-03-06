<?php

/**
 * QueryMainTest
 *
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 *
 * @author    Roman Rozinko <r.rozinko@gmail.com>
 * @since     0.3
 */

namespace fly\database\tests;

use fly\database\QueryMain;
use PHPUnit\Framework\TestCase;

class _QueryMain extends QueryMain
{

    public function getProtectedVar($name)
    {
        return $this->$name;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function prepare()
    {
        parent::prepare();

        return $this;
    }

}

class QueryBuilderTest extends TestCase
{

    /**
     * @throws \Exception
     */
    public function testInsert()
    {
        $class = new _QueryMain();

        $this->assertEquals(FALSE, $class->getProtectedVar('queryType'));

        $class->insert();
        $this->assertEquals('INSERT', $class->getProtectedVar('queryType'));
    }

    /**
     * @throws \Exception
     */
    public function testInsertInto()
    {
        $class = new _QueryMain();
        $class->insert();

        $this->assertEquals('', $class->getProtectedVar('insertInto'));

        $class->into('test_table');
        $this->assertEquals('test_table', $class->getProtectedVar('insertInto'));
    }

    /**
     * @throws \Exception
     */
    public function testInsertValues()
    {
        $class = new _QueryMain();
        $class->insert()->into('test_table');

        $this->assertEquals([], $class->getProtectedVar('insertValues'));

        $class->values(['key' => 123, 'key2' => 456]);

        $this->assertEquals([
            ['key' => 123, 'key2' => 456]
        ], $class->getProtectedVar('insertValues'));

        $class->values([
            ['key' => 123],
            ['key' => 456],
            ['key' => 789]
        ]);

        $this->assertEquals([
            ['key' => 123, 'key2' => 456],
            ['key' => 123],
            ['key' => 456],
            ['key' => 789]
        ], $class->getProtectedVar('insertValues'));
    }

    /**
     * @throws \Exception
     */
    public function testPrepareInsertValues()
    {
        $class = new _QueryMain();
        $class->insert()->into('table_1')->values([
            ['key' => 123],
            ['key' => 456],
            ['key' => 789]
        ])->prepare();

        $this->assertArrayHasKey('INSERT', $class->getProtectedVar('prepared'));
        $this->assertArrayHasKey('INTO', $class->getProtectedVar('prepared'));

        $this->assertEquals('INSERT', $class->getProtectedVar('prepared')['INSERT']);
        $this->assertEquals('INTO `table_1`', $class->getProtectedVar('prepared')['INTO']);

        $this->assertArrayHasKey('VALUES', $class->getProtectedVar('prepared'));
        $this->assertArrayHasKey('0', $class->getProtectedVar('prepared')['VALUES']);
        $this->assertArrayHasKey('1', $class->getProtectedVar('prepared')['VALUES']);
        $this->assertArrayHasKey('2', $class->getProtectedVar('prepared')['VALUES']);

        $this->assertEquals('(`key`) VALUES (:value1)', $class->getProtectedVar('prepared')['VALUES'][0]);
        $this->assertEquals('(`key`) VALUES (:value1)', $class->getProtectedVar('prepared')['VALUES'][1]);
        $this->assertEquals('(`key`) VALUES (:value1)', $class->getProtectedVar('prepared')['VALUES'][2]);

        $this->assertArrayHasKey('0', $class->getProtectedVar('preparedParams'));
        $this->assertArrayHasKey('1', $class->getProtectedVar('preparedParams'));
        $this->assertArrayHasKey('2', $class->getProtectedVar('preparedParams'));

        $this->assertArrayHasKey(':value1', $class->getProtectedVar('preparedParams')[0]);
        $this->assertArrayHasKey(':value1', $class->getProtectedVar('preparedParams')[1]);
        $this->assertArrayHasKey(':value1', $class->getProtectedVar('preparedParams')[2]);

        $this->assertEquals('123', $class->getProtectedVar('preparedParams')[0][':value1']);
        $this->assertEquals('456', $class->getProtectedVar('preparedParams')[1][':value1']);
        $this->assertEquals('789', $class->getProtectedVar('preparedParams')[2][':value1']);

        $this->assertArrayHasKey('0', $class->getProtectedVar('preparedSQL'));
        $this->assertArrayHasKey('1', $class->getProtectedVar('preparedSQL'));
        $this->assertArrayHasKey('2', $class->getProtectedVar('preparedSQL'));

        $this->assertEquals('INSERT INTO `table_1` (`key`) VALUES (:value1);', $class->getProtectedVar('preparedSQL')[0]);
        $this->assertEquals('INSERT INTO `table_1` (`key`) VALUES (:value1);', $class->getProtectedVar('preparedSQL')[1]);
        $this->assertEquals('INSERT INTO `table_1` (`key`) VALUES (:value1);', $class->getProtectedVar('preparedSQL')[2]);
    }

}
