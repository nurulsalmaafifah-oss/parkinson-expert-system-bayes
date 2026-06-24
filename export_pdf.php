<?php
// export_pdf.php
require_once 'config.php';
require_once 'auth.php';

// Proteksi halaman admin
check_login();

// Filter & Pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$tgl_mulai = isset($_GET['tgl_mulai']) ? mysqli_real_escape_string($conn, $_GET['tgl_mulai']) : '';
$tgl_selesai = isset($_GET['tgl_selesai']) ? mysqli_real_escape_string($conn, $_GET['tgl_selesai']) : '';

$where_clauses = [];
if (!empty($search)) {
    $where_clauses[] = "(p.nama_pasien LIKE '%$search%' OR d.id_diagnosa LIKE '%$search%')";
}
if (!empty($tgl_mulai)) {
    $where_clauses[] = "d.tanggal_diagnosa >= '$tgl_mulai 00:00:00'";
}
if (!empty($tgl_selesai)) {
    $where_clauses[] = "d.tanggal_diagnosa <= '$tgl_selesai 23:59:59'";
}

$where_clause = "";
if (count($where_clauses) > 0) {
    $where_clause = "WHERE " . implode(" AND ", $where_clauses);
}

// Query data diagnosa keseluruhan tanpa limit pagination untuk cetak utuh
$laporan_query = mysqli_query($conn, "
    SELECT d.*, p.nama_pasien, p.jenis_kelamin, p.umur 
    FROM diagnosa d
    JOIN pasien p ON d.id_pasien = p.id_pasien
    $where_clause
    ORDER BY d.id_diagnosa DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Diagnosa Parkinson</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            background-color: #fff;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 14px;
            margin: 0 0 5px 0;
            font-weight: normal;
        }
        .header p {
            font-size: 11px;
            margin: 0;
            color: #666;
        }
        .meta-info {
            margin-bottom: 15px;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .signature-block {
            float: right;
            width: 200px;
            text-align: center;
            margin-top: 20px;
        }
        .signature-space {
            height: 70px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    
    <div class="no-print" style="background: #f1f1f1; padding: 10px; text-align: right; margin-bottom: 20px; border-radius: 4px;">
        <button onclick="window.print();" style="padding: 6px 12px; background: #FF6B1A; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Cetak Sekarang</button>
        <button onclick="window.close();" style="padding: 6px 12px; background: #ddd; color: #333; border: none; border-radius: 4px; cursor: pointer; margin-left: 5px;">Tutup Halaman</button>
    </div>

    <!-- Header Kop Laporan -->
    <div class="header">
        <h1>Laporan Hasil Diagnosa</h1>
        <h2>Sistem Pakar Deteksi Dini Penyakit Parkinson</h2>
        <p>Neurological Research Institute • Dihasilkan secara otomatis oleh sistem</p>
    </div>

    <!-- Metadata Laporan -->
    <div class="meta-info">
        <strong>Tanggal Cetak:</strong> <?php echo date('d F Y H:i'); ?><br>
        <strong>Periode Laporan:</strong> <?php 
            if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
                echo date('d M Y', strtotime($tgl_mulai)) . " s.d. " . date('d M Y', strtotime($tgl_selesai));
            } elseif (!empty($tgl_mulai)) {
                echo "Mulai dari " . date('d M Y', strtotime($tgl_mulai));
            } elseif (!empty($tgl_selesai)) {
                echo "Sampai dengan " . date('d M Y', strtotime($tgl_selesai));
            } else {
                echo "Semua Periode";
            }
        ?><br>
        <?php if (!empty($search)): ?>
            <strong>Pencarian:</strong> "<?php echo htmlspecialchars($search); ?>"<br>
        <?php endif; ?>
        <strong>Total Data:</strong> <?php echo mysqli_num_rows($laporan_query); ?> Diagnosa
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 12%;">ID Diagnosa</th>
                <th style="width: 15%;">Tanggal</th>
                <th>Nama Pasien</th>
                <th style="width: 12%;">Gender</th>
                <th style="width: 8%;" class="text-right">Usia</th>
                <th>Hasil Diagnosa</th>
                <th style="width: 12%;" class="text-right">Persentase</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (mysqli_num_rows($laporan_query) > 0):
                $no = 1;
                while ($row = mysqli_fetch_assoc($laporan_query)):
            ?>
                <tr>
                    <td class="text-center"><?php echo $no++; ?></td>
                    <td style="font-family: monospace;">#DG-<?php echo str_pad($row['id_diagnosa'], 4, "0", STR_PAD_LEFT); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal_diagnosa'])); ?></td>
                    <td><strong><?php echo htmlspecialchars($row['nama_pasien']); ?></strong></td>
                    <td><?php echo $row['jenis_kelamin']; ?></td>
                    <td class="text-right"><?php echo $row['umur']; ?> Tahun</td>
                    <td><?php echo htmlspecialchars($row['hasil_penyakit']); ?> (<?php echo $row['tingkat_kepastian']; ?>)</td>
                    <td class="text-right"><strong><?php echo number_format($row['persentase'], 1); ?>%</strong></td>
                </tr>
            <?php 
                endwhile;
            else:
            ?>
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data diagnosa untuk periode ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Signature Block -->
    <div class="signature-block">
        <p>Yogyakarta, <?php echo date('d F Y'); ?></p>
        <p><strong>Administrator</strong></p>
        <div class="signature-space"></div>
        <hr style="border: 0; border-top: 1px solid #333; margin: 0 10px;">
        <p style="font-size: 11px; margin-top: 5px;">Sistem Pakar Parkinson</p>
    </div>

    <!-- Auto Print Script -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
