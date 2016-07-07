<?php

use PHPUnit\Framework\TestCase;

class PersistenceModelTest extends TestCase
{
    /**
     * @var \Propeller\Models\PersistenceModel
     */
    private $persistenceModel;

    public function setUp()
    {
        $this->persistenceModel = new \Propeller\Models\PersistenceModel();
        $this->persistenceModel->ormModel = $this->createMock(\Propeller\Models\OrmModel::class);

        $root = \org\bovigo\vfs\vfsStream::setup('root');
        \org\bovigo\vfs\vfsStream::newFile('foo.xml')->at($root)->setContent('<foo></foo>');
        \org\bovigo\vfs\vfsStream::newFile('bar.xml')->at($root);

        //TODO - to test in real environment, edit/comment the respective attributes
        $this->persistenceModel->table = 'foo';
        $this->persistenceModel->key = '';
        $this->persistenceModel->schema = $root->getChild('foo.xml')->url();
        $this->persistenceModel->runtime = $root->getChild('bar.xml')->url();
        $this->persistenceModel->namespace = 'bar';
        $this->persistenceModel->tables = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ];
    }

    //TODO - to test in real environment, edit the respective tests
    public function testGetTables()
    {
        $tables = [
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ];

        $this->assertEquals($tables, $this->persistenceModel->getTables());
    }

    public function testGetQuery()
    {
        $query = \Propel\Runtime\ActiveQuery\ModelCriteria::class;

        $this->persistenceModel->ormModel
            ->method('getQuery')
            ->willReturn(new $query());

        $this->assertInstanceOf($query, $this->persistenceModel->getQuery());
    }

    public function testGetMap()
    {
        $this->markTestSkipped('To test in real environment, edit this test');

        //query class, e.g. UsersQuery
        $query = \Propel\Runtime\ActiveQuery\ModelCriteria::class;

        $this->persistenceModel->query = $this->createMock($query);

        $map = \Propel\Runtime\Map\TableMap::class;

        $this->assertInstanceOf($map, $this->persistenceModel->getMap());
    }

    public function testGetColumns()
    {
        $this->markTestSkipped('To test in real environment, edit this test');

        //query class, e.g. UsersQuery
        $query = \Propel\Runtime\ActiveQuery\ModelCriteria::class;

        $this->persistenceModel->query = $this->createMock($query);

        $map = \Propel\Runtime\Map\TableMap::class;

        $this->persistenceModel->map = $this->createMock($map);

        $columns = \Propel\Runtime\Map\ColumnMap::class;

        $this->assertInstanceOf($columns, $this->persistenceModel->getColumns());
    }

    public function testGetKeys()
    {
        $this->markTestSkipped('To test in real environment, edit this test');

        //query class, e.g. UsersQuery
        $query = \Propel\Runtime\ActiveQuery\ModelCriteria::class;

        $this->persistenceModel->query = $this->createMock($query);

        $keys = [];

        $this->assertInstanceOf($keys, $this->persistenceModel->getKeys());
    }

    public function testGetModel()
    {
        $this->markTestSkipped('To test in real environment, edit this test');
    }

    public function testCreateRow()
    {
        $this->markTestSkipped('To test in real environment, edit this test');
    }

    public function testReadRow()
    {
        $this->markTestSkipped('To test in real environment, edit this test');

        //query class, e.g. UsersQuery
        $query = \Propel\Runtime\ActiveQuery\ModelCriteria::class;

        $this->persistenceModel->query = $this->createMock($query);

        $row = \Propel\Runtime\Collection\ObjectCollection::class;

        $this->assertInstanceOf($row, $this->persistenceModel->readRow());
    }

    public function testReadRows()
    {
        $this->markTestSkipped('To test in real environment, edit this test');

        //query class, e.g. UsersQuery
        $query = \Propel\Runtime\ActiveQuery\ModelCriteria::class;

        $this->persistenceModel->query = $this->createMock($query);

        $rows = \Propel\Runtime\Collection\ObjectCollection::class;

        $this->assertInstanceOf($rows, $this->persistenceModel->readRows());
    }

    public function testUpdateRow()
    {
        $this->markTestSkipped('To test in real environment, edit this test');

        //query class, e.g. UsersQuery
        $query = \Propel\Runtime\ActiveQuery\ModelCriteria::class;

        $this->persistenceModel->query = $this->createMock($query);

        $this->persistenceModel->ormModel
            ->method('save')
            ->willReturn($this->persistenceModel->key);

        $this->assertEquals($this->persistenceModel->key, $this->persistenceModel->updateRow([]));
    }

    public function testDeleteRow()
    {
        $this->markTestSkipped('To test in real environment, edit this test');

        //query class, e.g. UsersQuery
        $query = \Propel\Runtime\ActiveQuery\ModelCriteria::class;

        $this->persistenceModel->query = $this->createMock($query);

        $this->persistenceModel->ormModel
            ->method('delete')
            ->willReturn($this->persistenceModel->key);

        $this->assertEquals($this->persistenceModel->key, $this->persistenceModel->deleteRow());
    }
}
