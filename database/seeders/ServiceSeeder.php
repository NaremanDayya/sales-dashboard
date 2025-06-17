<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            'مرشندايزر',
            'بروموتر',
            'مروج تسويقي',
            'مندوب مبيعات',
            'سائق',
            'عامل عادي',
            'مركبة شاملة السائق',
            'مواد دعائية',
            'احتضان قانوني',
        ];

        foreach ($services as $service) {
            Service::create([
                'name' => $service,
                'description' => null,
                'target_percentage' => rand(30, 70),
            ]);
        }
    }
}
