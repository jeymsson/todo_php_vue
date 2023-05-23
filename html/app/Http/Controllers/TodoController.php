<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\Model;
use App\Models\Todo;

class TodoController extends Controller {
    // Returns a message error
    private function getResponseError() : Response {
        return Response(["message" => __('general_lang.response.not_found')], Response::HTTP_NOT_FOUND);
    }
    // Returns a principal model
    private function getModel() : Model {
        return new Todo;
    }
    // Set validations to form
    private function setValidations(Request $request) : void {
        $request->validate([
            'message' => 'required|string|min:3|max:255',
            'status_id' => 'exists:App\Models\TodoStatus,id',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index() {
        $data = $this->getModel()::all();
        return Response($data, Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $this->setValidations($request);

        $data = $this->getModel()::create($request->all());

        return Response($data, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        try {
            return $this->getModel()::findOrFail($id);
        } catch (\Throwable $e) {
            return $this->getResponseError();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {
        $this->setValidations($request);

        $data = null;
        try {
            $data = $this->getModel()::findOrFail($id);
        } catch (\Throwable $e) {
            return $this->getResponseError();
        }

        $data = $data->update($request->all());

        return Response($data, Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        $data = null;

        try {
            $data = $this->getModel()::findOrFail($id);
        } catch (\Throwable $e) {
            return $this->getResponseError();
        }

        $data->delete();

        return Response($data, Response::HTTP_OK);
    }
}
