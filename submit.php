<?php
session_start();
if(!isset($_SESSION['siswa']) || !isset($_GET['id'])) {
   header('location:./');
}

define('BASEPATH', dirname(__FILE__));

require('./include/connection.php');

$thn     = date('Y');
$dpn     = date('Y') + 1;
$periode = $thn.'/'.$dpn;
$suara   = $_GET['s'] + 1;

// 1. UPDATE SUARA KANDIDAT (Kode Bawaan)
$update  = $con->prepare("UPDATE t_kandidat SET suara = ? WHERE id_kandidat = ?") or die($con->error);
$update->bind_param('is', $suara, $_GET['id']);
$update->execute();

// 2. SIMPAN DATA KE TABEL PEMILIH (Kode Bawaan)
$save = $con->prepare("INSERT INTO t_pemilih(nis, periode) VALUES(?,?)") or die($con->error);
$save->bind_param('ss', $_SESSION['siswa'], $periode);
$save->execute();

// 3. --- FITUR BARU: LACAK PILIHAN ---
// Update status siswa jadi 'Sudah Memilih' dan simpan ID Kandidat yang dia coblos
$id_kandidat_dipilih = $_GET['id'];
$nis_pemilih = $_SESSION['siswa'];

$update_user = $con->prepare("UPDATE t_user SET pemilih = 'Sudah Memilih', pilihan_paslon = ? WHERE id_user = ?") or die($con->error);
$update_user->bind_param('is', $id_kandidat_dipilih, $nis_pemilih);
$update_user->execute();
// ------------------------------------

unset($_SESSION['siswa']);

header('location:./index.php?page=thanks');
?>