<?php

/**
 * QueryBuilderTest
 *
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 *
 * @author    Roman Rozinko <r.rozinko@gmail.com>
 * @since     0.3
 */

namespace fly\database\tests;

use PHPUnit\Framework\TestCase;
use fly\database\QueryBuilder;

class _QueryBuilder extends QueryBuilder
{

    public function getProtectedVar($name)
    {
        return $this->$name;
    }

}

class QueryBuilderTest extends TestCase
{

    /**
     * @throws \Exception
     */
    public function testInsert()
    {
        $class = new _QueryBuilder();

        $this->assertEquals(FALSE, $class->getProtectedVar('queryType'));

        $class->insert();
        $this->assertEquals('INSERT', $class->getProtectedVar('queryType'));
    }

    /**
     * @throws \Exception
     */
    public function testInsertInto()
    {
        $class = new _QueryBuilder();
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
        $class = new _QueryBuilder();
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

}
