<?php
// Matiin SEMUA warning biar proses mulus
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);

include('koneksi.php');
require 'vendor/autoload.php';
 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
 
$file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

if(isset($_FILES['berkas_excel']['name']) && in_array($_FILES['berkas_excel']['type'], $file_mimes)) {
 
    $arr_file = explode('.', $_FILES['berkas_excel']['name']);
    $extension = end($arr_file);
 
    if('csv' == $extension) {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
    } else {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    }
 
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($_FILES['berkas_excel']['tmp_name']);
    $sheetData = $spreadsheet->getActiveSheet()->toArray();
    
    // KOSONGKAN TABEL LAMA BIAR MULAI FRESH
    mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0");
    mysqli_query($koneksi, "TRUNCATE TABLE t_user");
    mysqli_query($koneksi, "TRUNCATE TABLE t_kelas");
    mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1");

    $kelas_map = [];
    $k_index = 1;
    $seen_nis = [];
    $guru_counter = 1;
    
    // LOOPING MULAI DARI INDEX 5 (Baris ke-6 di Excel karena baris 1-5 itu Kop Surat)
    for($i = 5; $i < count($sheetData); $i++)
    {
        $nama_asli  = isset($sheetData[$i][2]) ? trim($sheetData[$i][2]) : '';
        if ($nama_asli == '') continue; // Kalau namanya kosong, lewati baris ini

        $nama       = mysqli_real_escape_string($koneksi, $nama_asli);
        $jk         = isset($sheetData[$i][3]) ? mysqli_real_escape_string($koneksi, trim($sheetData[$i][3])) : 'L';
        $kelas_asli = isset($sheetData[$i][4]) ? trim($sheetData[$i][4]) : '';
        $pemilih    = 'Belum Memilih';
        
        // 1. OLAH KELAS OTOMATIS
        if (!isset($kelas_map[$kelas_asli]) && $kelas_asli != '') {
            $id_kelas_baru = 'k' . str_pad($k_index, 2, '0', STR_PAD_LEFT);
            $kelas_map[$kelas_asli] = $id_kelas_baru;
            $nama_kelas_safe = mysqli_real_escape_string($koneksi, $kelas_asli);
            
            // Simpan kelas baru ke database
            mysqli_query($koneksi, "INSERT INTO t_kelas (id_kelas, nama_kelas) VALUES ('$id_kelas_baru', '$nama_kelas_safe')");
            $k_index++;
        }
        $id_kelas = isset($kelas_map[$kelas_asli]) ? $kelas_map[$kelas_asli] : 'k01';

        // 2. OLAH NIS/NIP (BUAT FAKE NIS JIKA GURU, DAN TANGANI DUPLIKAT)
        $nis = isset($sheetData[$i][1]) ? trim($sheetData[$i][1]) : '';
        if ($nis == '' || strtolower($nis) == 'nis/nip') {
            $nis = 'GURU' . str_pad($guru_counter, 3, '0', STR_PAD_LEFT);
            $guru_counter++;
        }
        
        $nis_safe = mysqli_real_escape_string($koneksi, $nis);
        $original_nis = $nis_safe;
        $suffix = 1;
        
        // Kalau ketemu NIS yang dobel di Excel, tambahin _1, _2 dst
        while (in_array($nis_safe, $seen_nis)) {
            $nis_safe = $original_nis . '_' . $suffix;
            $suffix++;
        }
        $seen_nis[] = $nis_safe;

        // 3. MASUKKAN DATA SISWA/GURU
        mysqli_query($koneksi, "INSERT IGNORE INTO t_user (id_user, fullname, id_kelas, jk, pemilih) VALUES ('$nis_safe', '$nama', '$id_kelas', '$jk', '$pemilih')");
    }
    
    header("Location: ../dashboard.php?page=user&&pesan=Berhasil"); 
}
?>