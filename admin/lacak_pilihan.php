<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['id_admin'])) {
    header('location: ./index.php');
    exit;
}

// Panggil file koneksi 
require '../include/connection.php'; 

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Detail Pilihan - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-100 font-sans p-4 md:p-8">

    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-md overflow-hidden border border-slate-200">
        
        <!-- Header -->
        <div class="bg-slate-800 p-6 flex justify-between items-center text-white">
            <div>
                <h2 class="text-2xl font-bold">Laporan Detail Pilihan Pemilih</h2>
                <p class="text-slate-300 text-sm mt-1">Daftar siswa yang sudah memilih beserta paslon yang dicoblos.</p>
            </div>
            <a href="dashboard.php" class="bg-slate-700 hover:bg-slate-600 px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
            </a>
        </div>

        <!-- Tabel Data -->
        <div class="p-6 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 text-sm uppercase tracking-wider border-b border-slate-200">
                        <th class="p-4 font-semibold rounded-tl-lg">No</th>
                        <th class="p-4 font-semibold">NIS / NIP</th>
                        <th class="p-4 font-semibold">Nama Lengkap</th>
                        <th class="p-4 font-semibold">Status</th>
                        <th class="p-4 font-semibold rounded-tr-lg">Pilihan Paslon</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php
                    // 1. Ambil data kandidat untuk dicocokkan ID-nya dengan nama aslinya
                    $kandidat_map = [];
                    $q_kandidat = mysqli_query($con, "SELECT * FROM t_kandidat");
                    
                    if ($q_kandidat) {
                        while($k = mysqli_fetch_assoc($q_kandidat)) {
                            $kolom = array_values($k); 
                            $id_paslon = $kolom[0];    // ID Kandidat (misal: 12)
                            $nama_paslon = $kolom[1];  // Nama Kandidat
                            $kandidat_map[$id_paslon] = $nama_paslon;
                        }
                    }

                    // =========================================================
                    // 2. INI YANG DIPERBAIKI!
                    // Dulu WHERE pemilih = 'Sudah Memilih'
                    // Sekarang WHERE pilihan_paslon IS NOT NULL (Selama ada ID paslon, tampilkan!)
                    // =========================================================
                    $query_user = mysqli_query($con, "SELECT id_user, fullname, pemilih, pilihan_paslon FROM t_user WHERE pilihan_paslon IS NOT NULL ORDER BY fullname ASC");

                    $no = 1;
                    if($query_user) {
                        if(mysqli_num_rows($query_user) > 0) {
                            while ($row = mysqli_fetch_assoc($query_user)) {
                                
                                $id_pilihan = $row['pilihan_paslon'];
                                $nama_yang_dicoblos = isset($kandidat_map[$id_pilihan]) ? $kandidat_map[$id_pilihan] : 'Data Paslon Terhapus (ID: '.$id_pilihan.')';
                                
                                // Jika kolom status (pemilih) kosong di database, kita kasih label manual di sini
                                $status_tampil = (trim($row['pemilih']) == '') ? 'Sudah Memilih' : $row['pemilih'];
                    ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-4 text-sm text-slate-500"><?= $no++; ?></td>
                        <td class="p-4 text-sm font-medium text-slate-800"><?= htmlspecialchars($row['id_user']); ?></td>
                        <td class="p-4 text-sm text-slate-700 font-semibold"><?= htmlspecialchars($row['fullname']); ?></td>
                        <td class="p-4">
                            <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($status_tampil); ?>
                            </span>
                        </td>
                        <td class="p-4 text-sm font-bold text-blue-600">
                            <?= htmlspecialchars($nama_yang_dicoblos); ?>
                        </td>
                    </tr>
                    <?php 
                            } 
                        } else { 
                    ?>
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-500">
                            <i class="fas fa-box-open text-3xl mb-3 text-slate-300"></i>
                            <p>Belum ada data pemilih yang masuk.</p>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo '<tr><td colspan="5" class="p-8 text-center text-red-500 font-bold">Error Database: ' . mysqli_error($con) . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>