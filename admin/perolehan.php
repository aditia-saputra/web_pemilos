<?php
// Tentukan periode aktif
$thn = date('Y');
$dpn = date('Y') + 1;
$periode = $thn . '/' . $dpn;

// Ambil data kandidat dan suaranya dari database berdasarkan periode aktif
$query_kan = "SELECT nama_calon, suara FROM t_kandidat WHERE periode = '$periode'";
$result_kan = mysqli_query($con, $query_kan);

$labels_arr = [];
$data_arr = [];

if ($result_kan && mysqli_num_rows($result_kan) > 0) {
    while ($row = mysqli_fetch_assoc($result_kan)) {
        $labels_arr[] = "'" . $row['nama_calon'] . "'";
        $data_arr[] = $row['suara'];
    }
}

$labels_js = implode(',', $labels_arr);
$data_js = implode(',', $data_arr);
$jumlah_kandidat = count($data_arr);
?>

<div class="text-right mr-5 mt-5">
    <button id="save-img" class="btn btn-success" <?php echo ($jumlah_kandidat == 0) ? 'style="display:none;"' : ''; ?>>Simpan Grafik</button>
</div>

<div style="font-size:18px;" class="ml-5 mr-5 mb-5">
    <?php if ($jumlah_kandidat > 0) { ?>
        <canvas id="canvas"></canvas>
    <?php } else { ?>
        <div class="alert alert-warning text-center py-5 font-weight-bold" style="font-size: 16px; border-radius: 12px;">
            <i class="fas fa-exclamation-triangle mr-2"></i> Belum ada data kandidat atau perolehan suara untuk periode <?php echo $periode; ?>. Silakan tambahkan kandidat terlebih dahulu di menu Manajemen Kandidat.
        </div>
    <?php } ?>
</div>

<?php if ($jumlah_kandidat > 0) { ?>
<!-- Sertakan Script Chart.js dan Plugin DataLabels -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    var ctx = document.getElementById('canvas').getContext('2d');
    
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            // Label ditarik dinamis dari database PHP
            labels: [<?php echo $labels_js; ?>], 
            datasets: [{
                label: 'Jumlah Suara',
                data: [<?php echo $data_js; ?>], // Nilai suara ditarik dinamis dari database PHP
                backgroundColor: [
                    'rgba(250, 204, 21, 0.85)', 
                    'rgba(244, 63, 94, 0.85)',  
                    'rgba(56, 189, 248, 0.85)',
                    'rgba(34, 197, 94, 0.85)',
                    'rgba(168, 85, 247, 0.85)'
                ],
                borderColor: [
                    'rgba(250, 204, 21, 1)',
                    'rgba(244, 63, 94, 1)',
                    'rgba(56, 189, 248, 1)',
                    'rgba(34, 197, 94, 1)',
                    'rgba(168, 85, 247, 1)'
                ],
                borderWidth: 1
            }]
        },
        plugins: [ChartDataLabels],
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    color: '#333333',
                    font: {
                        weight: 'bold',
                        size: 14
                    },
                    formatter: function(value) {
                        return value;
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    document.getElementById('save-img').addEventListener('click', function () {
        var imageURL = myChart.toBase64Image();
        var link = document.createElement('a');
        link.href = imageURL;
        link.download = 'perolehan-suara-osis.png';
        link.click();
    });
});
</script>
<?php } ?>  