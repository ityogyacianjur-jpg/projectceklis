<!DOCTYPE html>
<html lang="id">
@extends('index') @section('title', 'Halaman Master User')
@section('content')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master User - Sistem Checklist</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { padding: 30px; background-color: #f4f6f9; font-family: sans-serif; }
        .card { border: none; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table th { background-color: #212529; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Master Data User</h2>
            <p class="text-muted mb-0">Kelola hak akses dan akun operasional karyawan</p>
        </div>
        <div>
            <a href="{{ url('/') }}" class="btn btn-outline-secondary me-2">← Kembali ke Checklist</a>
            <button class="btn btn-primary" onclick="showCreateModal()">+ Tambah User Baru</button>
        </div>
    </div>

    <div class="card p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="80">No</th>
                        <th>Nama Lengkap</th>
                        <th>ID Number (Username)</th>
                        <th>Bergabung Pada</th>
                        <th width="180" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <tr>
                        <td colspan="5" class="text-center text-muted">Memuat data user...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm" onsubmit="handleFormSubmit(event)">
                <div class="modal-body">
                    <input type="hidden" id="userId">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" id="userName" class="form-control" placeholder="Masukkan nama karyawan" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ID Number / NIK</label>
                        <input type="text" id="userIdNumber" class="form-control" placeholder="Contoh: USR001" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" id="userPassword" class="form-control">
                        <div id="passwordHelp" class="form-text text-muted">Minimal 6 karakter.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const csrfToken = '{{ csrf_token() }}';
    let userModal;

    document.addEventListener("DOMContentLoaded", function() {
        userModal = new bootstrap.Modal(document.getElementById('userModal'));
        loadUsers();
    });

    // Ambil Data User (READ)
    function loadUsers() {
        fetch("{{ url('/api/users') }}")
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('userTableBody');
                tbody.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Belum ada data user terdaftar.</td></tr>';
                    return;
                }

                data.forEach((user, index) => {
                    const date = new Date(user.created_at).toLocaleDateString('id-ID');
                    tbody.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td><strong>${user.name}</strong></td>
                            <td><code>${user.id_number}</code></td>
                            <td>${date}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning me-1" onclick="showEditModal(${JSON.stringify(user).replace(/"/g, '&quot;')})">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">Hapus</button>
                            </td>
                        </tr>
                    `;
                });
            })
            .catch(err => console.error("Gagal mengambil data user:", err));
    }

    // Trigger Modal Tambah Data
    function showCreateModal() {
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('modalTitle').innerText = 'Tambah User Baru';
        document.getElementById('userPassword').required = true;
        document.getElementById('passwordHelp').innerText = 'Password wajib diisi untuk pengguna baru (Min. 6 karakter).';
        userModal.show();
    }

    // Trigger Modal Edit Data
    function showEditModal(user) {
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = user.id;
        document.getElementById('userName').value = user.name;
        document.getElementById('userIdNumber').value = user.id_number;
        document.getElementById('modalTitle').innerText = 'Edit Data User';
        document.getElementById('userPassword').required = false;
        document.getElementById('passwordHelp').innerText = 'Biarkan kosong jika tidak ingin mengganti password.';
        userModal.show();
    }

    // Kirim Form Tambah / Ubah via Fetch API (CREATE & UPDATE)
    function handleFormSubmit(event) {
        event.preventDefault();

        const id = document.getElementById('userId').value;
        const name = document.getElementById('userName').value;
        const id_number = document.getElementById('userIdNumber').value;
        const password = document.getElementById('userPassword').value;

        const url = id ? `{{ url('/api/users') }}/${id}` : `{{ url('/api/users') }}`;
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name, id_number, password })
        })
        .then(async res => {
            const result = await res.json();
            
            if (res.ok) {
                // Berhasil (Status 200 / 201)
                alert(result.message);
                userModal.hide();
                loadUsers();
            } else if (res.status === 422) {
                // Tangkap Validasi Gagal dari Laravel (ID kembar, password kurang panjang, dll)
                let errorAlert = "Gagal memproses data:\n";
                for (const field in result.errors) {
                    errorAlert += `- ${result.errors[field].join(', ')}\n`;
                }
                alert(errorAlert);
            } else {
                alert("Terjadi kesalahan sistem: " + (result.message || "Unknown Error"));
            }
        })
        .catch(err => {
            console.error("Error Request:", err);
            alert("Gagal terhubung ke server.");
        });
    }

    // Hapus User (DELETE)
    function deleteUser(id) {
        if (confirm("Apakah Anda yakin ingin menghapus user ini dari sistem?")) {
            fetch(`{{ url('/api/users') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                alert(res.message);
                if (res.success) loadUsers();
            })
            .catch(err => console.error(err));
        }
    }
</script>

</body>
@endsection

</html>