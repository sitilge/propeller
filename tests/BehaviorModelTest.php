<?php

use PHPUnit\Framework\TestCase;

class BehaviorModelTest extends TestCase
{
    /**
     * @var \Propeller\Models\BehaviorModel
     */
    private $behaviorModel;

    public function setUp()
    {
        $this->behaviorModel = new \Propeller\Models\BehaviorModel();
    }

    public function testQueryAttributes()
    {
        $table = new \Propel\Generator\Model\Table('foo');

        $this->behaviorModel->setTable($table);

        $this->assertInternalType('string', $this->behaviorModel->queryAttributes());
    }

    public function testQueryMethods()
    {
        $this->assertInternalType('string', $this->behaviorModel->queryMethods());
    }
}
