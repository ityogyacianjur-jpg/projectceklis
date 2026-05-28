<!DOCTYPE html>
<html lang="id">
@extends('index') @section('title', 'Halaman Master User')
@section('content')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master User - Sistem Checklist</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 font-sans antialiased text-gray-800">

<div class="max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold">Master Data User</h2>
            <p class="text-gray-500 text-sm">Kelola hak akses dan akun operasional karyawan</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ url('/') }}" class="border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded text-sm font-medium transition">
                &larr; Kembali
            </a>
            <button onclick="showCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium transition shadow">
                + Tambah User
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-3 w-16">No</th>
                        <th class="px-4 py-3">Nama Lengkap</th>
                        <th class="px-4 py-3">ID Number</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Bergabung Pada</th>
                        <th class="px-4 py-3 text-center w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody" class="divide-y divide-gray-200">
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Memuat data user...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden transition-opacity">
    <div class="bg-white w-full max-w-md mx-4 rounded-lg shadow-xl overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-bold" id="modalTitle">Tambah User Baru</h5>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
        </div>
        
        <form id="userForm" onsubmit="handleFormSubmit(event)">
            <div class="p-6 space-y-4">
                <input type="hidden" id="userId">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="userName" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan nama karyawan" required>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ID Number / NIK</label>
                    <input type="text" id="userIdNumber" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: USR001" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Role / Hak Akses</label>
                    <select id="userRole" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="" disabled selected>Pilih Role...</option>
                        <option value="user">User Biasa</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                    <input type="password" id="userPassword" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p id="passwordHelp" class="text-xs text-gray-500 mt-1">Minimal 6 karakter.</p>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-100 text-sm font-medium transition">Batal</button>
                <button type="submit" id="btnSubmit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium shadow transition">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    const csrfToken = '{{ csrf_token() }}';
    const modalEl = document.getElementById('userModal');

    document.addEventListener("DOMContentLoaded", function() {
        loadUsers();
    });

    // --- LOGIKA MODAL VANILLA JS ---
    function openModal() {
        modalEl.classList.remove('hidden');
    }

    function closeModal() {
        modalEl.classList.add('hidden');
    }
    // -------------------------------

    // Ambil Data User (READ)
    function loadUsers() {
        fetch("{{ url('/api/users') }}")
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('userTableBody');
                tbody.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada data user terdaftar.</td></tr>';
                    return;
                }

                data.forEach((user, index) => {
                    const date = new Date(user.created_at).toLocaleDateString('id-ID');
                    
                    // Desain Badge untuk Role
                    const roleBadge = user.role === 'admin' 
                        ? '<span class="bg-red-100 text-red-800 px-2 py-0.5 rounded text-xs font-semibold">Admin</span>' 
                        : '<span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-semibold">User</span>';

                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3">${index + 1}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">${user.name}</td>
                            <td class="px-4 py-3 font-mono text-gray-600 bg-gray-100 px-1 py-0.5 rounded">${user.id_number}</td>
                            <td class="px-4 py-3">${roleBadge}</td>
                            <td class="px-4 py-3 text-gray-500">${date}</td>
                            <td class="px-4 py-3 text-center">
                                <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs font-medium mr-1 transition" onclick="showEditModal(${JSON.stringify(user).replace(/"/g, '&quot;')})">Edit</button>
                                <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-medium transition" onclick="deleteUser(${user.id})">Hapus</button>
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
        document.getElementById('userRole').value = '';
        document.getElementById('modalTitle').innerText = 'Tambah User Baru';
        document.getElementById('userPassword').required = true;
        document.getElementById('passwordHelp').innerText = 'Password wajib diisi untuk pengguna baru (Min. 6 karakter).';
        openModal();
    }

    // Trigger Modal Edit Data
    function showEditModal(user) {
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = user.id;
        document.getElementById('userName').value = user.name;
        document.getElementById('userIdNumber').value = user.id_number;
        document.getElementById('userRole').value = user.role;
        document.getElementById('modalTitle').innerText = 'Edit Data User';
        document.getElementById('userPassword').required = false;
        document.getElementById('passwordHelp').innerText = 'Biarkan kosong jika tidak ingin mengganti password.';
        openModal();
    }

    // Kirim Form Tambah / Ubah via Fetch API (CREATE & UPDATE)
    function handleFormSubmit(event) {
        event.preventDefault();

        const id = document.getElementById('userId').value;
        const name = document.getElementById('userName').value;
        const id_number = document.getElementById('userIdNumber').value;
        const role = document.getElementById('userRole').value;
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
            body: JSON.stringify({ name, id_number, role, password })
        })
        .then(async res => {
            const result = await res.json();
            
            if (res.ok) {
                alert(result.message);
                closeModal();
                loadUsers();
            } else if (res.status === 422) {
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