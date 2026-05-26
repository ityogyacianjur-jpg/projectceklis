<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Tambahkan untuk akses file system
use Illuminate\Support\Str; // Tambahkan untuk membuat nama file acak (UUID)

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
            foreach ($items as $item) {
                $fotoPath = $item['foto'] ?? null;

                // 1. Cek apakah foto berupa string Base64 (gambar baru diunggah)
                if ($fotoPath && preg_match('/^data:image\/(\w+);base64,/', $fotoPath, $type)) {
                    
                    // Ambil ekstensi file gambar (contoh: jpeg, png)
                    $extension = strtolower($type[1]);
                    
                    // Buang bagian awal (prefix) 'data:image/...;base64,' agar tersisa data murninya
                    $image_data = substr($fotoPath, strpos($fotoPath, ',') + 1);
                    $image_data = base64_decode($image_data);

                    // 2. Buat nama file unik (contoh: checklists/9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d.jpeg)
                    $fileName = 'checklists/' . Str::uuid() . '.' . $extension;

                    // 3. Simpan file fisik ke folder storage/app/public/checklists
                    Storage::disk('public')->put($fileName, $image_data);

                    // 4. Ubah nilai fotoPath menjadi URL relatif untuk disimpan di database
                    $fotoPath = 'storage/' . $fileName; 
                }

                // Update setiap baris berdasarkan ID
                Checklist::where('id', $item['id'])->update([
                    'status' => $item['status'] ?? null,
                    'komentar' => $item['komentar'] ?? null,
                    'foto' => $fotoPath // Sekarang hanya menyimpan teks pendek seperti 'storage/checklists/nama-file.jpeg'
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Data dan foto berhasil disimpan!']);
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