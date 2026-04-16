<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContainerController extends Controller
{
    private $containers = [
        [
            'container_id' => 'AB00001',
            'waste_type'   => 'Chemical',
            'weight_kg'    => 500,
            'status'       => 'Active',
            'tracking_logs' => [
                ['location' => 'Gudang A', 'timestamp' => '2026-04-01 08:00:00', 'description' => 'Masuk gudang'],
                ['location' => 'Jalur B',  'timestamp' => '2026-04-02 10:00:00', 'description' => 'Dalam perjalanan'],
            ],
        ],
        [
            'container_id' => 'CD00002',
            'waste_type'   => 'Organic',
            'weight_kg'    => 1200,
            'status'       => 'Active',
            'tracking_logs' => [
                ['location' => 'Gudang C', 'timestamp' => '2026-04-03 09:00:00', 'description' => 'Masuk gudang'],
            ],
        ],
        [
            'container_id' => 'EF00003',
            'waste_type'   => 'Radioactive',
            'weight_kg'    => 300,
            'status'       => 'Archived',
            'tracking_logs' => [],
        ],
    ];

    public function index()
    {
        return response()->json($this->containers, 200);
    }

    public function show($id)
    {
        $container = $this->findContainer($id);
        if (!$container) {
            return response()->json(['message' => 'Container not found'], 404);
        }
        return response()->json($container, 200);
    }

    public function store(Request $request)
    {
        $errors = $this->validate($request);
        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        $new = [
            'container_id'  => $request->container_id,
            'waste_type'    => $request->waste_type,
            'weight_kg'     => $request->weight_kg,
            'status'        => 'Active',
            'tracking_logs' => [],
        ];

        return response()->json($new, 201);
    }

    public function update(Request $request, $id)
    {
        $container = $this->findContainer($id);
        if (!$container) {
            return response()->json(['message' => 'Container not found'], 404);
        }
        $container['status'] = 'Archived';
        return response()->json($container, 200);
    }

    public function destroy($id)
    {
        $container = $this->findContainer($id);
        if (!$container) {
            return response()->json(['message' => 'Container not found'], 404);
        }
        return response()->json(['message' => 'Container deleted'], 200);
    }

    public function search(Request $request)
    {
        $results = $this->containers;

        if ($request->has('type')) {
            $results = array_filter($results, fn($c) => strtolower($c['waste_type']) === strtolower($request->type));
        }

        if ($request->has('min_weight')) {
            $results = array_filter($results, fn($c) => $c['weight_kg'] >= $request->min_weight);
        }

        return response()->json(array_values($results), 200);
    }

    public function logs($id)
    {
        $container = $this->findContainer($id);
        if (!$container) {
            return response()->json(['message' => 'Container not found'], 404);
        }
        return response()->json($container['tracking_logs'], 200);
    }

    private function findContainer($id)
    {
        foreach ($this->containers as $c) {
            if ($c['container_id'] === $id) return $c;
        }
        return null;
    }

    private function validate(Request $request): array
    {
        $errors = [];

        
        if (!preg_match('/^[A-Za-z]{2}[0-9]{5}$/', $request->container_id ?? '')) {
            $errors['container_id'][] = 'Format harus 2 huruf + 5 angka (contoh: AB00001)';
        }

      
        if ($this->findContainer($request->container_id)) {
            $errors['container_id'][] = 'Container ID sudah ada';
        }

      
        $w = $request->weight_kg;
        if (!is_numeric($w) || $w < 10 || $w > 5000) {
            $errors['weight_kg'][] = 'Berat harus antara 10 sampai 5000 kg';
        }

        if (strtolower($request->waste_type) === 'chemical' && is_numeric($w) && $w > 1000) {
            $errors['weight_kg'][] = 'Untuk waste_type Chemical, berat maksimal 1000 kg';
        }

        return $errors;
    }
}