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

    /**
     * @throws \Exception
     */
    public function testUpdateAndSet()
    {
        $class = new _QueryMain();

        $this->assertEquals(FALSE, $class->getProtectedVar('queryType'));

        $class->update('test_table');

        $this->assertEquals('UPDATE', $class->getProtectedVar('queryType'));
        $this->assertEquals('test_table', $class->getProtectedVar('update'));

        $class->set(['field_name', 'new_value']);

        $this->assertEquals(1, count($class->getProtectedVar('set')));
        $this->assertEquals([
            0 => ['field' => 'field_name', 'value' => 'new_value'],
        ], $class->getProtectedVar('set'));

        $class->addSet([
            ['field1', 'value1'],
            ['field2', 'value2'],
        ]);

        $this->assertEquals(3, count($class->getProtectedVar('set')));
        $this->assertEquals([
            0 => ['field' => 'field_name', 'value' => 'new_value'],
            1 => ['field' => 'field1', 'value' => 'value1'],
            2 => ['field' => 'field2', 'value' => 'value2'],
        ], $class->getProtectedVar('set'));

        $class->set([
            ['field1', 'value1'],
            ['field2', 'value2'],
        ]);

        $this->assertEquals(2, count($class->getProtectedVar('set')));
        $this->assertEquals([
            0 => ['field' => 'field1', 'value' => 'value1'],
            1 => ['field' => 'field2', 'value' => 'value2'],
        ], $class->getProtectedVar('set'));

        $class->prepare();

        $this->assertEquals('UPDATE `test_table`', $class->getProtectedVar('prepared')['UPDATE']);
        $this->assertEquals('SET `field1` = :value1, `field2` = :value2', $class->getProtectedVar('prepared')['SET']);

        $this->assertEquals('UPDATE `test_table` SET `field1` = :value1, `field2` = :value2;', $class->getProtectedVar('preparedSQL'));
        $this->assertEquals([
            ':value1' => 'value1',
            ':value2' => 'value2',
        ], $class->getProtectedVar('preparedParams'));
    }

    /**
     * @expectedException \Exception
     *
     * @throws \Exception
     */
    public function testExceptionInsertInvalidType()
    {
        $class = new _QueryMain();
        $class->select()->insert();
    }

    /**
     * @expectedException \Exception
     *
     * @throws \Exception
     */
    public function testExceptionIntoInvalidType()
    {
        $class = new _QueryMain();
        $class->select()->into('table');
    }

    /**
     * @expectedException \Exception
     *
     * @throws \Exception
     */
    public function testExceptionIntoInvalidTableName()
    {
        $class = new _QueryMain();
        $class->insert()->into('table not valid');
    }

    /**
     * @expectedException \Exception
     *
     * @throws \Exception
     */
    public function testExceptionValuesInvalidType()
    {
        $class = new _QueryMain();
        $class->select()->values(['key' => 'value']);
    }

    /**
     * @expectedException \Exception
     *
     * @throws \Exception
     */
    public function testExceptionValuesInvalidDataNoArray()
    {
        $class = new _QueryMain();
        $class->insert()->into('table')->values(2018);
    }

    /**
     * @expectedException \Exception
     *
     * @throws \Exception
     */
    public function testExceptionValuesInvalidDataInvalidArray()
    {
        $class = new _QueryMain();
        $class->insert()->into('table')->values(['key' => ['value'], 'key2' => 'value2']);
    }

    /**
     * @expectedException \Exception
     *
     * @throws \Exception
     */
    public function testExceptionValuesInvalidData3DArray()
    {
        $class = new _QueryMain();
        $class->insert()->into('table')->values([0 => ['key' => ['value1']]]);
    }

    /**
     * @expectedException \Exception
     *
     * @throws \Exception
     */
    public function testExceptionUpdateInvalidType()
    {
        $class = new _QueryMain();
        $class->select()->update('table');
    }

    /**
     * @expectedException \Exception
     *
     * @throws \Exception
     */
    public function testExceptionUpdateInvalidTableName()
    {
        $class = new _QueryMain();
        $class->update('table not valid');
    }

    /**
     * @param array $set
     *
     * @dataProvider dataProviderExceptionSetInvalidData
     * @expectedException \Exception
     *
     * @throws \Exception
     */
    public function testExceptionSetInvalidData($set)
    {
        $class = new _QueryMain();
        $class->update('table')->set($set);
    }

    public function dataProviderExceptionSetInvalidData()
    {
        return [
            [true],
            [500100],
            ['key'],
            [['key' => 123]],
            [['field', 'value', 'incorrectvalue']]
        ];
    }
}
