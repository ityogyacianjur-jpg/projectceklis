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
        const response = await fetch(window.appConfig.urlSaveData, {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': window.appConfig.csrfToken,
        'Content-Type': 'application/json'
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
