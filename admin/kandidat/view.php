<?php
if(!isset($_SESSION['id_admin'])) {
   header('location: ../');
   exit;
}

$id = strip_tags(mysqli_real_escape_string($con, $_GET['id']));
$sql = $con->prepare("SELECT id_kandidat, nama_calon, foto, visi, misi, suara, periode, program, no_k, detail_data FROM t_kandidat WHERE id_kandidat = ?") or die($con->error);
$sql->bind_param('i', $id);
$sql->execute();
$sql->store_result();
$sql->bind_result($id, $nama, $foto, $visi, $misi, $suara, $periode, $program, $no_k, $detail_data);
$sql->fetch();
?>

<h3 class="pl-5 pt-5 pb-3">Detail Kandidat</h3>
<hr class="ml-5 mr-5"/>

<div class="card mb-3 bg-transparent ml-5 mt-3" style="max-width: 95%;">
  <div class="row no-gutters">
    
    <div class="col-md-4 text-center">
      <div class="p-3">
         <img src="../assets/img/kandidat/<?php echo $foto; ?>" 
              alt="<?php echo $nama; ?>" 
              style="max-height: 350px; width: auto; max-width: 100%; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
      </div>
    </div> 

    <div class="col-md-8">
      <div class="card-body">
         <table class="table table-hover" style="font-size: 15px;">
            <tbody>
               <tr>
                  <td width="160" style="font-weight:bold;">No Kandidat</td>
                  <td>: <strong><?php echo $no_k; ?></strong></td>
               </tr>
               <tr>
                  <td style="font-weight:bold;">Nama Kandidat</td>
                  <td>: <strong><?php echo $nama; ?></strong></td>
               </tr>
               
               <tr>
                  <td style="vertical-align: top; font-weight:bold;">Detail Data Calon</td>
                  <td style="vertical-align: top; text-align: justify; line-height: 1.6;">
                     <div style="display:flex;">
                        <span style="margin-right: 8px;">:</span>
                        <span><?php echo nl2br(!empty($detail_data) ? $detail_data : '-'); ?></span>
                     </div>
                  </td>
               </tr>

               <tr>
                  <td style="vertical-align: top; font-weight:bold;">Visi</td>
                  <td style="vertical-align: top; text-align: justify; line-height: 1.6;">
                     <div style="display:flex;">
                        <span style="margin-right: 8px;">:</span>
                        <span><?php echo nl2br($visi); ?></span>
                     </div>
                  </td>
               </tr>

               <tr>
                  <td style="vertical-align: top; font-weight:bold;">Misi</td>
                  <td style="vertical-align: top; text-align: justify; line-height: 1.6;">
                     <div style="display:flex;">
                        <span style="margin-right: 8px;">:</span>
                        <span><?php echo nl2br($misi); ?></span>
                     </div>
                  </td>
               </tr>
               
               <tr>
                  <td style="vertical-align: top; font-weight:bold;">Program</td>
                  <td style="vertical-align: top; text-align: justify; line-height: 1.6;">
                     <div style="display:flex;">
                        <span style="margin-right: 8px;">:</span>
                        <span><?php echo nl2br($program); ?></span>
                     </div>
                  </td>
               </tr>

               <tr>
                  <td style="font-weight:bold;">Jumlah Suara</td>
                  <td>: <?php echo $suara; ?></td>
               </tr>
               <tr>
                  <td style="font-weight:bold;">Periode</td>
                  <td>: <?php echo $periode; ?></td>
               </tr>
            </tbody>
         </table>
      </div>
   </div>
 </div>

 <div class="col-md-12" style="margin-bottom: 30px; margin-top: 20px; padding-left: 30px;">
        <a href="?page=kandidat&action=edit&id=<?php echo $id; ?>" class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>
        <a href="?page=kandidat" class="btn btn-danger btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
 </div>
</div>