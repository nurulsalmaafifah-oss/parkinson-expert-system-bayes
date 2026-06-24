<?php
// export_excel.php
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

// Query data diagnosa keseluruhan
$laporan_query = mysqli_query($conn, "
    SELECT d.*, p.nama_pasien, p.jenis_kelamin, p.umur, p.alamat 
    FROM diagnosa d
    JOIN pasien p ON d.id_pasien = p.id_pasien
    $where_clause
    ORDER BY d.id_diagnosa DESC
");

// Set Headers untuk Eksport ke Excel (.xls)
header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=laporan_diagnosa_parkinson_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!-- Tabel HTML yang akan dibaca sebagai Excel -->
<table border="1">
    <thead>
        <tr style="background-color: #f5f5f5; font-weight: bold;">
            <th colspan="10" style="text-align: center; font-size: 16px; height: 40px;">
                LAPORAN REKAPITULASI DIAGNOSA PENYAKIT PARKINSON
            </th>
        </tr>
        <tr style="background-color: #f5f5f5; font-weight: bold;">
            <th colspan="10" style="text-align: center; font-size: 12px; height: 25px;">
                Periode Laporan: <?php 
                    if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
                        echo date('d M Y', strtotime($tgl_mulai)) . " s.d. " . date('d M Y', strtotime($tgl_selesai));
                    } elseif (!empty($tgl_mulai)) {
                        echo "Mulai " . date('d M Y', strtotime($tgl_mulai));
                    } elseif (!empty($tgl_selesai)) {
                        echo "Sampai " . date('d M Y', strtotime($tgl_selesai));
                    } else {
                        echo "Semua Periode";
                    }
                ?> | Diekspor pada: <?php echo date('d-m-Y H:i'); ?>
            </th>
        </tr>
        <tr style="background-color: #FF6B1A; color: white; font-weight: bold; height: 30px;">
            <th>No</th>
            <th>ID Diagnosa</th>
            <th>Tanggal Diagnosa</th>
            <th>Nama Pasien</th>
            <th>Jenis Kelamin</th>
            <th>Usia (Tahun)</th>
            <th>Alamat</th>
            <th>Hasil Diagnosa</th>
            <th>Persentase Probabilitas</th>
            <th>Tingkat Kepastian</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if (mysqli_num_rows($laporan_query) > 0):
            $no = 1;
            while ($row = mysqli_fetch_assoc($laporan_query)):
        ?>
            <tr style="height: 25px;">
                <td style="text-align: center;"><?php echo $no++; ?></td>
                <td style="font-family: monospace;">#DG-<?php echo str_pad($row['id_diagnosa'], 4, "0", STR_PAD_LEFT); ?></td>
                <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal_diagnosa'])); ?></td>
                <td><?php echo htmlspecialchars($row['nama_pasien']); ?></td>
                <td><?php echo htmlspecialchars($row['jenis_kelamin']); ?></td>
                <td style="text-align: right;"><?php echo $row['umur']; ?></td>
                <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                <td><?php echo htmlspecialchars($row['hasil_penyakit']); ?></td>
                <td style="text-align: right; font-weight: bold;"><?php echo number_format($row['persentase'], 2); ?>%</td>
                <td><?php echo htmlspecialchars($row['tingkat_kepastian']); ?></td>
            </tr>
        <?php 
            endwhile;
        else:
        ?>
            <tr>
                <td colspan="10" style="text-align: center; height: 30px;">Tidak ada data diagnosa ditemukan.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
