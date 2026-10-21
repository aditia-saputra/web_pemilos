<?php
if (!isset($_SESSION['id_admin'])) {
      header('location: ../');
      exit;
}

// Perintah SQL untuk menghapus seluruh data di tabel t_user
$query = "DELETE FROM t_user";
$sql = mysqli_query($con, $query);

if ($sql) {
      echo '<script>alert("Semua data siswa berhasil dihapus!");window.location="dashboard.php?page=user";</script>';
} else {
      echo '<script>alert("Gagal menghapus data siswa!");window.history.go(-1);</script>';
}
?>