<?php

/**
 * DatabaseTest
 *
 * @copyright Copyright (c) 2017 Roman Rozinko
 * @license   MIT License
 *
 * @author    Roman Rozinko <r.rozinko@gmail.com>
 * @since     0.3
 */

namespace fly\tests;

use PHPUnit\Framework\TestCase;
use fly\Database;

class DatabaseTest extends TestCase
{

    /**
     * @var string Database name
     */
    private $database = 'flytest';

    /**
     * DatabaseTest constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        Database::Connect([
            'database' => $this->database,
        ]);

        parent::__construct();
    }

    protected function deleteTables()
    {
        Database::SQL("DROP TABLE IF EXISTS `city`;");
        Database::SQL("DROP TABLE IF EXISTS `country`;");
        Database::SQL("DROP TABLE IF EXISTS `countrylanguage`;");
    }

    protected function createTables()
    {
        $content = file_get_contents(__DIR__ . '/samples/database/flytest.sql');
        $sql_array = explode(';', $content);
        foreach ($sql_array as $sql) {
            Database::SQL($sql . ';');
        }
    }

    /**
     * @throws \Exception
     */
    protected function setUp()
    {
        $this->deleteTables();
        $this->createTables();

        parent::setUp();
    }

    protected function tearDown()
    {
        $this->deleteTables();

        parent::tearDown();
    }

    /**
     * @throws \Exception
     */
    public function testSql()
    {
        $result = Database::SQL("SELECT COUNT(*) as `count` FROM `city` WHERE `CountryCode` = 'BLR';");
        $this->assertEquals([0 => ['count' => 7]], $result);

        $result = Database::SQL("SELECT `name` FROM `city` WHERE `id` = '2';");
        $this->assertEquals([0 => ['name' => 'Gomel']], $result);

        $result = Database::SQL("SELECT * FROM `city` WHERE `CountryCode` = 'RUS';");
        $this->assertEquals(12, count($result));

        $result = Database::SQL("SELECT `id`, `Name`, `CountryCode`, (SELECT `Language` FROM `countrylanguage` `cl` WHERE `c`.`CountryCode` = `cl`.`CountryCode` AND `isOfficial` = 'T') as `language` FROM `city` `c` WHERE `id` = '8';");
        $this->assertEquals(1, count($result));
        $this->assertEquals(8, $result[0]['id']);
        $this->assertEquals('Moscow', $result[0]['Name']);
        $this->assertEquals('RUS', $result[0]['CountryCode']);
        $this->assertEquals('Russian', $result[0]['language']);
    }

    /**
     * @throws \Exception
     */
    public function testSqlWithParams()
    {
        $result = Database::SQL("SELECT COUNT(*) as `count` FROM `city` WHERE `CountryCode` = ?;", ['RUS']);
        $this->assertEquals([0 => ['count' => 12]], $result);

        $result = Database::SQL("SELECT `name` FROM `city` WHERE `id` = ?;", [11]);
        $this->assertEquals([0 => ['name' => 'Nizni Novgorod']], $result);

        $result = Database::SQL("SELECT * FROM `city` WHERE `CountryCode` = ?;", ['BLR']);
        $this->assertEquals(7, count($result));

        $result = Database::SQL("SELECT `id`, `Name`, `CountryCode`, (SELECT `Language` FROM `countrylanguage` `cl` WHERE `c`.`CountryCode` = `cl`.`CountryCode` AND `isOfficial` = ?) as `language` FROM `city` `c` WHERE `id` = ?;", ['T', '18']);
        $this->assertEquals(1, count($result));
        $this->assertEquals(18, $result[0]['id']);
        $this->assertEquals('Rostov-na-Donu', $result[0]['Name']);
        $this->assertEquals('RUS', $result[0]['CountryCode']);
        $this->assertEquals('Russian', $result[0]['language']);
    }

    /**
     * @throws \Exception
     */
    public function testQueryBuilderSelect()
    {
        $result = Database::Query()
            ->selectAll()
            ->from(['city'])
            ->all();
        $this->assertEquals(19, count($result));

        $result = Database::Query()
            ->selectAll()
            ->from(['country'])
            ->all();
        $this->assertEquals(2, count($result));

        $result = Database::Query()
            ->select('Name')
            ->from(['city'])
            ->all();
        $this->assertEquals(19, count($result));
        $this->assertArrayHasKey('Name', $result[0]);
        $this->assertArrayHasKey('Name', $result[18]);

        $result = Database::Query()
            ->select(['Code', 'Name', 'Code2'])
            ->from(['country'])
            ->all();
        $this->assertEquals(2, count($result));
        $this->assertEquals(3, count($result[0]));
        $this->assertEquals(3, count($result[1]));
        $this->assertArrayHasKey('Code', $result[0]);
        $this->assertArrayHasKey('Name', $result[0]);
        $this->assertArrayHasKey('Code2', $result[0]);
        $this->assertArrayHasKey('Code', $result[1]);
        $this->assertArrayHasKey('Name', $result[1]);
        $this->assertArrayHasKey('Code2', $result[1]);

        $result = Database::Query()
            ->select('Name')
            ->from(['city'])
            ->clearSelect()
            ->addSelect('*')
            ->all();
        $this->assertEquals(19, count($result));
        $this->assertEquals(5, count($result[0]));

        $result = Database::Query()
            ->selectAll()
            ->from(['city'])
            ->clearFrom()
            ->addFrom(['country'])
            ->all();
        $this->assertEquals(2, count($result));
    }

    /**
     * @throws \Exception
     */
    public function testQueryBuilderSelectRow()
    {
        $result = Database::Query()
            ->selectAll()
            ->from(['city'])
            ->row();
        $this->assertEquals(5, count($result));

        $result = Database::Query()
            ->select(['Code', 'Name', 'Code2'])
            ->from(['country'])
            ->row();
        $this->assertEquals(3, count($result));
        $this->assertArrayHasKey('Code', $result);
        $this->assertArrayHasKey('Name', $result);
        $this->assertArrayHasKey('Code2', $result);
    }

    /**
     * @throws \Exception
     */
    public function testQueryBuilderSelectColumn()
    {
        $result = Database::Query()
            ->select('Name')
            ->from(['city'])
            ->orderBy('id')
            ->column();
        $this->assertEquals(19, count($result));
        $this->assertEquals('Minsk', $result[0]);
        $this->assertEquals('Gomel', $result[1]);
        $this->assertEquals('Omsk', $result[13]);

        $result = Database::Query()
            ->select('Name')
            ->from(['city'])
            ->orderBy('id')
            ->column(2);
        $this->assertEquals(2, count($result));
        $this->assertEquals('Minsk', $result[0]);
        $this->assertEquals('Gomel', $result[1]);

        $result = Database::Query()
            ->select('Name')
            ->from(['city'])
            ->orderBy('id')
            ->column([10, 3]);
        $this->assertEquals(3, count($result));
        $this->assertEquals('Nizni Novgorod', $result[0]);
        $this->assertEquals('Jekaterinburg', $result[1]);
        $this->assertEquals('Samara', $result[2]);
    }

    /**
     * @throws \Exception
     */
    public function testQueryBuilderSelectValue()
    {
        $result = Database::Query()
            ->select('Name')
            ->from(['city'])
            ->orderBy('id')
            ->value();
        $this->assertEquals(1, count($result));
        $this->assertEquals('Minsk', $result);
    }

    /**
     * @throws \Exception
     */
    public function testQueryBuilderSelectWithWhere()
    {
        $result = Database::Query()
            ->selectAll()
            ->from(['city'])
            ->where(['id', 1])
            ->all();
        $this->assertEquals(1, count($result));
        $this->assertEquals('Minsk', $result[0]['Name']);
        $this->assertEquals(1674000, $result[0]['Population']);

        $result = Database::Query()
            ->selectAll()
            ->from('city')
            ->where([
                ['CountryCode', 'RUS'],
                ['Name', 'St Petersburg'],
            ])
            ->row();
        $this->assertEquals('St Petersburg', $result['Name']);
        $this->assertEquals(4694000, $result['Population']);

        $result = Database::Query()
            ->selectAll()
            ->from('city')
            ->where(['CountryCode', 'BLR'])
            ->andWhere(['Name', 'Gomel'])
            ->all();
        $this->assertEquals(1, count($result));
        $this->assertEquals('Gomel', $result[0]['Name']);
        $this->assertEquals('BLR', $result[0]['CountryCode']);
        $this->assertEquals(475000, $result[0]['Population']);

        $result = Database::Query()
            ->selectAll()
            ->from('city')
            ->where([
                ['CountryCode', 'RUS'],
                ['Name', 'St Petersburg'],
            ])
            ->orWhere([
                ['CountryCode', 'BLR'],
                ['Name', 'Minsk'],
            ])
            ->orderBy(['id'])
            ->all();
        $this->assertEquals(2, count($result));
        $this->assertEquals('Minsk', $result[0]['Name']);
        $this->assertEquals('BLR', $result[0]['CountryCode']);
        $this->assertEquals(1674000, $result[0]['Population']);
        $this->assertEquals('St Petersburg', $result[1]['Name']);
        $this->assertEquals('RUS', $result[1]['CountryCode']);
        $this->assertEquals(4694000, $result[1]['Population']);

        $result = Database::Query()
            ->selectAll()
            ->from('city')
            ->where([
                [
                    ['CountryCode', 'RUS'],
                    ['Name', 'St Petersburg'],
                ],
                ['OR'],
                [
                    ['CountryCode', 'BLR'],
                    ['Name', 'Minsk'],
                ],
            ])
            ->orderBy(['id'])
            ->all();
        $this->assertEquals(2, count($result));
        $this->assertEquals('Minsk', $result[0]['Name']);
        $this->assertEquals('BLR', $result[0]['CountryCode']);
        $this->assertEquals(1674000, $result[0]['Population']);
        $this->assertEquals('St Petersburg', $result[1]['Name']);
        $this->assertEquals('RUS', $result[1]['CountryCode']);
        $this->assertEquals(4694000, $result[1]['Population']);

        $result = Database::Query()
            ->selectAll()
            ->from('city')
            ->where([
                [
                    ['CountryCode', 'RUS'],
                    ['Name', 'St Petersburg'],
                ],
                ['OR'],
                [
                    ['CountryCode', 'BLR'],
                    ['Name', 'Minsk'],
                ],
            ])
            ->orderBy(['id'])
            ->clearWhere()
            ->all();
        $this->assertEquals(19, count($result));
    }

    /**
     * @throws \Exception
     */
    public function testQueryBuilderSelectWithOrder()
    {
        $result = Database::Query()
            ->selectAll()
            ->from(['city'])
            ->orderBy(['id', 'DESC'])
            ->row();
        $this->assertEquals(5, count($result));
        $this->assertEquals(19, $result['ID']);
        $this->assertEquals('Perm', $result['Name']);
        $this->assertEquals(1009700, $result['Population']);

        $result = Database::Query()
            ->selectAll()
            ->from(['city'])
            ->orderBy(['id', 'DESC'])
            ->clearOrderBy()
            ->addOrderBy(['Population', 'DESC'])
            ->all(3);
        $this->assertEquals(3, count($result));
        $this->assertEquals('Moscow', $result[0]['Name']);
        $this->assertEquals(8389200, $result[0]['Population']);
        $this->assertEquals('St Petersburg', $result[1]['Name']);
        $this->assertEquals(4694000, $result[1]['Population']);
        $this->assertEquals('Minsk', $result[2]['Name']);
        $this->assertEquals(1674000, $result[2]['Population']);
    }

    /**
     * @throws \Exception
     */
    public function testQueryBuilderSelectWithLimit()
    {
        $result = Database::Query()
            ->selectAll()
            ->from(['city'])
            ->all(3);
        $this->assertEquals(3, count($result));

        $result = Database::Query()
            ->selectAll()
            ->from(['city'])
            ->offset(3)
            ->all(3);
        $this->assertEquals(3, count($result));

        $result = Database::Query()
            ->selectAll()
            ->from(['city'])
            ->offset(17)
            ->all(3);
        $this->assertEquals(2, count($result));

        $result = Database::Query()
            ->selectAll()
            ->from(['city'])
            ->all([17, 3]);
        $this->assertEquals(2, count($result));
    }

}
