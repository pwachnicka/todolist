<?php

namespace Tests;

use App\Models\Task;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class=UserSeeder');
    }

    /**
     * /api/tasks [GET]
     */

    public function test_should_return_all_tasks(): void
    {
        $this->get('/api/tasks', []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            '*' => [
                'id',
                'title',
                'category',
                'description',
                'user_id',
            ]
        ]);
    }

    /**
     * /api/tasks [GET]
     */
    public function test_should_return_15_tasks(): void
    {
        $this->get('/api/tasks', []);
        $this->seeStatusCode(200);
        $this->response->assertJsonCount(15);
        $this->seeJsonStructure([
            '*' => [
                'id',
                'title',
                'category',
                'description',
                'user_id',
            ]
        ]);
    }

    /**
     * /api/tasks/id [GET]
     */
    public function test_should_return_one_task(): void
    {
        $this->get('/api/tasks/5', []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'id',
            'title',
            'category',
            'description',
            'user_id',
        ]);
    }

    /**
     * /api/tasks [GET]
     */
    public function test_should_not_return_task_when_task_not_exist(): void
    {
        $this->get('/api/tasks/99999', []);
        $this->seeStatusCode(404);
        $this->seeJsonEquals(['error' => 'Task not found!']);
    }

    /**
     * /api/tasks [POST]
     */
    public function test_should_create_task(): void
    {
        $task = Task::factory()->definition();
        $task = [
            ...$task,
            'user_id' => 1
        ];
        $this->post("/api/tasks", $task, []);
        $this->seeStatusCode(201);
    }

    /**
     * /api/tasks [POST]
     */
    public function test_should_return_validation_error_when_request_is_empty(): void
    {
        $dataToPost = [];
        $this->post("/api/tasks", $dataToPost, []);
        $this->seeStatusCode(422);
        $this->seeJsonStructure([
            'title',
            'category',
            'description',
            'user_id',
        ]);
        $this->seeJsonContains([
            'title' => ["The title field is required."]
        ]);
    }

    /**
     * /api/tasks [POST]
     */
    public function test_should_return_validation_error_when_request_has_only_1_field(): void
    {
        $task = ['title' => 'Clean the bathroom'];
        $this->post("/api/tasks", $task, []);
        $this->seeStatusCode(422);
        $this->seeJsonStructure([
            'category',
            'description',
            'user_id',
        ]);
        $this->seeJsonContains([
            'category' => ["The category field is required."]
        ]);
    }

    /**
     * /api/tasks [POST]
     */
    public function test_should_return_validation_error_when_field_does_not_exist(): void
    {
        $dataToPost = ['non_exist' => true];
        $this->post("/api/tasks", $dataToPost, []);
        $this->seeStatusCode(422);
        $this->seeJsonStructure([
            'title',
            'category',
            'description',
            'user_id',
        ]);
        $this->seeJsonContains([
            'title' => ["The title field is required."]
        ]);
    }

    /**
     * /api/tasks/id [PUT]
     */
    public function test_should_update_task_with_few_fields(): void
    {
        $updatedValue = [
            'title' => "Go to the grocery for shopping",
            'description' => '2 carrots, 4 onions, 3 tomatos, parsley',
            'user_id' => 1
        ];

        $this->put('/api/tasks/1', $updatedValue, []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'title',
            'category',
            'description',
            'user_id',
        ]);
        $this->seeJsonContains($updatedValue);
    }

    /**
     * /api/tasks/id [PUT]
     */
    public function test_should_return_error_when_user_id_is_null(): void
    {
        $updatedValue = [
            'title' => "Go to the grocery for shopping",
        ];

        $this->put('/api/tasks/1', $updatedValue, []);
        $this->seeStatusCode(422);
        $this->seeJsonContains([
            "user_id" => ["The user id field is required."]
        ]);
    }

    /**
     * /api/tasks/id [PUT]
     */
    public function test_should_return_error_when_edited_task_does_not_exist()
    {
        $updatedValue = [
            'title' => "Go to the grocery for shopping",
            'user_id' => 1
        ];

        $this->put('/api/tasks/99999', $updatedValue, []);
        $this->seeStatusCode(404);
    }

    /**
     * /api/tasks/id [PUT]
     */
    public function test_should_return_task_when_task_exist_field_not_exits()
    {
        $updatedValue = [
            'title' => 'Buy new phone',
            'user_id' => 1,
            'phone_number' => '123456789'
        ];

        $this->put("/api/tasks/1", $updatedValue, []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'title',
            'category',
            'description',
            'user_id',
        ]);
    }

    /**
     * /api/tasks/id [DELETE]
     */
    public function test_should_delete_task(): void
    {
        $this->delete('api/tasks/11', [], []);
        $this->seeStatusCode(200);
        $this->seeJsonEquals(['message' => 'Delete succesfully']);
    }

    /**
     * /api/tasks/id [DELETE]
     */
    public function test_should_return_error_when_deleted_task_does_not_exist()
    {
        $this->delete('/api/tasks/2222', [], []);
        $this->seeStatusCode(404);
    }
}
