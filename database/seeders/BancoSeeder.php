<?php

namespace Database\Seeders;

use App\Models\Banco;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BancoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $registros = [
            'BCP',
            'INTERBANK',
            'BBVA',
            'SCOTIABANK',
        ];
        foreach ($registros as $nombre) {
            Banco::create(['nombre' => $nombre]);
        }
    }
}
