<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Tambahkan untuk akses file system
use Illuminate\Support\Str; // Tambahkan untuk membuat nama file acak (UUID)
use Illuminate\Support\Facades\DB; // 1. Tambahkan Facade DB

class ChecklistController extends Controller
{
    // Menampilkan halaman utama
    public function index()
    {
        return view('checklist');
    }

    // Mengambil data untuk frontend
    public function getData()
    {
        $data = Checklist::all();
        return response()->json($data);
    }

    // Menyimpan data dari frontend ke MySQL
    public function saveData(Request $request)
    {
        $items = $request->input('data');

        if ($items) {
            // 2. Mulai Transaksi Database
            DB::beginTransaction(); 
            
            try {
                foreach ($items as $item) {
                    $fotoPath = $item['foto'] ?? null;

                    // Logika penyimpanan foto ke storage lokal
                    if ($fotoPath && preg_match('/^data:image\/(\w+);base64,/', $fotoPath, $type)) {
                        $extension = strtolower($type[1]);
                        $image_data = substr($fotoPath, strpos($fotoPath, ',') + 1);
                        $image_data = base64_decode($image_data);
                        
                        $fileName = 'checklists/' . Str::uuid() . '.' . $extension;
                        Storage::disk('public')->put($fileName, $image_data);
                        $fotoPath = 'storage/' . $fileName; 
                    }

                    // Update baris per baris di dalam transaksi (Sangat cepat karena belum di-commit)
                    Checklist::where('id', $item['id'])->update([
                        'status' => $item['status'] ?? null,
                        'komentar' => $item['komentar'] ?? null,
                        'foto' => $fotoPath
                    ]);
                }

                // 3. Simpan (Commit) semua perubahan ke database SEKALIGUS
                DB::commit(); 
                
                return response()->json(['success' => true, 'message' => 'Data dan foto berhasil disimpan']);

            } catch (\Exception $e) {
                // 4. Jika ada 1 saja yang gagal, batalkan SEMUA perubahan (Rollback)
                DB::rollBack(); 
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada data untuk disimpan.'], 400);
    }


    // Reset data
    public function resetData()
    {
        // Opsional: Jika ingin menghapus semua file fisik foto saat di-reset
        // Storage::disk('public')->deleteDirectory('checklists');
        
        Checklist::query()->update([
            'status' => null,
            'komentar' => null,
            'foto' => null
        ]);
        return response()->json(['success' => true, 'message' => 'Data berhasil direset!']);
    }

    // Menambahkan poin pengecekan baru
    public function addItem(Request $request)
    {
        $request->validate([
            'item' => 'required|string|max:255'
        ]);

        $newItem = Checklist::create([
            'item' => $request->item,
            'status' => null,
            'komentar' => '',
            'foto' => null
        ]);

        return response()->json(['success' => true, 'message' => 'Poin berhasil ditambahkan!', 'data' => $newItem]);
    }

    // Menghapus poin pengecekan
    public function deleteItem($id)
    {
        $item = Checklist::find($id);
        
        if ($item) {
            // Opsional: Hapus file fisik gambar jika poin dihapus
            // if ($item->foto) {
            //     $path = str_replace('storage/', '', $item->foto);
            //     Storage::disk('public')->delete($path);
            // }

            $item->delete();
            return response()->json(['success' => true, 'message' => 'Poin berhasil dihapus!']);
        }

        return response()->json(['success' => false, 'message' => 'Poin tidak ditemukan!'], 404);
    }
}