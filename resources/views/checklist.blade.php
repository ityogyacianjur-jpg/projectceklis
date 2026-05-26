<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}"> <title>Checklist Pajangan + Foto + Filter</title>
<style>
    /* CSS Sama persis dengan aslinya */
    * { box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    body { background: #f0f2f5; margin: 0; padding: 8px; }
   .container { max-width: 1100px; margin: 0 auto; background: white; padding: 12px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
    h1 { text-align: center; color: #1a73e8; margin: 0 0 4px 0; font-size: 18px; }
   .info { text-align: center; color: #5f6368; margin-bottom: 12px; font-size: 12px; }
   .status { padding: 8px; border-radius: 6px; text-align: center; font-weight: 600; margin-bottom: 10px; font-size: 12px; }
   .online { background: #d1f4e0; color: #0d6832; }
   .offline { background: #fadcd9; color: #c5221f; }
   .loading { background: #e8f0fe; color: #1a73e8; }
   .filter-bar { display: flex; gap: 6px; justify-content: center; margin-bottom: 10px; flex-wrap: wrap; }
   .filter-btn { padding: 6px 12px; border: 2px solid #dadce0; background: white; border-radius: 20px; font-size: 12px; cursor: pointer; font-weight: 500; }
   .filter-btn.active { background: #1a73e8; color: white; border-color: #1a73e8; }
   .filter-btn.tidak.active { background: #d93025; border-color: #d93025; }
   .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; table-layout: fixed; }
    th, td { padding: 6px 4px; text-align: left; border-bottom: 1px solid #e8eaed; vertical-align: top; }
    th { background: #1a73e8; color: white; position: sticky; top: 0; font-size: 11px; }
    tr:nth-child(even) { background: #f8f9fa; }
    tr.row-tidak { background: #fce8e6!important; }
   .no { width: 7%; text-align: center; color: #5f6368; }
   .item { width: auto; line-height: 1.4; word-wrap: break-word; white-space: normal; padding-right: 6px; }
   .cek { width: 12%; text-align: center; }
   .foto { width: 18%; text-align: center; display: none; }
   .komentar { width: 30%; display: none; }
   .komentar textarea { width: 100%; padding: 4px; border: 1px solid #dadce0; border-radius: 4px; font-size: 11px; resize: vertical; min-height: 32px; font-family: inherit; }
    input[type="radio"] { width: 16px; height: 16px; accent-color: #1a73e8; margin: 0; }
   .btn-foto { padding: 4px 6px; background: #34a853; color: white; border: none; border-radius: 4px; font-size: 10px; cursor: pointer; }
   .btn-hapus-foto { padding: 2px 5px; background: #d93025; color: white; border: none; border-radius: 3px; font-size: 9px; cursor: pointer; margin-top: 2px; }
   .thumbnail { width: 36px; height: 36px; object-fit: cover; border-radius: 3px; cursor: pointer; border: 1px solid #ddd; display: block; margin: 0 auto; }
   .foto-wrap { display: flex; flex-direction: column; align-items: center; gap: 2px; }
   .actions { margin-top: 12px; display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; }
    button { padding: 8px 12px; border: none; border-radius: 6px; font-size: 13px; cursor: pointer; font-weight: 500; }
   .save { background: #1a73e8; color: white; }
   .reset { background: #d93025; color: white; }
   .export { background: #188038; color: white; }
   .toggle { background: #5f6368; color: white; }
   .summary { margin-top: 10px; padding: 8px; background: #e8f0fe; border-radius: 8px; text-align: center; font-weight: 600; color: #1967d2; font-size: 12px; }
   .ya { color: #188038; font-weight: 600; }
   .tidak { color: #d93025; font-weight: 600; }
    #fileInput { display: none; }
   .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); justify-content: center; align-items: center; }
   .modal img { max-width: 90%; max-height: 90%; border-radius: 8px; }
</style>

</head>
<body>
<div class="container">
  <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                Logout
            </button>
</form>
    <h1>📋 Checklist Pajangan + Foto</h1>
    <div class="info" id="tanggal"></div>
    <div class="info" ><span>Halo, <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->role }})</span></div>
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
  let data = [];
  let currentFotoIndex = null;
  let showDetail = false;
  let currentFilter = 'semua';
  
  // Setup CSRF Token untuk Laravel AJAX Request
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  // Fungsi ambil data awal
  async function fetchData() {
      try {
          const response = await fetch('/api/checklists');
          data = await response.json();
          // Memastikan null/undefined tidak merusak fungsi replace di export
          data = data.map(item => ({
              ...item,
              komentar: item.komentar || ''
          }));
          document.getElementById('status').className = 'status online';
          document.getElementById('status').textContent = '🟢 Online - Data MySQL Tersinkronisasi';
          renderTable();
      } catch (error) {
          document.getElementById('status').className = 'status offline';
          document.getElementById('status').textContent = '🔴 Error: Gagal mengambil data';
      }
  }

  function renderTable() {
    const tbody = document.getElementById('listBody');
    tbody.innerHTML = '';
    
    let filteredData = data;
    if(currentFilter !== 'semua') {
      filteredData = data.filter(row => row.status === currentFilter);
    }
    
    filteredData.forEach((row) => {
      // Kita gunakan index asli array untuk referensi
      const i = data.findIndex(d => d.id === row.id); 
      
      const tr = document.createElement('tr');
      if(row.status === 'Tidak') tr.classList.add('row-tidak');
      tr.innerHTML = `
        <td class="no">${i + 1}</td>
        <td class="item">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 4px;">
                <span>${row.item}</span>
                <button onclick="hapusPoin(${row.id})" style="background: #d93025; color: white; border: none; border-radius: 4px; padding: 2px 6px; font-size: 10px; cursor: pointer;" title="Hapus Poin">🗑️</button>
            </div>
        </td>
        <td class="cek"><input type="radio" name="status${i}" value="Ya" ${row.status === 'Ya'? 'checked' : ''} onchange="updateStatus(${i}, 'Ya')"></td>
        <td class="cek"><input type="radio" name="status${i}" value="Tidak" ${row.status === 'Tidak'? 'checked' : ''} onchange="updateStatus(${i}, 'Tidak')"></td>
        <td class="foto">
          <div class="foto-wrap">
            ${row.foto? `<img src="${row.foto}" class="thumbnail" onclick="showModal('${row.foto}')">
                         <button class="btn-hapus-foto" onclick="hapusFoto(${i})">Hapus</button>` :
                        `<button class="btn-foto" onclick="ambilFoto(${i})">📷 Foto</button>`
            }
          </div>
        </td>
        <td class="komentar"><textarea oninput="updateKomentar(${i}, this.value)" placeholder="Isi temuan...">${row.komentar}</textarea></td>
      `;
      tbody.appendChild(tr);
    });
    updateSummary();
    updateFilterButtons();
    setTanggal();
    updateDetailView();
  }

  window.setFilter = (filter) => {
    currentFilter = filter;
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    renderTable();
  }

  function updateFilterButtons() {
    const ya = data.filter(d => d.status === 'Ya').length;
    const tidak = data.filter(d => d.status === 'Tidak').length;
    document.querySelectorAll('.filter-btn')[0].textContent = `Semua (${data.length})`;
    document.querySelectorAll('.filter-btn')[1].textContent = `✅ Ya (${ya})`;
    document.querySelectorAll('.filter-btn')[2].textContent = `❌ Tidak (${tidak})`;
  }

  window.toggleDetail = () => {
    showDetail = !showDetail;
    updateDetailView();
    document.querySelector('.toggle').textContent = showDetail? '👁️ Sembunyikan' : '📷👁️ Tampil Foto/Komentar';
  }

  function updateDetailView() {
    document.querySelectorAll('.foto,.komentar').forEach(col => col.style.display = showDetail? 'table-cell' : 'none');
  }

  window.ambilFoto = (i) => {
    currentFotoIndex = i;
    document.getElementById('fileInput').click();
  }

  window.hapusFoto = (i) => {
    if (confirm('Yakin hapus foto ini?')) {
      data[i].foto = null;
      renderTable();
    }
  }

  document.getElementById('fileInput').onchange = function(e) {
    const file = e.target.files[0];
    if (!file || currentFotoIndex === null) return;
    const reader = new FileReader();
    reader.onload = function(event) {
      const img = new Image();
      img.onload = function() {
        const canvas = document.createElement('canvas');
        const maxWidth = 800;
        const scale = maxWidth / img.width;
        canvas.width = maxWidth;
        canvas.height = img.height * scale;
        canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
        data[currentFotoIndex].foto = canvas.toDataURL('image/jpeg', 0.7);
        renderTable();
      };
      img.src = event.target.result;
    };
    reader.readAsDataURL(file);
    e.target.value = '';
  };

  window.showModal = (src) => {
    document.getElementById('modalImg').src = src;
    document.getElementById('modal').style.display = 'flex';
  }

  window.updateStatus = (i, status) => {
    data[i].status = status;
    renderTable();
  }

  window.updateKomentar = (i, value) => {
    data[i].komentar = value;
  }

  function updateSummary() {
    const total = data.length;
    const ya = data.filter(d => d.status === 'Ya').length;
    const tidak = data.filter(d => d.status === 'Tidak').length;
    const belum = total - ya - tidak;
    const adaFoto = data.filter(d => d.foto).length;
    document.getElementById('summary').innerHTML =
      `Total: ${total} | <span class="ya">Ya: ${ya}</span> | <span class="tidak">Tidak: ${tidak}</span> | Foto: ${adaFoto} | Belum: ${belum}`;
  }

  function setTanggal() {
    const tgl = new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    document.getElementById('tanggal').textContent = `Dicheck pada: ${tgl}`;
  }

  // SIMPAN KE MYSQL MELALUI LARAVEL
  window.simpanKeDatabase = async () => {
    const btn = document.getElementById('btnSave');
    btn.textContent = "⏳ Menyimpan...";
    
    try {
        const response = await fetch('/api/checklists', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ data: data })
        });
        
        const result = await response.json();
        if(result.success) {
            alert('✅ ' + result.message);
        } else {
            alert('❌ Gagal menyimpan data');
        }
    } catch (error) {
        alert('🔴 Error jaringan saat menyimpan data');
    }
    btn.textContent = "💾 Simpan ke Database";
  }

  window.resetData = async () => {
    if (confirm('Yakin reset semua? Data & foto akan hilang permanen.')) {
        try {
            const response = await fetch('/api/checklists/reset', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            });
            const result = await response.json();
            if(result.success) {
                // Refresh data
                fetchData();
            }
        } catch(e) {
            alert('Gagal mereset data server');
        }
    }
  }

  window.exportData = () => {
    let csv = 'No,Poin Pengecekan,Status,Komentar,Ada Foto,Tanggal Export\n';
    const tglExport = new Date().toLocaleString('id-ID');
    data.forEach((row, i) => {
      // Pastikan komentar ada dan aman untuk CSV
      const amanKomentar = (row.komentar || "").replace(/"/g, '""');
      csv += `${i + 1},"${row.item}","${row.status || 'Belum dicek'}","${amanKomentar}","${row.foto? 'Ya' : 'Tidak'}","${tglExport}"\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `Checklist-Foto-${new Date().toISOString().slice(0,10)}.csv`;
    a.click();
  }

  // --- FUNGSI TAMBAH POIN ---
  window.tambahPoin = async () => {
    const input = document.getElementById('newItemName');
    const itemName = input.value.trim();
    
    if(!itemName) {
        alert('⚠️ Nama poin pengecekan tidak boleh kosong!');
        return;
    }

    try {
        const response = await fetch('/api/checklists/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ item: itemName })
        });
        const result = await response.json();
        
        if(result.success) {
            input.value = ''; // Kosongkan input
            fetchData(); // Render ulang tabel dari server
        } else {
            alert('❌ Gagal menambahkan poin.');
        }
    } catch (e) {
        alert('🔴 Error jaringan saat menambahkan poin.');
    }
  }

  // --- FUNGSI HAPUS POIN ---
  window.hapusPoin = async (id) => {
    if(!confirm('⚠️ Yakin ingin menghapus poin ini permanen?')) return;

    try {
        const response = await fetch(`/api/checklists/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });
        const result = await response.json();
        
        if(result.success) {
            fetchData(); // Render ulang tabel dari server
        } else {
            alert('❌ Gagal menghapus poin.');
        }
    } catch(e) {
        alert('🔴 Error jaringan saat menghapus poin.');
    }
  }
  
  // Panggil data saat pertama kali load
  fetchData();
</script>
</body>
</html>