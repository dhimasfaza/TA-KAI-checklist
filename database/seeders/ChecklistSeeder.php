<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Checklist,ChecklistCategory,ChecklistItem,Location};

class ChecklistSeeder extends Seeder
{
    public function run(): void
    {
        // Contoh lokasi
        Location::insert([
            ['name'=>'Depo A','city'=>'Kota A','code'=>'DPA','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Depo B','city'=>'Kota B','code'=>'DPB','created_at'=>now(),'updated_at'=>now()],
        ]);

        $chk = Checklist::create([
            'name' => 'SC Hub Warehousing Walkthrough',
            'description' => 'Checklist inspeksi gudang (MVP)',
            'is_active' => true,
        ]);

        $data = [
            'Kondisi Fisik Gudang' => [
                'Tidak ada kebocoran',
                'Kebersihan selalu terjaga',
                'Keamanan & kunci rapi/terkunci',
            ],
            'Pencatatan Barang Persediaan' => [
                'Kartu Barang (I.3C) lengkap',
                'Kesuaian pencatatan vs barang (sampling acak)',
            ],
            'Penempatan Barang Persediaan' => [
                'Barang rapi & tidak menutup akses',
                'Label penempatan lengkap & sesuai',
                'Barang bersegel/perishable sesuai aturan',
            ],
            'Penanganan Barang Persediaan' => [
                'SOP penanganan tersedia & dipatuhi',
                'Per packing sesuai jumlah',
                'Barang ditempatkan aman & tidak terkena air',
            ],
            'Stok Opname Berkala' => [
                'Dilakukan sesuai kebijakan harian/bulanan',
                'Aging > 3 bulan ditandai',
                'Selisih didokumentasikan dan ditindaklanjuti',
            ],
            'Fasilitas Pergudangan' => [
                'Rak/penyimpanan memadai',
                'Peralatan bantu (hand pallet) berfungsi',
                'Penerangan & ventilasi cukup',
            ],
            'Safety' => [
                'APAR/Fire Hydrant tersedia & dicek rutin',
                'Helm/APD tersedia & digunakan',
                'Gudang dilengkapi CCTV',
                'Akses keluar/masuk terkendali',
                'Kunci/gembok memadai',
            ],
            'Pengelolaan Barang Scrap' => [
                'ATDO/ATF tersimpan aman & rapi',
                'Barang bekas tertata & diberi label',
                'Limbah B3 dikelola sesuai aturan',
                'Dokumen lingkungan lengkap',
            ],
        ];

        $orderCat = 1;
        foreach ($data as $catTitle => $items) {
            $cat = ChecklistCategory::create([
                'checklist_id' => $chk->id,
                'title' => $catTitle,
                'sort_order' => $orderCat++,
            ]);
            $orderItem = 1;
            foreach ($items as $it) {
                ChecklistItem::create([
                    'checklist_category_id' => $cat->id,
                    'title' => $it,
                    'sort_order' => $orderItem++,
                ]);
            }
        }
    }
}