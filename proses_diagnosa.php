<?php
// proses_diagnosa.php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: diagnosa.php");
    exit;
}

// 1. Ambil data pasien dari form
$nama_pasien = mysqli_real_escape_string($conn, $_POST['nama_pasien']);
$umur = intval($_POST['umur']);
$jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
$alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
$selected_gejala = isset($_POST['gejala']) ? $_POST['gejala'] : []; // Array of id_gejala
$keyakinan_data = isset($_POST['keyakinan']) ? $_POST['keyakinan'] : []; // Array id_gejala => nilai

// Jika tidak ada gejala yang dipilih, arahkan kembali ke form dengan peringatan
if (empty($selected_gejala)) {
    echo "<script>alert('Silakan pilih minimal satu gejala!'); window.location.href='diagnosa.php';</script>";
    exit;
}

// Pastikan kolom gejala_terpilih ada di tabel diagnosa
$column_check = mysqli_query($conn, "SHOW COLUMNS FROM diagnosa LIKE 'gejala_terpilih'");
if (mysqli_num_rows($column_check) == 0) {
    mysqli_query($conn, "ALTER TABLE diagnosa ADD COLUMN gejala_terpilih TEXT NULL");
}

// Pastikan tabel diagnosa_detail ada
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS diagnosa_detail (
    id_detail INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_diagnosa INT(11) NOT NULL,
    id_gejala INT(11) NOT NULL,
    kode_gejala VARCHAR(10),
    nama_gejala TEXT,
    nilai_gejala DECIMAL(5,2),
    tingkat_keyakinan DECIMAL(3,2),
    label_keyakinan VARCHAR(30),
    nilai_evidence DECIMAL(10,4),
    FOREIGN KEY (id_diagnosa) REFERENCES diagnosa(id_diagnosa) ON DELETE CASCADE,
    FOREIGN KEY (id_gejala) REFERENCES gejala(id_gejala) ON DELETE CASCADE
)");

// 2. Simpan data pasien ke tabel pasien
$insert_pasien = mysqli_query($conn, "INSERT INTO pasien (nama_pasien, jenis_kelamin, umur, alamat) VALUES ('$nama_pasien', '$jenis_kelamin', $umur, '$alamat')");
if (!$insert_pasien) {
    die("Gagal menyimpan data pasien: " . mysqli_error($conn));
}
$id_pasien = mysqli_insert_id($conn);

// 3. Ambil data gejala terpilih beserta nilai dan hitung evidence
$gejala_ids_str = implode(',', array_map('intval', $selected_gejala));
$gejala_query = mysqli_query($conn, "SELECT * FROM gejala WHERE id_gejala IN ($gejala_ids_str)");
$gejala_list = [];

// Fungsi label keyakinan berdasarkan rentang (sesuai jurnal)
function getKeyakinanLabel($val) {
    if ($val <= 0.2) return 'Tidak Pasti';
    if ($val <= 0.4) return 'Kurang Pasti';
    if ($val <= 0.6) return 'Mungkin';
    if ($val <= 0.8) return 'Pasti';
    return 'Sangat Pasti';
}

while ($row = mysqli_fetch_assoc($gejala_query)) {
    $id_g = $row['id_gejala'];
    
    // Ambil tingkat keyakinan dari input user (default 0.8 jika tidak ada)
    $tk = isset($keyakinan_data[$id_g]) ? floatval($keyakinan_data[$id_g]) : 0.8;
    $row['tingkat_keyakinan'] = $tk;
    
    // Label keyakinan
    $row['label_keyakinan'] = getKeyakinanLabel($tk);
    
    // Simpan tingkat keyakinan user sebagai evidence per gejala
    $row['nilai_evidence'] = $tk;
    
    $gejala_list[$id_g] = $row;
}

// 4. Hitung Total Evidence = Jumlah seluruh tingkat keyakinan user
$total_evidence = 0;
foreach ($gejala_list as $g) {
    $total_evidence += floatval($g['nilai_evidence']);
}

// 5. Ambil daftar penyakit dari database
$penyakit_query = mysqli_query($conn, "SELECT * FROM penyakit");
$penyakit_list = [];
while ($row = mysqli_fetch_assoc($penyakit_query)) {
    $penyakit_list[] = $row;
}

// 6. Hitung Teorema Bayes (Logika Baru)
// Untuk setiap penyakit:
//   skor = nilai_bayes_penyakit * total_bobot_gejala_cocok
$bayes_calc = [];

foreach ($penyakit_list as $penyakit) {
    $id_penyakit = $penyakit['id_penyakit'];
    $nilai_bayes = floatval($penyakit['nilai_bayes']);
    
    // Ambil rule_bayes untuk penyakit ini
    $rule_query = mysqli_query($conn, "SELECT id_gejala, bobot FROM rule_bayes WHERE id_penyakit = $id_penyakit");
    $gejala_cocok = 0;
    $total_bobot_cocok = 0;
    
    while ($rule = mysqli_fetch_assoc($rule_query)) {
        if (in_array($rule['id_gejala'], $selected_gejala)) {
            $gejala_cocok++;
            $total_bobot_cocok += floatval($rule['bobot']);
        }
    }
    
    // Hitung skor sesuai aturan baru
    if ($gejala_cocok > 0) {
        $hasil_bayes = $nilai_bayes * $total_bobot_cocok;
    } else {
        $hasil_bayes = 0;
    }
    
    $bayes_calc[$id_penyakit] = [
        'penyakit' => $penyakit,
        'nilai_bayes' => $nilai_bayes,
        'hasil_bayes' => $hasil_bayes,
        'jumlah_cocok' => $gejala_cocok,
        'persentase' => min(100, round($hasil_bayes * 100, 2))
    ];
}

// 7. Tentukan penyakit dengan hasil_bayes tertinggi
$highest_bayes = -1.0;
$highest_cocok = -1;
$detected_penyakit_id = null;
$detected_penyakit_name = "Tidak Terdeteksi";

foreach ($bayes_calc as $id_penyakit => $calc) {
    if ($calc['hasil_bayes'] > $highest_bayes) {
        $highest_bayes = $calc['hasil_bayes'];
        $highest_cocok = $calc['jumlah_cocok'];
        $detected_penyakit_id = $id_penyakit;
    } elseif ($calc['hasil_bayes'] == $highest_bayes && $calc['hasil_bayes'] > 0) {
        // Jika skor sama, pilih yang jumlah gejala cocoknya lebih banyak
        if ($calc['jumlah_cocok'] > $highest_cocok) {
            $highest_bayes = $calc['hasil_bayes'];
            $highest_cocok = $calc['jumlah_cocok'];
            $detected_penyakit_id = $id_penyakit;
        }
    }
}

// Cari nama penyakit terdeteksi
if ($detected_penyakit_id !== null && $highest_bayes > 0) {
    $detected_penyakit_name = $bayes_calc[$detected_penyakit_id]['penyakit']['nama_penyakit'];
} else {
    $detected_penyakit_name = "Gejala tidak spesifik (Tidak terdeteksi penyakit Parkinson)";
    $highest_bayes = 0.0;
}

// 8. Konversi ke persentase & tingkat kepastian
$persentase = min(100, round($highest_bayes * 100, 2));

// Kategori Kepastian
if ($highest_bayes <= 0.20) {
    $tingkat_kepastian = "Tidak Pasti";
} elseif ($highest_bayes <= 0.40) {
    $tingkat_kepastian = "Kurang Pasti";
} elseif ($highest_bayes <= 0.60) {
    $tingkat_kepastian = "Mungkin";
} elseif ($highest_bayes <= 0.80) {
    $tingkat_kepastian = "Pasti";
} else {
    $tingkat_kepastian = "Sangat Pasti";
}

// 9. Simpan hasil ke tabel diagnosa
$gejala_terpilih_str = implode(',', $selected_gejala);
$insert_diagnosa = mysqli_query($conn, "INSERT INTO diagnosa (id_pasien, hasil_penyakit, persentase, tingkat_kepastian, gejala_terpilih) VALUES ($id_pasien, '$detected_penyakit_name', $persentase, '$tingkat_kepastian', '$gejala_terpilih_str')");

if (!$insert_diagnosa) {
    die("Gagal menyimpan rekam medis: " . mysqli_error($conn));
}
$id_diagnosa = mysqli_insert_id($conn);

// 10. Simpan detail gejala ke tabel diagnosa_detail
foreach ($gejala_list as $g) {
    $id_gejala = intval($g['id_gejala']);
    $kode = mysqli_real_escape_string($conn, $g['kode_gejala']);
    $nama = mysqli_real_escape_string($conn, $g['nama_gejala']);
    $nilai = floatval($g['nilai_gejala']);
    $tk = floatval($g['tingkat_keyakinan']);
    $label = mysqli_real_escape_string($conn, $g['label_keyakinan']);
    $evidence = floatval($g['nilai_evidence']);
    
    mysqli_query($conn, "INSERT INTO diagnosa_detail (id_diagnosa, id_gejala, kode_gejala, nama_gejala, nilai_gejala, tingkat_keyakinan, label_keyakinan, nilai_evidence) VALUES ($id_diagnosa, $id_gejala, '$kode', '$nama', $nilai, $tk, '$label', $evidence)");
}

// 11. Simpan data perhitungan ke session untuk visualisasi di halaman hasil
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['last_diagnosa_chart'] = [];
$_SESSION['last_diagnosa_bayes'] = [];
$_SESSION['last_total_evidence'] = $total_evidence;

foreach ($penyakit_list as $penyakit) {
    $p_id = $penyakit['id_penyakit'];
    
    $_SESSION['last_diagnosa_chart'][] = [
        'name' => $penyakit['nama_penyakit'],
        'percentage' => $bayes_calc[$p_id]['persentase']
    ];
    
    $_SESSION['last_diagnosa_bayes'][] = [
        'nama_penyakit' => $penyakit['nama_penyakit'],
        'kode_penyakit' => $penyakit['kode_penyakit'],
        'nilai_bayes' => $bayes_calc[$p_id]['nilai_bayes'],
        'hasil_bayes' => round($bayes_calc[$p_id]['hasil_bayes'], 4),
        'persentase' => $bayes_calc[$p_id]['persentase']
    ];
}

// Redirect ke halaman hasil
header("Location: hasil.php?id=$id_diagnosa");
exit;
?>
