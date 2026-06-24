<?php
// config.php
// Konfigurasi Database Sistem Pakar Parkinson Teorema Bayes

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "db_parkinson_bayes";

// Membuat koneksi ke database MySQL
$conn = mysqli_connect($db_host, $db_user, $db_pass);

if (!$conn) {
    die("Koneksi ke MySQL Server gagal: " . mysqli_connect_error());
}

// Cek apakah database db_parkinson_bayes sudah ada, jika belum buat database
$db_check = mysqli_query($conn, "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db_name'");
if (mysqli_num_rows($db_check) == 0) {
    mysqli_query($conn, "CREATE DATABASE $db_name");
}

// Hubungkan ke database
mysqli_select_db($conn, $db_name);

// Cek apakah tabel penyakit sudah ada, jika belum import db_parkinson_bayes.sql
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'penyakit'");
if (mysqli_num_rows($table_check) == 0) {
    $sql_file = __DIR__ . '/db_parkinson_bayes.sql';
    if (file_exists($sql_file)) {
        $sql_content = file_get_contents($sql_file);
        
        // Bersihkan SQL dari komentar multi-line
        $sql_content = preg_replace('!/\*.*?\*/!s', '', $sql_content);
        $sql_content = preg_replace('/\n{2,}/', "\n", $sql_content);
        
        // Pecah per query
        $queries = explode(";\n", $sql_content);
        if (count($queries) <= 1) {
            $queries = explode(";", $sql_content);
        }
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                mysqli_query($conn, $query);
            }
        }
    }
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Auto-seeding untuk Gejala dan Rule Bayes jika kosong
$gejala_check = mysqli_query($conn, "SELECT COUNT(*) as total FROM gejala");
$gejala_count = mysqli_fetch_assoc($gejala_check)['total'];

if ($gejala_count == 0) {
    // Daftar 46 gejala lengkap Parkinson
    $gejala_list = [
        ['G01', 'Tremor saat istirahat (Resting Tremor)', 0.85],
        ['G02', 'Kekakuan otot pada tangan atau kaki (Rigidity)', 0.75],
        ['G03', 'Gerakan tubuh lambat (Bradykinesia)', 0.90],
        ['G04', 'Gangguan keseimbangan postural', 0.65],
        ['G05', 'Kehilangan keseimbangan', 0.20],
        ['G06', 'Kesulitan memulai gerakan (Freezing of gait)', 0.60],
        ['G07', 'Sentakan otot involunter tiba-tiba (Myoclonus)', 0.80],
        ['G08', 'Gerakan memutar tidak terkendali (Chorea)', 0.75],
        ['G09', 'Kedutan halus pada kelopak mata (Myokymia)', 0.70],
        ['G10', 'Kedutan otot pada satu sisi wajah (Hemifacial spasm)', 0.80],
        ['G11', 'Kontraksi otot berulang yang menyebabkan postur abnormal (Dystonia)', 0.85],
        ['G12', 'Jalan sempoyongan dan tidak stabil (Ataxia)', 0.80],
        ['G13', 'Kurang bergairah', 0.40],
        ['G14', 'Tremor saat melakukan gerakan terarah (Action Tremor)', 0.70],
        ['G15', 'Suara melemah dan monoton (Hypophonia)', 0.50],
        ['G16', 'Kesulitan menelan makanan atau cairan (Dysphagia)', 0.55],
        ['G17', 'Ekspresi wajah datar seperti topeng (Masked facies)', 0.50],
        ['G18', 'Tulisan tangan mengecil (Micrographia)', 0.45],
        ['G19', 'Kedipan mata berkurang frekuensinya', 0.40],
        ['G20', 'Air liur menetes berlebihan (Sialorrhea)', 0.50],
        ['G21', 'Rasa lelah yang berlebihan (Fatigue)', 0.45],
        ['G22', 'Gangguan tidur REM (sering mengigau/bergerak aktif saat tidur)', 0.60],
        ['G23', 'Kehilangan indera penciuman (Anosmia)', 0.40],
        ['G24', 'Tekanan darah turun drastis saat berdiri (Orthostatic hypotension)', 0.50],
        ['G25', 'Gangguan kecemasan atau depresi', 0.45],
        ['G26', 'Kesulitan konsentrasi atau berpikir lambat', 0.50],
        ['G27', 'Kurang dapat koordinasi', 0.30],
        ['G28', 'Sembelit kronis (Constipation)', 0.45],
        ['G29', 'Keringat berlebih (Hyperhidrosis)', 0.40],
        ['G30', 'Kulit wajah berminyak (Seborrheic dermatitis)', 0.35],
        ['G31', 'Sering buang air kecil di malam hari (Nocturia)', 0.45],
        ['G32', 'Pandangan kabur atau kesulitan mengarahkan gerakan mata', 0.50],
        ['G33', 'Menggigil pada tangan saat memegang benda kecil', 0.55],
        ['G34', 'Otot kaku di daerah leher', 0.60],
        ['G35', 'Kesulitan membalikkan badan di tempat tidur', 0.55],
        ['G36', 'Kelambatan dalam merespon ucapan orang lain', 0.40],
        ['G37', 'Rasa tidak stabil saat berputar arah ketika berjalan', 0.60],
        ['G38', 'Ketegangan otot pada pergelangan kaki', 0.55],
        ['G39', 'Kram otot yang menyakitkan terutama di malam hari', 0.50],
        ['G40', 'Tremor postural (gemetar saat lengan diluruskan ke depan)', 0.65],
        ['G41', 'Perubahan pola berjalan menjadi lebih cepat dan condong ke depan (Festinating gait)', 0.60],
        ['G42', 'Penurunan volume suara saat berbicara panjang', 0.45],
        ['G43', 'Rasa tidak nyaman atau gelisah pada kaki saat beristirahat (Restless legs)', 0.50],
        ['G44', 'Gangguan dalam mengenali arah ruang atau disorientasi ringan', 0.40],
        ['G45', 'Kesulitan mengancingkan baju atau menalikan sepatu', 0.55],
        ['G46', 'Penurunan berat badan tanpa penyebab yang jelas', 0.35]
    ];

    foreach ($gejala_list as $gejala) {
        $kode = $gejala[0];
        $nama = mysqli_real_escape_string($conn, $gejala[1]);
        $nilai = $gejala[2];
        mysqli_query($conn, "INSERT INTO gejala (kode_gejala, nama_gejala, nilai_gejala) VALUES ('$kode', '$nama', '$nilai')");
    }
}

// Auto-seeding untuk Rule Bayes jika kosong
$rule_check = mysqli_query($conn, "SELECT COUNT(*) as total FROM rule_bayes");
$rule_count = mysqli_fetch_assoc($rule_check)['total'];

if ($rule_count == 0) {
    // Ambil penyakit
    $penyakit_query = mysqli_query($conn, "SELECT id_penyakit, kode_penyakit FROM penyakit");
    $penyakit_ids = [];
    while ($row = mysqli_fetch_assoc($penyakit_query)) {
        $penyakit_ids[$row['kode_penyakit']] = $row['id_penyakit'];
    }

    // Ambil gejala
    $gejala_query = mysqli_query($conn, "SELECT id_gejala, kode_gejala FROM gejala");
    $gejala_ids = [];
    while ($row = mysqli_fetch_assoc($gejala_query)) {
        $gejala_ids[$row['kode_gejala']] = $row['id_gejala'];
    }

    // Pemetaan Aturan (Penyakit -> Gejala -> Bobot Conditional P(E|H))
    // Sesuai validasi pengguna:
    // G01-G02 -> Mioklonus (P01)
    // G03-G17 -> Chorea (P02)
    // G18-G21 -> Miokimia (P03)
    // G22-G26 -> Distonia (P04)
    // G27-G35 -> Ataksia (P05)
    // G36-G38 -> Kejang Hemifasial (P06)
    // G39-G46 -> Tremor (P07)
    $rules = [];
    
    // Helper function to generate rules
    $add_rules = function(&$rules, $penyakit, $start_g, $end_g) {
        for ($i = $start_g; $i <= $end_g; $i++) {
            $kode = 'G' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $rules[] = [$penyakit, $kode, 0.8]; // Default bobot 0.8
        }
    };
    
    $add_rules($rules, 'P01', 1, 2);
    $add_rules($rules, 'P02', 3, 17);
    $add_rules($rules, 'P03', 18, 21);
    $add_rules($rules, 'P04', 22, 26);
    $add_rules($rules, 'P05', 27, 35);
    $add_rules($rules, 'P06', 36, 38);
    $add_rules($rules, 'P07', 39, 46);

    foreach ($rules as $rule) {
        $p_kode = $rule[0];
        $g_kode = $rule[1];
        $bobot = $rule[2];

        if (isset($penyakit_ids[$p_kode]) && isset($gejala_ids[$g_kode])) {
            $p_id = $penyakit_ids[$p_kode];
            $g_id = $gejala_ids[$g_kode];
            mysqli_query($conn, "INSERT INTO rule_bayes (id_penyakit, id_gejala, bobot) VALUES ($p_id, $g_id, $bobot)");
        }
    }
}
?>
