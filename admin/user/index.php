<?php
if (!isset($_SESSION['id_admin'])) {
   header('location: ../');
   exit;
}

if (isset($_GET['action'])) {

   switch ($_GET['action']) {
      case 'tambah':
         include('./user/add.php');
         break;

      case 'edit':
         include('./user/edit.php');
         break;

      case 'import':
         include('./user/import.php');
         break;

      case 'hapus_semua':
         // Kosongkan dulu tabel t_pemilih agar tidak bentrok foreign key
         $del_all_pemilih = mysqli_query($con, "DELETE FROM t_pemilih");
         
         // Setelah itu kosongkan tabel t_user
         $del_all_user = mysqli_query($con, "DELETE FROM t_user");

         if ($del_all_user) {
             echo '<script>alert("Semua data siswa berhasil dihapus!");window.location="dashboard.php?page=user";</script>';
         } else {
             echo '<script>alert("Gagal menghapus data siswa!");window.history.go(-1);</script>';
         }
         break;

      case 'hapus':
         if (isset($_GET['id'])) {
            $nis = strip_tags(mysqli_real_escape_string($con, $_GET['id']));

            // Hapus dulu data pilihannya di t_pemilih (jika ada) agar tidak error foreign key constraint
            $del_pemilih = $con->prepare("DELETE FROM t_pemilih WHERE nis = ?");
            if ($del_pemilih) {
                $del_pemilih->bind_param('s', $nis);
                $del_pemilih->execute();
                $del_pemilih->close();
            }

            // Setelah itu hapus data usernya di t_user
            $sql = $con->prepare("DELETE FROM t_user WHERE id_user = ?");
            if ($sql) {
                $sql->bind_param('s', $nis);
                $sql->execute();
                $sql->close();
            }

            header('location: ?page=user');
            exit;
         } else {
            header('location: ./');
            exit;
         }
         break;

      default:
         include('./user/list.php');
         break;
   }

} else {
   include('./user/list.php');
}
?>