<!DOCTYPE html>
<html lang="id">
@extends('index') @section('title', 'Halaman Pengisian Ceklis')

@section('content')
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}"> <title>Checklist Pajangan + Foto + Filter</title>
<link rel="stylesheet" href="{{ asset('css/checklist.css') }}">

</head>
<body>


<div class="container">
    <h1>📋 Checklist Pajangan + Foto</h1>
    <div class="info" id="tanggal"></div>
    <div id="status" class="status loading">🔄 Mengambil data dari MySQL...</div>

    <div style="display: flex; gap: 8px; justify-content: center; margin-bottom: 15px;">
        <input type="text" id="newItemName" placeholder="Ketik poin pengecekan baru..." style="padding: 8px 12px; border: 1px solid #dadce0; border-radius: 20px; width: 60%; font-size: 13px; outline: none;">
        <button onclick="tambahPoin()" style="background: #34a853; color: white; border: none; border-radius: 20px; padding: 8px 16px; cursor: pointer; font-size: 13px; font-weight: 500;">➕ Tambah</button>
    </div>

    <div class="filter-bar">
        <button class="filter-btn active" onclick="setFilter('semua')">Semua</button>
        <button class="filter-btn" onclick="setFilter('Ya')">✅ Ya</button>
        <button class="filter-btn tidak" onclick="setFilter('Tidak')">❌ Tidak</button>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th class="no">No</th>
                    <th class="item">Poin Pengecekan</th>
                    <th class="cek">Ya</th>
                    <th class="cek">Tidak</th>
                    <th class="foto">Foto Temuan</th>
                    <th class="komentar">Komentar</th>
                </tr>
            </thead>
            <tbody id="listBody"></tbody>
        </table>
    </div>

    <div class="summary" id="summary">Memuat data...</div>

    <div class="actions">
        <button class="save" onclick="simpanKeDatabase()" id="btnSave">💾 Simpan ke Database</button>
        <button class="export" onclick="exportData()">📊 Export CSV</button>
        <button class="toggle" onclick="toggleDetail()">📷👁️ Tampil Foto/Komentar</button>
        <button class="reset" onclick="resetData()">🔄 Reset Semua</button>
    </div>
</div>

<input type="file" id="fileInput" accept="image/*" capture="environment">
<div class="modal" id="modal" onclick="this.style.display='none'">
    <img id="modalImg" src="">
</div>

<script>
        window.appConfig = {
            csrfToken: '{{ csrf_token() }}',
            // Pastikan URL di bawah ini sesuai dengan rute (Route) di web.php Anda
            urlGetData: '{{ url("/get-data") }}',       
            urlSaveData: '{{ url("/save-data") }}',      
            urlAddItem: '{{ url("/add-item") }}',        
            urlResetData: '{{ url("/reset-data") }}'     
        };
    </script>

    <script src="{{ asset('js/checklist.js') }}"></script>
</body>
@endsection
</html>