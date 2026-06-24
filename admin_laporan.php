<?php
// admin_laporan.php
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

// Pagination Setup
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $limit;

// Total records
$total_query = mysqli_query($conn, "
    SELECT COUNT(*) as total 
    FROM diagnosa d
    JOIN pasien p ON d.id_pasien = p.id_pasien
    $where_clause
");
$total_records = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_records / $limit);

// Query data diagnosa
$laporan_query = mysqli_query($conn, "
    SELECT d.*, p.nama_pasien, p.jenis_kelamin, p.umur 
    FROM diagnosa d
    JOIN pasien p ON d.id_pasien = p.id_pasien
    $where_clause
    ORDER BY d.id_diagnosa DESC
    LIMIT $start, $limit
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Laporan Diagnosa - ParkinsonExpert</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
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
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-surface text-on-surface flex min-h-screen">
    
    <!-- Shared Component: SideNavBar -->
    <aside class="hidden md:flex fixed left-0 top-0 h-full w-[280px] bg-inverse-surface shadow-md flex-col py-md z-45">
        <!-- Header -->
        <div class="px-md mb-lg">
            <h1 class="font-headline-sm text-headline-sm text-surface-bright mb-xs">Admin Portal</h1>
            <p class="font-body-sm text-body-sm text-surface-variant">Expert System Management</p>
        </div>
        <!-- CTA -->
        <div class="px-md mb-lg">
            <a href="diagnosa.php" class="w-full bg-primary text-on-primary font-label-md text-label-md py-sm rounded flex items-center justify-center gap-xs hover:bg-on-primary-fixed-variant transition-colors shadow-sm">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">add</span>
                Diagnosa Baru
            </a>
        </div>
        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto">
            <ul class="flex flex-col gap-xs">
                <li>
                    <a class="flex items-center gap-sm text-surface-variant px-4 py-3 hover:bg-surface-container-high transition-all" href="admin_dashboard.php">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span class="font-label-md text-label-md">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a class="flex items-center gap-sm text-surface-variant px-4 py-3 hover:bg-surface-container-high transition-all" href="admin_penyakit.php">
                        <span class="material-symbols-outlined">neurology</span>
                        <span class="font-label-md text-label-md">Data Penyakit</span>
                    </a>
                </li>
                <li>
                    <a class="flex items-center gap-sm text-surface-variant px-4 py-3 hover:bg-surface-container-high transition-all" href="admin_gejala.php">
                        <span class="material-symbols-outlined">format_list_bulleted</span>
                        <span class="font-label-md text-label-md">Data Gejala</span>
                    </a>
                </li>
                <li>
                    <a class="flex items-center gap-sm text-surface-variant px-4 py-3 hover:bg-surface-container-high transition-all" href="admin_basis.php">
                        <span class="material-symbols-outlined">psychology</span>
                        <span class="font-label-md text-label-md">Basis Pengetahuan</span>
                    </a>
                </li>
                <li>
                    <!-- Active Tab: Laporan -->
                    <a class="flex items-center gap-sm bg-primary-container text-on-primary-container border-l-4 border-primary px-4 py-3 scale-95 transition-transform" href="admin_laporan.php">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">analytics</span>
                        <span class="font-label-md text-label-md">Laporan Diagnosa</span>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- Footer -->
        <div class="mt-auto px-md">
            <a class="flex items-center gap-sm text-surface-variant px-4 py-3 hover:bg-surface-container-high transition-all" href="logout.php">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-label-md text-label-md">Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Canvas -->
    <main class="flex-1 md:ml-[280px] p-margin-mobile md:p-margin-desktop flex flex-col min-w-0">
        <!-- Header Section -->
        <header class="mb-lg flex flex-col md:flex-row md:items-center justify-between gap-md">
            <div>
                <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface mb-xs">Laporan Diagnosa</h2>
                <p class="font-body-md text-body-md text-on-surface-variant">Riwayat hasil diagnosa sistem pakar Parkinson.</p>
            </div>
            
            <div class="flex items-center gap-sm">
                <!-- PDF Export button passing parameters -->
                <a href="export_pdf.php?search=<?php echo urlencode($search); ?>&tgl_mulai=<?php echo urlencode($tgl_mulai); ?>&tgl_selesai=<?php echo urlencode($tgl_selesai); ?>" 
                   target="_blank" 
                   class="flex items-center gap-xs px-sm py-2 border border-outline-variant rounded text-primary font-label-md text-label-md hover:bg-primary-container hover:text-on-primary-container transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                    Cetak PDF / Print
                </a>
                <!-- Excel Export button passing parameters -->
                <a href="export_excel.php?search=<?php echo urlencode($search); ?>&tgl_mulai=<?php echo urlencode($tgl_mulai); ?>&tgl_selesai=<?php echo urlencode($tgl_selesai); ?>" 
                   class="flex items-center gap-xs px-sm py-2 bg-tertiary-container text-on-primary font-label-md text-label-md rounded hover:bg-on-tertiary-fixed-variant transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">table_view</span>
                    Export Excel
                </a>
            </div>
        </header>

        <!-- Filters & Search Toolbar -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-lg p-md mb-lg shadow-sm">
            <form method="GET" action="admin_laporan.php">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-md items-end">
                    <!-- Search -->
                    <div class="md:col-span-4">
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs">Cari Pasien</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                            <input class="w-full pl-[40px] pr-sm py-2 border border-outline-variant rounded bg-surface focus:ring-2 focus:ring-primary focus:border-primary font-body-sm text-body-sm text-on-surface transition-all" name="search" placeholder="Nama pasien..." type="text" value="<?php echo htmlspecialchars($search); ?>"/>
                        </div>
                    </div>
                    <!-- Date Range -->
                    <div class="md:col-span-3">
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs">Mulai Tanggal</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-on-surface-variant">calendar_today</span>
                            <input class="w-full pl-[40px] pr-sm py-2 border border-outline-variant rounded bg-surface focus:ring-2 focus:ring-primary focus:border-primary font-body-sm text-body-sm text-on-surface transition-all cursor-pointer" name="tgl_mulai" type="date" value="<?php echo htmlspecialchars($tgl_mulai); ?>"/>
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs">Sampai Tanggal</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-on-surface-variant">calendar_month</span>
                            <input class="w-full pl-[40px] pr-sm py-2 border border-outline-variant rounded bg-surface focus:ring-2 focus:ring-primary focus:border-primary font-body-sm text-body-sm text-on-surface transition-all cursor-pointer" name="tgl_selesai" type="date" value="<?php echo htmlspecialchars($tgl_selesai); ?>"/>
                        </div>
                    </div>
                    <!-- Filter Action -->
                    <div class="md:col-span-2">
                        <button type="submit" class="w-full bg-secondary-container text-on-secondary-fixed-variant font-label-md text-label-md py-2 rounded hover:bg-secondary-fixed-dim transition-colors flex items-center justify-center gap-xs cursor-pointer h-[40px]">
                            <span class="material-symbols-outlined">filter_list</span>
                            Terapkan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Data Table Container -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-lg overflow-hidden shadow-sm flex-1 flex flex-col mb-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr class="bg-primary text-on-primary border-b border-primary">
                            <th class="px-md py-3 font-label-md text-label-md text-on-primary whitespace-nowrap w-28">ID Diagnosa</th>
                            <th class="px-md py-3 font-label-md text-label-md text-on-primary whitespace-nowrap w-36">Tanggal</th>
                            <th class="px-md py-3 font-label-md text-label-md text-on-primary">Nama Pasien</th>
                            <th class="px-md py-3 font-label-md text-label-md text-on-primary whitespace-nowrap w-32">Jenis Kelamin</th>
                            <th class="px-md py-3 font-label-md text-label-md text-on-primary text-right w-16">Usia</th>
                            <th class="px-md py-3 font-label-md text-label-md text-on-primary">Hasil Diagnosa</th>
                            <th class="px-md py-3 font-label-md text-label-md text-on-primary text-right w-28">Persentase</th>
                            <th class="px-md py-3 font-label-md text-label-md text-on-primary text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm text-on-surface">
                        <?php if (mysqli_num_rows($laporan_query) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($laporan_query)): 
                                $badge_color = "bg-primary-container text-on-primary-container";
                                $text_color = "text-primary";
                                
                                if ($row['tingkat_kepastian'] == "Sangat Pasti" || $row['tingkat_kepastian'] == "Pasti") {
                                    $badge_color = "bg-error-container text-on-error-container"; // Matches Parkinson Positive in prototype red
                                    $text_color = "text-error";
                                } elseif ($row['tingkat_kepastian'] == "Tidak Pasti" || $row['tingkat_kepastian'] == "Kurang Pasti") {
                                    $badge_color = "bg-tertiary-fixed text-on-tertiary-fixed"; // Matches Negative green
                                    $text_color = "text-tertiary";
                                } else {
                                    $badge_color = "bg-secondary-container text-on-secondary-fixed-variant";
                                    $text_color = "text-secondary";
                                }
                            ?>
                                <tr class="hover:bg-surface transition-colors h-[48px]">
                                    <td class="px-md py-2 font-mono text-secondary">#DG-<?php echo str_pad($row['id_diagnosa'], 4, "0", STR_PAD_LEFT); ?></td>
                                    <td class="px-md py-2 text-on-surface-variant"><?php echo date('d M Y H:i', strtotime($row['tanggal_diagnosa'])); ?></td>
                                    <td class="px-md py-2 font-medium"><?php echo htmlspecialchars($row['nama_pasien']); ?></td>
                                    <td class="px-md py-2"><?php echo htmlspecialchars($row['jenis_kelamin']); ?></td>
                                    <td class="px-md py-2 text-right"><?php echo $row['umur']; ?></td>
                                    <td class="px-md py-2">
                                        <span class="inline-flex items-center px-2 py-[2px] rounded text-xs font-semibold <?php echo $badge_color; ?>">
                                            <?php echo htmlspecialchars($row['hasil_penyakit']); ?>
                                        </span>
                                    </td>
                                    <td class="px-md py-2 text-right font-medium <?php echo $text_color; ?>"><?php echo number_format($row['persentase'], 1); ?>%</td>
                                    <td class="px-md py-2 text-center">
                                        <a href="hasil.php?id=<?php echo $row['id_diagnosa']; ?>&src=admin" class="text-primary hover:text-on-primary-fixed-variant inline-block p-1" title="Detail">
                                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="py-6 text-center text-on-surface-variant">Tidak ada data riwayat diagnosa ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-auto px-md py-4 border-t border-outline-variant flex items-center justify-between bg-surface-container-lowest">
                <span class="font-body-sm text-body-sm text-on-surface-variant">
                    Menampilkan <?php echo $total_records > 0 ? $start + 1 : 0; ?>-<?php echo min($start + $limit, $total_records); ?> dari <?php echo $total_records; ?> data
                </span>
                <?php if ($total_pages > 1): ?>
                    <div class="flex items-center gap-xs">
                        <!-- Prev page -->
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&tgl_mulai=<?php echo urlencode($tgl_mulai); ?>&tgl_selesai=<?php echo urlencode($tgl_selesai); ?>" 
                           class="p-xs text-on-surface-variant hover:text-primary transition-colors flex items-center <?php echo $page <= 1 ? 'pointer-events-none opacity-50' : ''; ?>">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </a>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&tgl_mulai=<?php echo urlencode($tgl_mulai); ?>&tgl_selesai=<?php echo urlencode($tgl_selesai); ?>" 
                               class="w-[32px] h-[32px] flex items-center justify-center rounded-DEFAULT font-label-sm text-label-sm <?php echo $page == $i ? 'bg-primary-container text-on-primary-container' : 'hover:bg-surface-container text-on-surface-variant transition-colors'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <!-- Next page -->
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&tgl_mulai=<?php echo urlencode($tgl_mulai); ?>&tgl_selesai=<?php echo urlencode($tgl_selesai); ?>" 
                           class="p-xs text-on-surface-variant hover:text-primary transition-colors flex items-center <?php echo $page >= $total_pages ? 'pointer-events-none opacity-50' : ''; ?>">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
