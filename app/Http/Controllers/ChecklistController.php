<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    // Menampilkan halaman utama
    public function index()
    {
        return view('checklist');
    }

    // Mengambil data untuk frontend (menggantikan fungsi onValue Firebase)
    public function getData()
    {
        $data = Checklist::all();
        return response()->json($data);
    }

    // Menyimpan data dari frontend ke MySQL (menggantikan set() Firebase)
    public function saveData(Request $request)
    {
        $items = $request->input('data');

        if ($items) {
            foreach ($items as $item) {
                // Update setiap baris berdasarkan ID
                Checklist::where('id', $item['id'])->update([
                    'status' => $item['status'],
                    'komentar' => $item['komentar'],
                    'foto' => $item['foto']
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan ke Database!']);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada data untuk disimpan.'], 400);
    }

    // Reset data
    public function resetData()
    {
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
            $item->delete();
            return response()->json(['success' => true, 'message' => 'Poin berhasil dihapus!']);
        }

        return response()->json(['success' => false, 'message' => 'Poin tidak ditemukan!'], 404);
    }
}