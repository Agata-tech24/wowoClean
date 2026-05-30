<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Container;
use App\Models\TrackingLog;

class ContainerSeeder extends Seeder
{
    public function run(): void
    {
        $c1 = Container::create([
            'container_id' => 'AB00001',
            'waste_type'   => 'Chemical',
            'weight_kg'    => 500,
            'status'       => 'Active',
        ]);
        TrackingLog::create(['container_id' => $c1->id, 'location' => 'Gudang A', 'timestamp' => '2026-04-01 08:00:00', 'description' => 'Masuk gudang']);
        TrackingLog::create(['container_id' => $c1->id, 'location' => 'Jalur B',  'timestamp' => '2026-04-02 10:00:00', 'description' => 'Dalam perjalanan']);

        $c2 = Container::create([
            'container_id' => 'CD00002',
            'waste_type'   => 'Organic',
            'weight_kg'    => 1200,
            'status'       => 'Active',
        ]);
        TrackingLog::create(['container_id' => $c2->id, 'location' => 'Gudang C', 'timestamp' => '2026-04-03 09:00:00', 'description' => 'Masuk gudang']);

        Container::create([
            'container_id' => 'EF00003',
            'waste_type'   => 'Radioactive',
            'weight_kg'    => 300,
            'status'       => 'Archived',
        ]);
    }
}