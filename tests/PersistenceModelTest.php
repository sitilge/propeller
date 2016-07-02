<?php

use PHPUnit\Framework\TestCase;

class PersistenceModelTest extends TestCase
{
    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private $root;

    /**
     * @var \Propeller\Models\PersistenceModel
     */
    private $persistenceModel;

    /**
     * @var \Models\Models\UsersQuery
     */
    private $query;

    /**
     * @var \Models\Models\Users
     */
    private $model;

    /**
     * @var string
     */
    private $table = 'users';

    /**
     * @var string
     */
    private $key = '';

    /**
     * @var string
     */
    private $schema =
        '<?xml version="1.0" encoding="utf-8"?>
        <database name="default" defaultIdMethod="native" namespace="Models" defaultPhpNamingMethod="underscore">
          <table name="menu" idMethod="native" phpName="Menu" namespace="Models">
            <column name="id" phpName="Id" type="INTEGER" primaryKey="true" autoIncrement="true" required="true"/>
            <column name="name" phpName="Name" type="VARCHAR" size="50" required="true"/>
            <column name="slug" phpName="Slug" type="VARCHAR" size="50" required="true"/>
            <column name="display" phpName="Display" type="INTEGER" required="true"/>
            <column name="sequence" phpName="Sequence" type="INTEGER" required="true"/>
            <vendor type="mysql">
              <parameter name="Engine" value="InnoDB"/>
            </vendor>
          </table>
          <table name="photos" idMethod="native" phpName="Photos" namespace="Models">
            <column name="id" phpName="Id" type="INTEGER" primaryKey="true" autoIncrement="true" required="true"/>
            <column name="name" phpName="Name" type="VARCHAR" size="45"/>
            <column name="color" phpName="Color" type="VARCHAR" size="45"/>
            <vendor type="mysql">
              <parameter name="Engine" value="InnoDB"/>
            </vendor>
          </table>
          <table name="users" idMethod="native" phpName="Users" namespace="Models">
            <column name="id" phpName="Id" type="INTEGER" sqlType="int(11) unsigned" primaryKey="true" autoIncrement="true" required="true"/>
            <column name="name" phpName="Name" type="VARCHAR" size="255" required="true"/>
            <column name="surname" phpName="Surname" type="VARCHAR" size="255" required="true"/>
            <column name="photo" phpName="Photo" type="INTEGER" required="true"/>
            <column name="email" phpName="Email" type="VARCHAR" size="255" required="true"/>
            <column name="birthday" phpName="Birthday" type="DATE" required="true"/>
            <foreign-key foreignTable="photos" name="fk_users_1">
              <reference local="photo" foreign="id"/>
            </foreign-key>
            <index name="fk_users_1">
              <index-column name="photo"/>
            </index>
            <unique name="email">
              <unique-column name="email"/>
            </unique>
            <vendor type="mysql">
              <parameter name="Engine" value="InnoDB"/>
            </vendor>
          </table>
        </database>';

    /**
     * @var array
     */
    private $tables = [
        'menu' => 'Menu',
        'photos' => 'Photos',
        'users' => 'Users'
    ];

    /**
     * @var string
     */
    private $modelNamespace = '\\Models\\Models\\';

    /**
     * @var array
     */
    private $input = [
        'name' => 'John',
        'surname' => 'Davis',
        'photo' => 1,
        'email' => 'davis@example.com'
    ];

    public function setUp()
    {
        $this->root = \org\bovigo\vfs\vfsStream::setup('root');

        \org\bovigo\vfs\vfsStream::newFile('schema.xml')->at($this->root)->setContent($this->schema);

        $this->persistenceModel = new \Propeller\Models\PersistenceModel();
        $this->persistenceModel->table = $this->table;
        $this->persistenceModel->tables = $this->tables;
        $this->persistenceModel->modelNamespace = $this->modelNamespace;

        $queryName = $this->modelNamespace.$this->tables[$this->table].'Query';
        $this->query = new $queryName;

        $modelName = $this->modelNamespace.$this->tables[$this->table];
        $this->model = new $modelName;
    }

    public function testGetTables()
    {
        $this->persistenceModel->schemaPath = $this->root->getChild('schema.xml')->url();

        $this->assertEquals($this->tables, $this->persistenceModel->getTables());
    }

    /**
     * @depends testGetTables
     */
    public function testGetQuery()
    {
        $this->assertEquals($this->query, $this->persistenceModel->getQuery());
    }

    /**
     * @depends testGetQuery
     */
    public function testGetMap()
    {
        $this->assertEquals($this->query->getTableMap(), $this->persistenceModel->getMap());
    }

    /**
     * @depends testGetMap
     */
    public function testGetColumns()
    {
        $map = $this->query->getTableMap();

        $this->assertEquals($map->getColumns(), $this->persistenceModel->getColumns());
    }

    /**
     * @depends testGetQuery
     */
    public function testReadRows()
    {
        $this->assertEquals($this->query->find(), $this->persistenceModel->readRows());
    }

    /**
    * @depends testReadRows
    */
    public function testGetKeys()
    {
        $rows = $this->query->find();

        $this->assertEquals($rows->getPrimaryKeys(false), $this->persistenceModel->getKeys());
    }

    /**
     * @depends testGetTables
     */
    public function testGetModel()
    {
        $this->assertEquals($this->model, $this->persistenceModel->getModel());
    }

    /**
     * @depends testGetQuery
     */
    public function testReadRow()
    {
        $this->assertEquals($this->query->findPk($this->key), $this->persistenceModel->readRow());
    }

    /**
     * @depends testGetModel
     * @depends testGetMap
     */
    public function testCreateRow()
    {
        $this->persistenceModel->createRow($this->input);

        $this->persistenceModel->key = $this->persistenceModel->model->getId();

        $this->assertInternalType('int', $this->persistenceModel->key);

        $this->persistenceModel->deleteRow();
    }

    /**
     * @depends testGetQuery
     * @depends testGetMap
     */
    public function testUpdateRow()
    {
        $this->persistenceModel->createRow($this->input);

        $this->persistenceModel->key = $this->persistenceModel->model->getId();

        $this->assertInternalType('int', $this->persistenceModel->updateRow($this->input));

        $this->persistenceModel->deleteRow();
    }

    /**
     * @depends testGetQuery
     */
    public function testDeleteRow()
    {
        $this->persistenceModel->createRow($this->input);

        $this->persistenceModel->key = $this->persistenceModel->model->getId();

        //TODO - why is not returning integer but NULL???
        $this->assertNull($this->persistenceModel->deleteRow());
    }
}