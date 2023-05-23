<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\TodoStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseTransactions; // Para gerenciar transações
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;

class TodoStatusTest extends TestCase {
    use DatabaseTransactions, WithFaker;

    private $response_structure;
    private $response_error;
    private $response_structure_paged;

    // LIKE CONSTRUCTOR
    public function setUp(): void {
        parent::setUp();
        $this->initDatabase();
        $this->setStructure(['id', 'description', 'created_at', 'updated_at']);
        $this->setErrorStructure(['message']);
    }

    public function initDatabase(): void {
        // Configura banco na memoria
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);

        // Roda migrations
        Artisan::call('migrate');
        // Roda seeds
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\TodoStatusSeeder']);
    }

    private function setStructure(array $data): void {
        $this->response_structure = $data;
        $this->response_structure_paged = [
            'metadata' => [
              'page',
              'limit',
              'sort_by' => [],
              'sort_desc',
              'total',
              'total_pages'
            ],
            'result' => [$data]
        ];
    }
    private function getStructure() : array {
        return $this->response_structure;
    }
    private function getStructurePaged() : array {
        return $this->response_structure_paged;
    }
    private function setErrorStructure(array $data): void {
        $this->response_error = $data;
    }
    private function getErrorStructure() : array {
        return $this->response_error;
    }

    /**
     * A basic feature test example.
     * @test
     */
    public function getAllRegisters(): void
    {
        $response = $this->get('/api/todo_status');
        $response->assertJsonStructure($this->getStructurePaged());
        $response->assertStatus(200);
    }

    /**
     * Check a valid search.
     * @test
     * @depends getAllRegisters
     */
    public function checkIfSearchIsValid(): void
    {
        $data = TodoStatus::inRandomOrder()->firstOrFail();
        $response = $this->get('/api/todo_status/'.$data['id']);

        $response->assertJsonStructure($this->getStructure());
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Check a invalid search.
     * @test
     * @depends getAllRegisters
     */
    public function checkIfSearchIsInvalid(): void
    {
        $data = TodoStatus::orderBy('id', 'desc')->first();
        $id = $data['id']+1;
        $response = $this->get('/api/todo_status/'.$id);

        $response->assertJsonStructure($this->getErrorStructure());
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * Check a valid destroy.
     * @test
     * @depends getAllRegisters
     */
    public function checkIfValidDestroy(): void
    {
        $data = TodoStatus::inRandomOrder()->firstOrFail();
        $id = $data['id'];
        $response = $this->delete('/api/todo_status/'.$id);

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Check a invalid destroy.
     * @test
     * @depends getAllRegisters
     */
    public function checkIfInvalidDestroy(): void
    {
        $data = TodoStatus::orderBy('id', 'desc')->first();
        $id = $data['id']+1;
        $response = $this->delete('/api/todo_status/'.$id);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * Check a valid post.
     * @test
     */
    public function checkIfValidPost(): void
    {
        $header = [
            'Accept' => 'application/json',
        ];
        $content = [
            'description' => $this->faker->name
        ];

        $response = $this->post('/api/todo_status/', $content, $header);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    /**
     * Check a invalid post.
     * @test
     */
    public function checkIfInvalidPost(): void
    {
        $header = [
            'Accept' => 'application/json',
        ];
        $content = [
            'description' => $this->faker->paragraphs
        ];

        $response = $this->post('/api/todo_status/', $content, $header);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Check a valid update.
     * @test
     * @depends checkIfSearchIsValid
     */
    public function checkIfValidUpdate(): void
    {
        $data = TodoStatus::inRandomOrder()->firstOrFail();

        $header = [
            'Accept' => 'application/json',
        ];
        $content = [
            'description' => $this->faker->name
        ];

        $response = $this->put('/api/todo_status/'.$data['id'], $content, $header);

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }

    /**
     * Check a invalid update.
     * @test
     * @depends checkIfSearchIsValid
     */
    public function checkIfInvalidUpdate(): void
    {
        $data = TodoStatus::inRandomOrder()->firstOrFail();

        $header = [
            'Accept' => 'application/json',
        ];
        $content = [
            'description' => $this->faker->paragraphs
        ];

        $response = $this->put('/api/todo_status/'.$data['id'], $content, $header);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    // check_if_valid_
    // check_if_invalid_
    // check_if_valid_
    // check_if_invalid_
    //

}
