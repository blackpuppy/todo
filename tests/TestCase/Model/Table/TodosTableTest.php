<?php
namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

use Cake\I18n\Time;

class TodosTest extends TestCase {
    public $fixtures = ['app.todos'];

    public function setUp() {
        parent::setUp();
        $this->Todos = TableRegistry::get('Todos');
    }

    public function testSaving() {
        $data = ['todo' => ''];
        $todo = $this->Todos->newEntity($data);
        $resultingError = $this->Todos->validator()->errors($data);
        $expectedError = [
            'todo' => [
                'This field cannot be left empty'
            ]
        ];
        $this->assertEquals($expectedError, $resultingError);

        $total = $this->Todos->find()->count();
        $this->assertEquals(1, $total);

        $data = ['todo' => 'testing'];
        $todo = $this->Todos->newEntity($data);
        $this->Todos->save($todo);
        $newTotal = $this->Todos->find()->count();
        $this->assertEquals(2, $newTotal);
    }

    public function testRecent() {
        $query = $this->Todos->find('recent', ['status' => 0]);
        $expected = [
            [
                'id' => 1,
                'todo' => 'First To-do',
                'created' => new Time('2014-11-21 12:00:00'),
                'updated' => new Time('2014-11-21 12:00:00'),
                'is_done' => 0
            ]
        ];

        $this->assertEquals($expected, $query->toArray());
    }

    public function testFindTimeAgoInWords() {
        $todos = TableRegistry::get('Todos');
        $todo = $todos->get(1);
        $todos->patchEntity($todo, ['updated' => new Time(date('Y-m-d H:i:s', strtotime('-3 seconds ago')))]);
        $todos->save($todo);
        $result = $todos->find('recent', ['status' => 0]);
        $r = $result->toArray();
        $this->assertContains('second', $r[0]->updated);

        $todos = TableRegistry::get('Todos');
        $todo = $todos->get(1);
        $todos->patchEntity($todo, ['created' => new Time(date('Y-m-d H:i:s', strtotime('-3 seconds ago')))]);
        $todos->save($todo);
        $result = $todos->find('recent', ['status' => 0]);
        $r = $result->toArray();
        $this->assertContains('second', $r[0]->created);
    }
}