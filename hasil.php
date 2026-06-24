<?php
// hasil.php
require_once 'config.php';

$id_diagnosa = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_diagnosa <= 0) {
    header("Location: index.php");
    exit;
}

// Query data diagnosa dan pasien
$query = mysqli_query($conn, "
    SELECT d.*, p.nama_pasien, p.jenis_kelamin, p.umur, p.alamat, p.created_at as tgl_daftar
    FROM diagnosa d
    JOIN pasien p ON d.id_pasien = p.id_pasien
    WHERE d.id_diagnosa = $id_diagnosa
");

if (mysqli_num_rows($query) == 0) {
    echo "<script>alert('Data diagnosa tidak ditemukan!'); window.location.href='index.php';</script>";
    exit;
}

$diagnosa = mysqli_fetch_assoc($query);
$gejala_terpilih = $diagnosa['gejala_terpilih']; // String separated by comma e.g. "1,2,5"

// Ambil data gejala terpilih (fallback dari tabel gejala)
$gejala_list = [];
if (!empty($gejala_terpilih)) {
    $gejala_query = mysqli_query($conn, "SELECT * FROM gejala WHERE id_gejala IN ($gejala_terpilih) ORDER BY kode_gejala ASC");
    while ($row = mysqli_fetch_assoc($gejala_query)) {
        $gejala_list[] = $row;
    }
}

// Ambil data detail gejala dari diagnosa_detail (termasuk tingkat keyakinan & evidence)
$detail_list = [];
$has_detail = false;
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'diagnosa_detail'");
if (mysqli_num_rows($check_table) > 0) {
    $detail_query = mysqli_query($conn, "SELECT * FROM diagnosa_detail WHERE id_diagnosa = $id_diagnosa ORDER BY kode_gejala ASC");
    if ($detail_query && mysqli_num_rows($detail_query) > 0) {
        $has_detail = true;
        while ($row = mysqli_fetch_assoc($detail_query)) {
            $detail_list[] = $row;
        }
    }
}

// Ambil deskripsi dan solusi penyakit terdeteksi
$nama_penyakit = $diagnosa['hasil_penyakit'];
$penyakit_query = mysqli_query($conn, "SELECT * FROM penyakit WHERE nama_penyakit = '" . mysqli_real_escape_string($conn, $nama_penyakit) . "'");
$penyakit_data = mysqli_fetch_assoc($penyakit_query);

$deskripsi = $penyakit_data ? $penyakit_data['deskripsi'] : "Gejala yang dipilih tidak mengarah secara spesifik pada salah satu jenis penyakit Parkinson yang terdaftar di sistem kami.";
$solusi = $penyakit_data ? $penyakit_data['solusi'] : "Silakan berkonsultasi secara langsung dengan Dokter Spesialis Saraf (Neurolog) untuk mendapatkan pemeriksaan klinis lebih lanjut.";

// Rekonstruksi perhitungan Bayes untuk diagram probabilitas
$penyakit_all_query = mysqli_query($conn, "SELECT * FROM penyakit");
$penyakit_all = [];
while ($row = mysqli_fetch_assoc($penyakit_all_query)) {
    $penyakit_all[] = $row;
}

$chart_data = [];
$bayes_detail = [];

if (!empty($gejala_terpilih)) {
    // Rekonstruksi perhitungan Bayes sesuai aturan baru:
    // skor = nilai_bayes_penyakit * total_bobot_gejala_cocok
    $selected_gejala_array = explode(',', $gejala_terpilih);
    
    foreach ($penyakit_all as $p) {
        $id_penyakit = $p['id_penyakit'];
        $nilai_bayes = floatval($p['nilai_bayes']);
        
        // Ambil rule_bayes untuk penyakit ini
        $rule_query = mysqli_query($conn, "SELECT id_gejala, bobot FROM rule_bayes WHERE id_penyakit = $id_penyakit");
        $gejala_cocok = 0;
        $total_bobot_cocok = 0;
        
        while ($rule = mysqli_fetch_assoc($rule_query)) {
            if (in_array($rule['id_gejala'], $selected_gejala_array)) {
                $gejala_cocok++;
                $total_bobot_cocok += floatval($rule['bobot']);
            }
        }
        
        if ($gejala_cocok > 0) {
            $hasil_bayes = $nilai_bayes * $total_bobot_cocok;
        } else {
            $hasil_bayes = 0;
        }
        
        $percentage = min(100, round($hasil_bayes * 100, 2));
        
        $chart_data[] = [
            'name' => $p['nama_penyakit'],
            'percentage' => $percentage
        ];
        $bayes_detail[] = [
            'nama' => $p['nama_penyakit'],
            'kode' => $p['kode_penyakit'],
            'nilai_bayes' => $nilai_bayes,
            'total_evidence' => $total_bobot_cocok, // Ditampilkan sebagai Total Evidence di UI untuk menjaga struktur UI
            'hasil_bayes' => $hasil_bayes,
            'persentase' => $percentage
        ];
    }
} else {
    foreach ($penyakit_all as $p) {
        $chart_data[] = [
            'name' => $p['nama_penyakit'],
            'percentage' => 0.0
        ];
    }
}

// Cek darimana asal kunjungan halaman
$source = isset($_GET['src']) ? $_GET['src'] : 'public';
$back_url = "index.php";
if ($source == 'admin') {
    $back_url = "admin_laporan.php";
}
?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Hasil Diagnosis - ParkinsonExpert</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
                          "colors": {
                    "primary": "#FF6B1A",
                    "primary-fixed": "#FFE7D6",
                    "on-primary-fixed-variant": "#FF8A3D",
                    "primary-container": "#FFE7D6",
                    "on-primary-container": "#FF6B1A",
                    "on-primary": "#ffffff",
                    "surface": "#ffffff",
                    "surface-bright": "#2D2D2D",
                    "surface-container-lowest": "#ffffff",
                    "surface-container-low": "#F8F3ED",
                    "surface-container": "#F2EAE2",
                    "surface-container-high": "#E8DDD3",
                    "surface-variant": "#6B7280",
                    "on-surface-variant": "#6B7280",
                    "background": "#F8F3ED",
                    "inverse-surface": "#ffffff",
                    "outline": "#E8DDD3",
                    "outline-variant": "#E8DDD3",
                    "on-surface": "#2D2D2D",
                    "on-background": "#2D2D2D",
                    "secondary": "#6B7280",
                    "secondary-fixed": "#F2EAE2",
                    "on-secondary-fixed-variant": "#FF8A3D",
                    "on-secondary-container": "#6B7280",
                    "tertiary-container": "#22C55E",
                    "on-tertiary-container": "#ffffff",
                    "error-container": "#EF4444",
                    "on-error-container": "#ffffff",
                    "error": "#EF4444",
                    "surface-tint": "#FF8A3D"
            },
                          "borderRadius": {
                    "DEFAULT": "12px",
                    "lg": "16px",
                    "xl": "20px",
                    "full": "9999px"
            },
              "spacing": {
                      "sm": "12px",
                      "xs": "4px",
                      "lg": "32px",
                      "margin-mobile": "16px",
                      "gutter": "24px",
                      "xl": "48px",
                      "margin-desktop": "40px",
                      "md": "24px",
                      "base": "8px"
              },
              "fontFamily": {
                      "label-md": ["Inter"],
                      "body-sm": ["Inter"],
                      "headline-md": ["Inter"],
                      "body-lg": ["Inter"],
                      "body-md": ["Inter"],
                      "headline-sm": ["Inter"],
                      "headline-lg": ["Inter"],
                      "headline-lg-mobile": ["Inter"],
                      "label-sm": ["Inter"]
              },
              "fontSize": {
                      "label-md": ["14px", { "lineHeight": "20px", "letterSpacing": "0.05em", "fontWeight": "600" }],
                      "body-sm": ["14px", { "lineHeight": "20px", "fontWeight": "400" }],
                      "headline-md": ["24px", { "lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                      "body-lg": ["18px", { "lineHeight": "28px", "fontWeight": "400" }],
                      "body-md": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                      "headline-sm": ["20px", { "lineHeight": "28px", "fontWeight": "600" }],
                      "headline-lg": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                      "headline-lg-mobile": ["24px", { "lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "700" }],
                      "label-sm": ["12px", { "lineHeight": "16px", "fontWeight": "500" }]
              }
            }
          }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bento-card {
            background-color: #ffffff;
            border-radius: 1rem;
            border: 1px solid #c3c6d7;
            padding: 24px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        @media print {
            body { background: white; color: black; }
            nav, .no-print { display: none !important; }
            main { margin-left: 0 !important; width: 100% !important; }
            .bento-card { border: 1px solid #ddd; box-shadow: none; page-break-inside: avoid; }
        }
    </style>
</head>
<body class="antialiased min-h-screen bg-background flex flex-col">
    <!-- TopNavBar (for Public) -->
    <?php if ($source !== 'admin'): ?>
    <nav class="sticky top-0 z-50 flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop bg-surface dark:bg-inverse-surface h-16 border-b border-outline-variant dark:border-outline no-print">
        <div class="flex items-center gap-md">
            <a href="index.php" class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed">ParkinsonExpert</a>
        </div>
        <div class="flex items-center gap-lg">
            <a class="text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors font-label-md text-label-md" href="index.php">Home</a>
            <a class="text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors font-label-md text-label-md" href="login.php">Login Admin</a>
        </div>
    </nav>
    <?php endif; ?>

    <!-- Main Content Area -->
    <main class="flex-grow p-margin-mobile md:p-margin-desktop bg-background min-h-screen <?php echo $source == 'admin' ? 'md:ml-[280px]' : ''; ?>">
        
        <!-- Top Actions -->
        <div class="flex justify-between items-center mb-lg no-print">
            <a href="<?php echo $back_url; ?>" class="flex items-center gap-xs text-secondary hover:text-primary transition-colors font-label-md text-label-md">
                <span class="material-symbols-outlined" data-icon="arrow_back">arrow_back</span>
                Kembali
            </a>
            <div class="flex gap-sm">
                <button onclick="window.print()" class="flex items-center gap-xs h-10 px-4 rounded border border-outline-variant text-primary font-label-md text-label-md hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined" data-icon="picture_as_pdf">picture_as_pdf</span>
                    Print PDF / Cetak
                </button>
            </div>
        </div>
        
        <div class="mb-lg">
            <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface mb-xs">Hasil Diagnosis</h2>
            <p class="font-body-md text-body-md text-secondary">Analisis komprehensif berdasarkan input gejala klinis pasien.</p>
        </div>
        
        <!-- Bento Grid Layout -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-gutter">
            
            <!-- Patient Info Card (Col span 4) -->
            <div class="bento-card md:col-span-4 flex flex-col gap-sm">
                <div class="flex items-center gap-sm mb-sm text-primary">
                    <span class="material-symbols-outlined text-[32px]" data-icon="person">person</span>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Data Pasien</h3>
                </div>
                <div class="bg-surface-container-low p-sm rounded-lg border border-outline-variant flex justify-between items-center">
                    <span class="font-label-md text-label-md text-secondary">Nama</span>
                    <span class="font-body-md text-body-md text-on-surface font-semibold"><?php echo htmlspecialchars($diagnosa['nama_pasien']); ?></span>
                </div>
                <div class="bg-surface-container-low p-sm rounded-lg border border-outline-variant flex justify-between items-center">
                    <span class="font-label-md text-label-md text-secondary">Usia</span>
                    <span class="font-body-md text-body-md text-on-surface"><?php echo $diagnosa['umur']; ?> Tahun</span>
                </div>
                <div class="bg-surface-container-low p-sm rounded-lg border border-outline-variant flex justify-between items-center">
                    <span class="font-label-md text-label-md text-secondary">Jenis Kelamin</span>
                    <span class="font-body-md text-body-md text-on-surface"><?php echo htmlspecialchars($diagnosa['jenis_kelamin']); ?></span>
                </div>
                <div class="bg-surface-container-low p-sm rounded-lg border border-outline-variant flex justify-between items-center">
                    <span class="font-label-md text-label-md text-secondary">Tanggal Diagnosis</span>
                    <span class="font-body-md text-body-md text-on-surface"><?php echo date('d M Y H:i', strtotime($diagnosa['tanggal_diagnosa'])); ?></span>
                </div>
                <div class="bg-surface-container-low p-sm rounded-lg border border-outline-variant flex flex-col items-start gap-1">
                    <span class="font-label-md text-label-md text-secondary">Alamat</span>
                    <span class="font-body-sm text-body-sm text-on-surface leading-relaxed"><?php echo htmlspecialchars($diagnosa['alamat']); ?></span>
                </div>
            </div>
            
            <!-- Main Result Card (Col span 8) -->
            <div class="bento-card md:col-span-8 flex flex-col md:flex-row gap-lg items-center relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute -right-20 -top-20 opacity-5 pointer-events-none">
                    <span class="material-symbols-outlined text-[200px]" data-icon="biotech">biotech</span>
                </div>
                <div class="flex-1 flex flex-col gap-md z-10 w-full text-center md:text-left">
                    <div class="flex flex-col gap-xs">
                        <span class="font-label-md text-label-md text-secondary uppercase tracking-wider">Hasil Terdeteksi</span>
                        <h3 class="font-headline-lg text-headline-lg text-primary"><?php echo htmlspecialchars($diagnosa['hasil_penyakit']); ?></h3>
                    </div>
                    <div class="flex items-center justify-center md:justify-start gap-sm">
                        <?php
                            $badge_color = "bg-primary-container text-on-primary-container";
                            if ($diagnosa['tingkat_kepastian'] == "Sangat Pasti" || $diagnosa['tingkat_kepastian'] == "Pasti") {
                                $badge_color = "bg-tertiary-container text-on-tertiary-container";
                            } elseif ($diagnosa['tingkat_kepastian'] == "Tidak Pasti" || $diagnosa['tingkat_kepastian'] == "Kurang Pasti") {
                                $badge_color = "bg-error-container text-on-error-container";
                            }
                        ?>
                        <div class="<?php echo $badge_color; ?> px-3 py-1 rounded-full font-label-sm text-label-sm flex items-center gap-xs">
                            <span class="material-symbols-outlined text-[16px]" data-icon="verified">verified</span>
                            <?php echo $diagnosa['tingkat_kepastian']; ?>
                        </div>
                        <span class="font-body-sm text-body-sm text-secondary">Metode Teorema Bayes</span>
                    </div>
                </div>
                
                <!-- Circular Probability Display -->
                <div class="w-40 h-40 relative flex items-center justify-center shrink-0 z-10">
                    <svg class="w-full h-full transform -rotate-90" viewbox="0 0 100 100">
                        <circle cx="50" cy="50" fill="none" r="45" stroke="#E8DDD3" stroke-width="10"></circle>
                        <?php
                            // Calculate dashoffset. Full circumference is 2 * pi * r = 2 * 3.14159 * 45 = 282.74
                            $circumference = 282.74;
                            $offset = $circumference - ($diagnosa['persentase'] / 100) * $circumference;
                        ?>
                        <circle class="transition-all duration-1000 ease-out" cx="50" cy="50" fill="none" r="45" stroke="#FF6B1A" stroke-dasharray="<?php echo $circumference; ?>" stroke-dashoffset="<?php echo $offset; ?>" stroke-width="10"></circle>
                    </svg>
                    <div class="absolute flex flex-col items-center justify-center">
                        <span class="font-headline-md text-headline-md text-primary font-bold"><?php echo number_format($diagnosa['persentase'], 1); ?>%</span>
                        <span class="font-label-sm text-label-sm text-secondary">Probabilitas</span>
                    </div>
                </div>
            </div>
            
            <!-- Probability Chart (Col span 6) -->
            <div class="bento-card md:col-span-6 flex flex-col h-[380px]">
                <div class="flex items-center gap-sm mb-md text-secondary">
                    <span class="material-symbols-outlined" data-icon="bar_chart">bar_chart</span>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Distribusi Probabilitas</h3>
                </div>
                <div class="flex-1 w-full relative h-[250px]">
                    <canvas id="probabilityChart"></canvas>
                </div>
            </div>
            
            <!-- Selected Symptoms with Evidence Detail (Col span 6) -->
            <div class="bento-card md:col-span-6 flex flex-col h-[380px]">
                <div class="flex items-center gap-sm mb-md text-secondary">
                    <span class="material-symbols-outlined" data-icon="list_alt">list_alt</span>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Gejala Terpilih (<?php echo $has_detail ? count($detail_list) : count($gejala_list); ?>)</h3>
                </div>
                <div class="flex-1 overflow-y-auto pr-2">
                    <?php if ($has_detail && !empty($detail_list)): ?>
                        <!-- Tabel detail evidence -->
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-primary text-on-primary border-b border-primary">
                                    <th class="font-label-sm text-label-sm text-on-primary py-2 px-1">Kode</th>
                                    <th class="font-label-sm text-label-sm text-on-primary py-2 px-1">Gejala</th>
                                    <th class="font-label-sm text-label-sm text-on-primary py-2 px-1 text-center">Nilai</th>
                                    <th class="font-label-sm text-label-sm text-on-primary py-2 px-1 text-center">Keyakinan</th>
                                    <th class="font-label-sm text-label-sm text-on-primary py-2 px-1 text-center">Evidence</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detail_list as $d): ?>
                                <tr class="border-b border-outline-variant hover:bg-surface-container-low transition-colors">
                                    <td class="py-2 px-1 font-mono font-label-sm text-label-sm text-primary"><?php echo $d['kode_gejala']; ?></td>
                                    <td class="py-2 px-1 font-body-sm text-body-sm text-on-surface"><?php echo htmlspecialchars($d['nama_gejala']); ?></td>
                                    <td class="py-2 px-1 text-center font-mono font-label-sm text-label-sm text-secondary"><?php echo number_format($d['nilai_gejala'], 2); ?></td>
                                    <td class="py-2 px-1 text-center">
                                        <span class="inline-block bg-primary-fixed text-on-primary-fixed-variant font-label-sm text-label-sm px-2 py-0.5 rounded-full"><?php echo htmlspecialchars($d['label_keyakinan']); ?> (<?php echo number_format($d['tingkat_keyakinan'], 1); ?>)</span>
                                    </td>
                                    <td class="py-2 px-1 text-center font-mono font-label-md text-label-md text-on-surface font-bold"><?php echo number_format($d['nilai_evidence'], 4); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php elseif (!empty($gejala_list)): ?>
                        <!-- Fallback: tampilan lama untuk data historis -->
                        <div class="space-y-2">
                        <?php foreach ($gejala_list as $g): ?>
                            <div class="flex justify-between items-center p-sm bg-surface rounded border border-outline-variant">
                                <div class="flex items-center gap-sm">
                                    <span class="material-symbols-outlined text-primary text-[20px]" data-icon="check_circle">check_circle</span>
                                    <span class="font-body-sm text-body-sm text-on-surface"><?php echo htmlspecialchars($g['nama_gejala']); ?></span>
                                </div>
                                <span class="font-label-sm text-label-sm text-secondary bg-surface-container px-2 py-1 rounded shrink-0 font-mono"><?php echo $g['kode_gejala']; ?></span>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-md text-on-surface-variant">Tidak ada gejala dipilih.</div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!empty($bayes_detail)): ?>
            <!-- Detail Perhitungan Bayes (Col span 12) -->
            <div class="bento-card md:col-span-12">
                <div class="flex items-center gap-sm mb-md text-secondary">
                    <span class="material-symbols-outlined" data-icon="calculate">calculate</span>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Detail Perhitungan Teorema Bayes</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-primary text-on-primary border-b-2 border-primary">
                                <th class="font-label-md text-label-md text-on-primary py-3 px-2">Penyakit</th>
                                <th class="font-label-md text-label-md text-on-primary py-3 px-2 text-center">Nilai Bayes</th>
                                <th class="font-label-md text-label-md text-on-primary py-3 px-2 text-center">Total Evidence</th>
                                <th class="font-label-md text-label-md text-on-primary py-3 px-2 text-center">Hasil Bayes</th>
                                <th class="font-label-md text-label-md text-on-primary py-3 px-2 text-center">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Urutkan berdasarkan hasil_bayes descending
                            usort($bayes_detail, function($a, $b) {
                                return $b['hasil_bayes'] <=> $a['hasil_bayes'];
                            });
                            foreach ($bayes_detail as $bd): 
                                $is_detected = ($bd['nama'] == $diagnosa['hasil_penyakit']);
                                $row_class = $is_detected ? 'bg-primary-fixed/30' : '';
                            ?>
                            <tr class="border-b border-outline-variant hover:bg-surface-container-low transition-colors <?php echo $row_class; ?>">
                                <td class="py-3 px-2">
                                    <div class="flex items-center gap-xs">
                                        <?php if ($is_detected): ?>
                                            <span class="material-symbols-outlined text-primary text-[18px]" data-icon="check_circle">check_circle</span>
                                        <?php endif; ?>
                                        <span class="font-body-md text-body-md text-on-surface <?php echo $is_detected ? 'font-bold' : ''; ?>"><?php echo htmlspecialchars($bd['nama']); ?></span>
                                        <span class="font-label-sm text-label-sm text-secondary font-mono">(<?php echo $bd['kode']; ?>)</span>
                                    </div>
                                </td>
                                <td class="py-3 px-2 text-center font-mono font-label-md text-label-md text-on-surface"><?php echo number_format($bd['nilai_bayes'], 2); ?></td>
                                <td class="py-3 px-2 text-center font-mono font-label-md text-label-md text-on-surface"><?php echo number_format($bd['total_evidence'], 2); ?></td>
                                <td class="py-3 px-2 text-center font-mono font-label-md text-label-md text-primary font-bold"><?php echo number_format($bd['hasil_bayes'], 4); ?></td>
                                <td class="py-3 px-2 text-center">
                                    <span class="font-label-md text-label-md font-bold <?php echo $is_detected ? 'text-primary' : 'text-on-surface'; ?>"><?php echo number_format($bd['hasil_bayes'] * 100, 2); ?>%</span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Description & Advice (Col span 12) -->
            <div class="bento-card md:col-span-12 grid grid-cols-1 md:grid-cols-2 gap-lg">
                <!-- Description -->
                <div>
                    <div class="flex items-center gap-sm mb-sm text-secondary">
                        <span class="material-symbols-outlined" data-icon="info">info</span>
                        <h4 class="font-label-md text-label-md text-on-surface font-semibold">Deskripsi Penyakit</h4>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed text-justify">
                        <?php echo nl2br(htmlspecialchars($deskripsi)); ?>
                    </p>
                </div>
                <!-- Treatment Advice -->
                <div>
                    <div class="flex items-center gap-sm mb-sm text-secondary">
                        <span class="material-symbols-outlined" data-icon="healing">healing</span>
                        <h4 class="font-label-md text-label-md text-on-surface font-semibold">Saran Penanganan Awal</h4>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed text-justify">
                        <?php echo nl2br(htmlspecialchars($solusi)); ?>
                    </p>
                </div>
            </div>
            
        </div>
    </main>
    
    <!-- Scripts for Chart.js -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('probabilityChart').getContext('2d');
            
            const labels = [];
            const data = [];
            const bgColors = [];
            
            <?php
            // Sort chart data descending for better readability in chart
            usort($chart_data, function($a, $b) {
                return $b['percentage'] <=> $a['percentage'];
            });
            // Display only top 5 diseases in the chart for clarity if there are many
            $limit_chart = array_slice($chart_data, 0, 5);
            foreach ($limit_chart as $index => $item):
            ?>
                labels.push("<?php echo htmlspecialchars($item['name']); ?>");
                data.push(<?php echo $item['percentage']; ?>);
                bgColors.push(<?php echo $index === 0 ? "'#FF6B1A'" : "'#E8DDD3'"; ?>);
            <?php endforeach; ?>

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Persentase Probabilitas',
                        data: data,
                        backgroundColor: bgColors,
                        borderRadius: 4,
                        barThickness: 24
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#191c1e',
                            titleFont: { family: 'Inter', size: 12 },
                            bodyFont: { family: 'Inter', size: 12 },
                            padding: 10,
                            cornerRadius: 6,
                            callbacks: {
                                label: function(context) {
                                    return context.raw + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: '#c3c6d7',
                                drawBorder: false,
                            },
                            ticks: {
                                color: '#565e74',
                                font: {
                                    family: 'Inter',
                                    size: 11
                                },
                                stepSize: 25
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                color: '#565e74',
                                font: {
                                    family: 'Inter',
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
