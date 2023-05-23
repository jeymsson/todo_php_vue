<?php

namespace App\Models\Custom;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;

trait ModelCustom {
    public static function allPaged($columns = '*', Model $model, array $parameters = []) {
        // Get parameters
        $page = (int) ($parameters['page'] ?? 1);
        $limit = (int) ($parameters['limit'] ?? 500);
        $sort_by = !empty($parameters['sort_by']) ? explode(',', $parameters['sort_by']) : [];
        $sort_desc = (bool) ($parameters['sort_desc'] ?? false);
        $search = $parameters['search'] ?? null;
        $search_col_name = $parameters['search_col_name'] ?? null;
        // Calculate start
        $start_page = ($page - 1) * $limit;
        $start_in = $parameters['start_in'] ?? 0;
        // Define the foreign tables
        $foreign_tables = (array) ($parameters['foreign_tables'] ?? []);

        // Get connector
        $query = $model::query();

        // Verify pages
        $total = $query->count();
        $totalPages = ceil($total / $limit);

        // Verify and sort columns
        foreach ($sort_by as $sort) {
            $query->orderBy($sort, $sort_desc ? 'desc' : 'asc');
        }

        // Verify and search values
        if(!empty($search) && !empty($search_col_name)) {
            $query->where($search_col_name, 'Like', $search.'%');
        }

        // Limit pages
        $query->offset($start_in + $start_page)
            ->limit($limit);

        // Add custom foreign tables
        if(!empty($foreign_tables)) {
            foreach ($foreign_tables as $val) {
                $query->with($val);
            }
        }

        // Get columns
        $result = $query->get($columns);


        return [
            'metadata' => [
                'limit' => $limit,
                'sort_by' => $sort_by,
                'sort_desc' => $sort_desc,
                'page' => $page,
                'total_pages' => $totalPages,
                'listed' => count($result),
                'total_qtde' => $total,
            ],
            'result' => $result,
        ];
    }

    public static function getIdOrFail($id, array $parameters = []) {
        try {
            // Define the foreign tables
            $foreign_tables = (array) ($parameters['foreign_tables'] ?? []);

            // Get connector
            $query = self::query();

            // Add custom foreign tables
            if(!empty($foreign_tables)) {
                foreach ($foreign_tables as $val) {
                    $query->with($val);
                }
            }

            // Get columns
            $result = $query->findOrFail($id);
        } catch (\Throwable $e) {
            return Response(["message" => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }
        return $result;
    }
}
