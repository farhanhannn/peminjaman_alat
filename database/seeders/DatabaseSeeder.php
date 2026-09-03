<?php

namespace Database\Seeders;

use App\Models\{Category, Loan, Tool, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(['email' => 'admin@gmail.com'], ['name'=>'Administrator','password'=>Hash::make('admin123'),'role'=>'admin','is_active'=>true]);
        $petugas = User::updateOrCreate(['email' => 'petugas@gmail.com'], ['name'=>'Petugas Gudang','password'=>Hash::make('petugas123'),'role'=>'petugas','is_active'=>true]);
        $peminjam = User::updateOrCreate(['email' => 'peminjam@gmail.com'], ['name'=>'Peminjam Demo','password'=>Hash::make('peminjam123'),'role'=>'peminjam','is_active'=>true]);

        $elektronik = Category::updateOrCreate(['name'=>'Elektronik'], ['description'=>'Peralatan elektronik untuk kegiatan operasional.']);
        $produksi = Category::updateOrCreate(['name'=>'Peralatan Produksi'], ['description'=>'Alat pendukung kegiatan produksi dan praktik.']);
        $lapangan = Category::updateOrCreate(['name'=>'Peralatan Lapangan'], ['description'=>'Peralatan untuk kegiatan di luar ruangan.']);

        Tool::updateOrCreate(['code'=>'ALT-001'], ['category_id'=>$elektronik->id,'name'=>'Proyektor Epson','description'=>'Proyektor LCD untuk presentasi.','stock'=>5,'available_stock'=>5,'condition'=>'baik','daily_fine'=>10000,'is_active'=>true]);
        Tool::updateOrCreate(['code'=>'ALT-002'], ['category_id'=>$elektronik->id,'name'=>'Laptop Lenovo','description'=>'Laptop untuk kegiatan administrasi.','stock'=>8,'available_stock'=>8,'condition'=>'baik','daily_fine'=>15000,'is_active'=>true]);
        Tool::updateOrCreate(['code'=>'ALT-003'], ['category_id'=>$produksi->id,'name'=>'Kamera Digital','description'=>'Kamera digital untuk dokumentasi.','stock'=>3,'available_stock'=>3,'condition'=>'baik','daily_fine'=>20000,'is_active'=>true]);
        Tool::updateOrCreate(['code'=>'ALT-004'], ['category_id'=>$lapangan->id,'name'=>'Tripod','description'=>'Tripod kamera.','stock'=>6,'available_stock'=>6,'condition'=>'baik','daily_fine'=>5000,'is_active'=>true]);

        if (! Loan::where('loan_code','PJM-DEMO-001')->exists()) {
            $tool = Tool::where('code','ALT-001')->first();
            Loan::create(['loan_code'=>'PJM-DEMO-001','borrower_id'=>$peminjam->id,'tool_id'=>$tool->id,'quantity'=>1,'loan_date'=>now()->toDateString(),'due_date'=>now()->addDays(7)->toDateString(),'status'=>'pending','fine'=>0,'notes'=>'Data contoh untuk pengujian.']);
        }
    }
}
