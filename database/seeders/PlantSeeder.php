<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class PlantSeeder extends Seeder
{
    public function run(): void
    {
        $plants = [
            ['A010','KANTOR PUSAT'],
            ['B010','DAOP 1 JAKARTA'],
            ['B020','DAOP 2 BANDUNG'],
            ['B030','DAOP 3 CIREBON'],
            ['B040','DAOP 4 SEMARANG'],
            ['B050','DAOP 5 PURWOKERTO'],
            ['B060','DAOP 6 YOGYAKARTA'],
            ['B070','DAOP 7 MADIUN'],
            ['B080','DAOP 8 SURABAYA'],
            ['B090','DAOP 9 JEMBER'],
            ['C010','DIVRE 1 SUMATERA UTARA'],
            ['C020','DIVRE 2 SUMATERA BARAT'],
            ['C031','DIVRE 3 PALEMBANG'],
            ['C032','DIVRE 4 TANJUNGKARANG'],
            ['E010','KANTOR DIVISI LRT JABODEBEK'],
            ['Y010','BALAI YASA MANGGARAI'],
            ['Y020','BALAI YASA TEGAL'],
            ['Y030','BALAI YASA YOGYAKARTA'],
            ['Y040','BALAI YASA SURABAYA-GUBENG'],
            ['Y050','BALAI YASA MEDAN-PULUBRAYAN'],
            ['Y060','BALAI YASA LAHAT'],
        ];

        foreach ($plants as [$code,$name]) {
            Location::updateOrCreate(
                ['code'=>$code],
                ['name'=>$name, 'city'=>null]
            );
        }
    }
}
