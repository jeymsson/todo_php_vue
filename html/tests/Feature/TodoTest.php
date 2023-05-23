<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Todo;
use App\Models\TodoStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseTransactions; // Para gerenciar transações
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;

class TodoTest extends TestCase {
    use DatabaseTransactions, WithFaker;

    private $response_structure;
    private $response_error;
    // private $response_structure_paged;

    // LIKE CONSTRUCTOR
    public function setUp(): void {
        parent::setUp();
        $this->initDatabase();
        $this->setStructure(['id', 'message', 'status_id', 'created_at', 'updated_at']);
        $this->setErrorStructure(['message']);
        // $this->response_structure_paged = ['metadata' => ['page', 'limit', 'sort_by', 'sort_desc', 'total', 'total_pages'], 'result' => [$this->response_structure]];
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
    }
    private function getStructure() : array {
        return $this->response_structure;
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
        $response = $this->get('/api/todos');
        $response->assertJsonStructure([]);
        $response->assertStatus(200);
    }

    /**
     * Check a valid search.
     * @test
     * @depends getAllRegisters
     */
    public function checkIfSearchIsValid(): void
    {
        $data = Todo::inRandomOrder()->first();
        if($data) {
            $response = $this->get('/api/todos/'.$data['id']);

            $response->assertJsonStructure($this->getStructure());
            $response->assertStatus(Response::HTTP_OK);
        } else {
            $this->assertEquals(true, true);
        }
    }

    /**
     * Check a invalid search.
     * @test
     * @depends getAllRegisters
     */
    public function checkIfSearchIsInvalid(): void
    {
        $data = Todo::orderBy('id', 'desc')->first();
        if($data) {
            $id = $data['id']+1;
            $response = $this->get('/api/todos/'.$id);

            $response->assertJsonStructure($this->getErrorStructure());
            $response->assertStatus(Response::HTTP_NOT_FOUND);
        } else {
            $this->assertEquals(true, true);
        }
    }

    /**
     * Check a valid destroy.
     * @test
     * @depends getAllRegisters
     */
    public function checkIfValidDestroy(): void
    {
        $data = Todo::inRandomOrder()->first();
        if($data) {
            $id = $data['id'];
            $response = $this->delete('/api/todos/'.$id);

            $response->assertStatus(Response::HTTP_OK);
        } else {
            $this->assertEquals(true, true);
        }
    }

    /**
     * Check a invalid destroy.
     * @test
     * @depends getAllRegisters
     */
    public function checkIfInvalidDestroy(): void
    {
        $data = Todo::orderBy('id', 'desc')->first();
        if($data) {
            $id = $data['id']+1;
            $response = $this->delete('/api/todos/'.$id);

            $response->assertStatus(Response::HTTP_NOT_FOUND);
        } else {
            $this->assertEquals(true, true);
        }
    }

    /**
     * Check a valid post.
     * @test
     */
    public function checkIfValidPost(): void
    {
        $status = TodoStatus::inRandomOrder()->firstOrFail();

        $header = [
            'Accept' => 'application/json',
        ];
        $content = [
            'message' => $this->faker->name,
            'status_id' => $status['id']
        ];

        $response = $this->post('/api/todos/', $content, $header);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    /**
     * Check a invalid post.
     * @test
     */
    public function checkIfInvalidPost(): void
    {
        $status = TodoStatus::orderBy('id', 'desc')->first();

        $header = [
            'Accept' => 'application/json',
        ];
        $content = [
            'message' => $this->faker->paragraphs,
            'status_id' => $status['id']+1
        ];

        $response = $this->post('/api/todos/', $content, $header);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Check a valid update.
     * @test
     * @depends checkIfSearchIsValid
     */
    public function checkIfValidUpdate(): void
    {
        $data = Todo::inRandomOrder()->first();
        if($data) {
            $status = TodoStatus::inRandomOrder()->firstOrFail();

            $header = [
                'Accept' => 'application/json',
            ];
            $content = [
                'message' => $this->faker->name,
                'status_id' => $status['id']
            ];

            $response = $this->put('/api/todos/'.$data['id'], $content, $header);

            $response->assertStatus(Response::HTTP_ACCEPTED);
        } else {
            $this->assertEquals(true, true);
        }
    }

    /**
     * Check a invalid update.
     * @test
     * @depends checkIfSearchIsValid
     */
    public function checkIfInvalidUpdate(): void
    {
        $data = Todo::inRandomOrder()->first();
        if($data) {
            $status = TodoStatus::orderBy('id', 'desc')->first();

            $header = [
                'Accept' => 'application/json',
            ];
            $content = [
                'message' => $this->faker->paragraphs,
                'status_id' => $status['id']+1
            ];

            $response = $this->put('/api/todos/'.$data['id'], $content, $header);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $this->assertEquals(true, true);
        }
    }
    // check_if_valid_
    // check_if_invalid_
    // check_if_valid_
    // check_if_invalid_
    //

}
