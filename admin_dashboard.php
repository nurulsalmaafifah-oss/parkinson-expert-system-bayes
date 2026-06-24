<?php
// admin_dashboard.php
require_once 'config.php';
require_once 'auth.php';

// Proteksi halaman admin
check_login();

// 1. Hitung total data
$total_penyakit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM penyakit"))['total'];
$total_gejala = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM gejala"))['total'];
$total_diagnosa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM diagnosa"))['total'];
$total_pasien = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pasien"))['total'];

// 2. Query tren diagnosa bulanan untuk 8 bulan terakhir
$months = [];
$counts = [];
for ($i = 7; $i >= 0; $i--) {
    $date = date('Y-m', strtotime("-$i month"));
    $month_name = date('M', strtotime("-$i month"));
    $months[] = $month_name;
    
    $start_date = $date . "-01 00:00:00";
    $end_date = date('Y-m-t', strtotime("-$i month")) . " 23:59:59";
    
    $count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM diagnosa WHERE tanggal_diagnosa BETWEEN '$start_date' AND '$end_date'");
    $counts[] = mysqli_fetch_assoc($count_query)['total'];
}

$max_count = max($counts);
$height_percentages = [];
foreach ($counts as $cnt) {
    if ($max_count > 0) {
        $height_percentages[] = round(($cnt / $max_count) * 100);
    } else {
        $height_percentages[] = 10; // Default height if 0
    }
}

// 3. Query aktivitas terbaru (5 diagnosa terakhir)
$recent_query = mysqli_query($conn, "
    SELECT d.id_diagnosa, d.tanggal_diagnosa, d.hasil_penyakit, d.tingkat_kepastian, p.nama_pasien 
    FROM diagnosa d
    JOIN pasien p ON d.id_pasien = p.id_pasien
    ORDER BY d.id_diagnosa DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Dashboard Admin - ParkinsonExpert</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&amp;display=swap" rel="stylesheet"/>
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
                    "label-md": ["14px", {"lineHeight": "20px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                    "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                    "headline-md": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                    "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                    "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                    "headline-sm": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                    "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                    "headline-lg-mobile": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "700"}],
                    "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "500"}]
            }
          }
        }
      }
    </script>
    <style>
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #c3c6d7; 
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #737686; 
        }
    </style>
</head>
<body class="bg-surface-container-low font-body-md text-on-surface antialiased h-screen overflow-hidden flex">
    
    <!-- Shared Component: SideNavBar -->
    <aside class="fixed left-0 top-0 h-full w-[280px] bg-inverse-surface shadow-md flex flex-col py-md z-50">
        <!-- Header -->
        <div class="px-md mb-lg flex items-center gap-sm">
            <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center shrink-0 overflow-hidden relative border border-outline-variant">
                <span class="material-symbols-outlined text-on-primary-container">admin_panel_settings</span>
            </div>
            <div>
                <h2 class="font-headline-sm text-headline-sm text-surface-bright"><?php echo htmlspecialchars($_SESSION['admin_nama']); ?></h2>
                <p class="font-label-sm text-label-sm text-surface-variant opacity-80">Administrator</p>
            </div>
        </div>
        <!-- CTA -->
        <div class="px-md mb-lg">
            <a href="diagnosa.php" class="w-full h-[40px] bg-primary text-on-primary rounded font-label-md text-label-md flex items-center justify-center gap-xs hover:bg-surface-tint transition-colors shadow-sm">
                <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
                Diagnosa Baru
            </a>
        </div>
        <!-- Navigation Links -->
        <nav class="flex-1 flex flex-col font-label-md text-label-md">
            <!-- Active Tab: Dashboard -->
            <a class="flex items-center gap-sm bg-primary-container text-on-primary-container border-l-4 border-primary px-4 py-3 scale-95 transition-transform" href="admin_dashboard.php">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">dashboard</span>
                Dashboard
            </a>
            <!-- Inactive Tabs -->
            <a class="flex items-center gap-sm text-surface-variant px-4 py-3 hover:bg-on-secondary-fixed-variant transition-all" href="admin_penyakit.php">
                <span class="material-symbols-outlined">neurology</span>
                Data Penyakit
            </a>
            <a class="flex items-center gap-sm text-surface-variant px-4 py-3 hover:bg-on-secondary-fixed-variant transition-all" href="admin_gejala.php">
                <span class="material-symbols-outlined">format_list_bulleted</span>
                Data Gejala
            </a>
            <a class="flex items-center gap-sm text-surface-variant px-4 py-3 hover:bg-on-secondary-fixed-variant transition-all" href="admin_basis.php">
                <span class="material-symbols-outlined">psychology</span>
                Basis Pengetahuan
            </a>
            <a class="flex items-center gap-sm text-surface-variant px-4 py-3 hover:bg-on-secondary-fixed-variant transition-all" href="admin_laporan.php">
                <span class="material-symbols-outlined">analytics</span>
                Laporan Diagnosa
            </a>
        </nav>
        <!-- Footer -->
        <div class="mt-auto pt-md font-label-md text-label-md">
            <a class="flex items-center gap-sm text-surface-variant px-4 py-3 hover:bg-on-secondary-fixed-variant transition-all" href="logout.php">
                <span class="material-symbols-outlined">logout</span>
                Logout
            </a>
        </div>
    </aside>

    <!-- Main Content Canvas -->
    <main class="ml-[280px] flex-1 flex flex-col h-full overflow-y-auto">
        <!-- Top Professional Header -->
        <header class="h-[72px] px-margin-desktop flex items-center justify-between bg-surface border-b border-outline-variant sticky top-0 z-40">
            <div>
                <h1 class="font-headline-md text-headline-md text-on-surface">Overview</h1>
                <p class="font-body-sm text-body-sm text-secondary"><?php echo date('l, d F Y'); ?></p>
            </div>
            <div class="flex items-center gap-md">
                <div class="flex items-center gap-sm pl-md border-l border-outline-variant">
                    <span class="font-label-md text-label-md text-on-surface"><?php echo htmlspecialchars($_SESSION['admin_nama']); ?></span>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="px-margin-desktop py-lg flex-1 flex flex-col gap-gutter">
            <!-- 4 Stat Cards Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter">
                <!-- Card 1: Penyakit -->
                <div class="bg-surface rounded-lg border border-outline-variant p-md flex flex-col gap-sm relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-label-md text-secondary">Total Penyakit</span>
                        <div class="w-8 h-8 rounded bg-primary-fixed flex items-center justify-center text-on-primary-fixed-variant">
                            <span class="material-symbols-outlined text-[20px]">neurology</span>
                        </div>
                    </div>
                    <div class="font-headline-lg text-headline-lg text-on-surface"><?php echo $total_penyakit; ?></div>
                    <div class="flex items-center gap-xs font-label-sm text-label-sm text-secondary">
                        <span>Penyakit terdaftar</span>
                    </div>
                    <div class="absolute bottom-0 left-0 w-full h-[2px] bg-outline-variant group-hover:bg-primary transition-colors"></div>
                </div>
                <!-- Card 2: Gejala -->
                <div class="bg-surface rounded-lg border border-outline-variant p-md flex flex-col gap-sm relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-label-md text-secondary">Total Gejala</span>
                        <div class="w-8 h-8 rounded bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed-variant">
                            <span class="material-symbols-outlined text-[20px]">format_list_bulleted</span>
                        </div>
                    </div>
                    <div class="font-headline-lg text-headline-lg text-on-surface"><?php echo $total_gejala; ?></div>
                    <div class="flex items-center gap-xs font-label-sm text-label-sm text-secondary">
                        <span>Gejala terdaftar</span>
                    </div>
                    <div class="absolute bottom-0 left-0 w-full h-[2px] bg-outline-variant group-hover:bg-primary transition-colors"></div>
                </div>
                <!-- Card 3: Diagnosa -->
                <div class="bg-surface rounded-lg border border-outline-variant p-md flex flex-col gap-sm relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-label-md text-secondary">Total Diagnosa</span>
                        <div class="w-8 h-8 rounded bg-primary-fixed flex items-center justify-center text-on-primary-fixed-variant">
                            <span class="material-symbols-outlined text-[20px]">biotech</span>
                        </div>
                    </div>
                    <div class="font-headline-lg text-headline-lg text-on-surface"><?php echo $total_diagnosa; ?></div>
                    <div class="flex items-center gap-xs font-label-sm text-label-sm text-secondary">
                        <span>Riwayat pemeriksaan</span>
                    </div>
                    <div class="absolute bottom-0 left-0 w-full h-[2px] bg-outline-variant group-hover:bg-primary transition-colors"></div>
                </div>
                <!-- Card 4: Pasien -->
                <div class="bg-surface rounded-lg border border-outline-variant p-md flex flex-col gap-sm relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-label-md text-secondary">Total Pasien</span>
                        <div class="w-8 h-8 rounded bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed-variant">
                            <span class="material-symbols-outlined text-[20px]">groups</span>
                        </div>
                    </div>
                    <div class="font-headline-lg text-headline-lg text-on-surface"><?php echo $total_pasien; ?></div>
                    <div class="flex items-center gap-xs font-label-sm text-label-sm text-secondary">
                        <span>Pasien diperiksa</span>
                    </div>
                    <div class="absolute bottom-0 left-0 w-full h-[2px] bg-outline-variant group-hover:bg-primary transition-colors"></div>
                </div>
            </div>

            <!-- Bento Grid: Chart & Activity Table -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter h-[400px]">
                <!-- Chart Area (Span 8) -->
                <div class="lg:col-span-8 bg-surface rounded-lg border border-outline-variant flex flex-col h-full">
                    <div class="p-md border-b border-outline-variant flex items-center justify-between">
                        <h3 class="font-headline-sm text-headline-sm text-on-surface">Tren Diagnosis</h3>
                        <div class="flex items-center gap-sm">
                            <span class="font-label-sm text-label-sm px-xs py-1 text-primary bg-primary-fixed rounded">Bulanan</span>
                        </div>
                    </div>
                    <div class="flex-1 p-md relative flex items-end justify-between gap-4 overflow-hidden">
                        <!-- Simulated Bar Chart using HTML/CSS -->
                        <div class="absolute inset-0 p-md flex flex-col justify-between z-0 pointer-events-none opacity-20">
                            <div class="w-full h-px bg-outline"></div>
                            <div class="w-full h-px bg-outline"></div>
                            <div class="w-full h-px bg-outline"></div>
                            <div class="w-full h-px bg-outline"></div>
                            <div class="w-full h-px bg-outline"></div>
                        </div>
                        <?php foreach ($months as $index => $m_name): 
                                $h_percent = $height_percentages[$index];
                                $cnt = $counts[$index];
                                // Alternate colors for visual interest
                                $bar_color = ($index % 2 == 0) ? 'bg-primary-fixed' : 'bg-primary';
                        ?>
                            <div class="w-full h-[<?php echo $h_percent; ?>%] <?php echo $bar_color; ?> rounded-t z-10 relative group transition-all duration-500">
                                <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-inverse-surface text-surface px-2 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20 shadow">
                                    <?php echo $cnt; ?> Diagnosa
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="px-md pb-md flex justify-between font-label-sm text-label-sm text-secondary border-t border-outline-variant pt-2 mt-auto">
                        <?php foreach ($months as $m_name): ?>
                            <span><?php echo $m_name; ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Recent Activity (Span 4) -->
                <div class="lg:col-span-4 bg-surface rounded-lg border border-outline-variant flex flex-col h-full">
                    <div class="p-md border-b border-outline-variant flex items-center justify-between">
                        <h3 class="font-headline-sm text-headline-sm text-on-surface">Aktivitas Terbaru</h3>
                        <a href="admin_laporan.php" class="font-label-sm text-label-sm text-primary hover:underline">Lihat Semua</a>
                    </div>
                    <div class="flex-1 overflow-y-auto">
                        <?php if (mysqli_num_rows($recent_query) > 0): ?>
                            <?php while ($recent = mysqli_fetch_assoc($recent_query)): 
                                $time_diff = time() - strtotime($recent['tanggal_diagnosa']);
                                if ($time_diff < 60) {
                                    $time_str = "baru saja";
                                } elseif ($time_diff < 3600) {
                                    $time_str = round($time_diff / 60) . " menit lalu";
                                } elseif ($time_diff < 86400) {
                                    $time_str = round($time_diff / 3600) . " jam lalu";
                                } else {
                                    $time_str = date('d M', strtotime($recent['tanggal_diagnosa']));
                                }
                            ?>
                                <a href="hasil.php?id=<?php echo $recent['id_diagnosa']; ?>&src=admin" class="flex items-center gap-sm px-md py-sm border-b border-outline-variant h-[48px] hover:bg-surface-container-low transition-colors cursor-pointer">
                                    <div class="w-2 h-2 rounded-full bg-tertiary-container shrink-0"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-label-sm text-label-sm text-on-surface truncate">
                                            <?php echo htmlspecialchars($recent['nama_pasien']); ?>: <?php echo htmlspecialchars($recent['hasil_penyakit']); ?>
                                        </p>
                                    </div>
                                    <span class="font-body-sm text-body-sm text-secondary shrink-0 text-[11px]"><?php echo $time_str; ?></span>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-xl text-on-surface-variant text-body-sm">Belum ada diagnosa dilakukan.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
