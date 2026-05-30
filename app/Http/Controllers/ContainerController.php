<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Container;

class ContainerController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/v1/gateway/containers",
 *     summary="Ambil semua data kontainer",
 *     tags={"Containers"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="Data kontainer berhasil diambil"),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */
    public function index()
    {
        $containers = Container::with('trackingLogs')->get();
        return response()->json($containers, 200);
    }

    public function show($id)
    {
        $container = Container::with('trackingLogs')->where('container_id', $id)->first();
        if (!$container) {
            return response()->json(['message' => 'Container not found'], 404);
        }
        return response()->json($container, 200);
    }

    /**
 * @OA\Post(
 *     path="/api/v1/gateway/containers",
 *     summary="Tambah kontainer baru (Admin only)",
 *     tags={"Containers"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="container_id", type="string", example="GH00005"),
 *             @OA\Property(property="waste_type", type="string", example="Organic"),
 *             @OA\Property(property="weight_kg", type="number", example=200)
 *         )
 *     ),
 *     @OA\Response(response=201, description="Kontainer berhasil ditambahkan"),
 *     @OA\Response(response=403, description="Forbidden"),
 *     @OA\Response(response=422, description="Validasi gagal")
 * )
 */

    public function store(Request $request)
    {
        $errors = $this->validateContainer($request);
        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        $container = Container::create([
            'container_id' => $request->container_id,
            'waste_type'   => $request->waste_type,
            'weight_kg'    => $request->weight_kg,
            'status'       => 'Active',
        ]);

        return response()->json($container, 201);
    }

    public function update(Request $request, $id)
    {
        $container = Container::where('container_id', $id)->first();
        if (!$container) {
            return response()->json(['message' => 'Container not found'], 404);
        }
        $container->status = 'Archived';
        $container->save();
        return response()->json($container, 200);
    }

    public function destroy($id)
    {
        $container = Container::where('container_id', $id)->first();
        if (!$container) {
            return response()->json(['message' => 'Container not found'], 404);
        }
        $container->delete();
        return response()->json(['message' => 'Container deleted'], 200);
    }

    public function search(Request $request)
    {
        $query = Container::with('trackingLogs');

        if ($request->has('type')) {
            $query->where('waste_type', $request->type);
        }

        if ($request->has('min_weight')) {
            $query->where('weight_kg', '>=', $request->min_weight);
        }

        return response()->json($query->get(), 200);
    }

    public function logs($id)
    {
        $container = Container::with('trackingLogs')->where('container_id', $id)->first();
        if (!$container) {
            return response()->json(['message' => 'Container not found'], 404);
        }
        return response()->json($container->trackingLogs, 200);
    }

    private function validateContainer(Request $request): array
    {
        $errors = [];

        if (!preg_match('/^[A-Za-z]{2}[0-9]{5}$/', $request->container_id ?? '')) {
            $errors['container_id'][] = 'Format harus 2 huruf + 5 angka (contoh: AB00001)';
        }

        if (Container::where('container_id', $request->container_id)->exists()) {
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