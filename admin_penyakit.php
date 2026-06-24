<?php
// admin_penyakit.php
require_once 'config.php';
require_once 'auth.php';

// Proteksi halaman admin
check_login();

$message = "";
$error = "";

// 1. Proses Delete
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = intval($_GET['id']);
    $delete = mysqli_query($conn, "DELETE FROM penyakit WHERE id_penyakit = $id");
    if ($delete) {
        $message = "Data penyakit berhasil dihapus!";
    } else {
        $error = "Gagal menghapus data penyakit: " . mysqli_error($conn);
    }
}

// 2. Proses Tambah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $kode = mysqli_real_escape_string($conn, $_POST['kode_penyakit']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_penyakit']);
    $nilai = floatval($_POST['nilai_bayes']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $solusi = mysqli_real_escape_string($conn, $_POST['solusi']);

    // Validasi kode unik
    $check = mysqli_query($conn, "SELECT * FROM penyakit WHERE kode_penyakit = '$kode'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Kode penyakit '$kode' sudah terdaftar!";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO penyakit (kode_penyakit, nama_penyakit, nilai_bayes, deskripsi, solusi) VALUES ('$kode', '$nama', $nilai, '$deskripsi', '$solusi')");
        if ($insert) {
            $message = "Penyakit baru berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan penyakit: " . mysqli_error($conn);
        }
    }
}

// 3. Proses Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = intval($_POST['id_penyakit']);
    $kode = mysqli_real_escape_string($conn, $_POST['kode_penyakit']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_penyakit']);
    $nilai = floatval($_POST['nilai_bayes']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $solusi = mysqli_real_escape_string($conn, $_POST['solusi']);

    // Validasi kode unik selain record ini
    $check = mysqli_query($conn, "SELECT * FROM penyakit WHERE kode_penyakit = '$kode' AND id_penyakit != $id");
    if (mysqli_num_rows($check) > 0) {
        $error = "Kode penyakit '$kode' sudah digunakan oleh penyakit lain!";
    } else {
        $update = mysqli_query($conn, "UPDATE penyakit SET kode_penyakit = '$kode', nama_penyakit = '$nama', nilai_bayes = $nilai, deskripsi = '$deskripsi', solusi = '$solusi' WHERE id_penyakit = $id");
        if ($update) {
            $message = "Data penyakit berhasil diperbarui!";
        } else {
            $error = "Gagal memperbarui penyakit: " . mysqli_error($conn);
        }
    }
}

// 4. Pencarian dan Pagination Setup
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$limit = 5; // Menampilkan 5 data per halaman
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $limit;

$where_clause = "";
if (!empty($search)) {
    $where_clause = "WHERE kode_penyakit LIKE '%$search%' OR nama_penyakit LIKE '%$search%'";
}

// Hitung total records untuk pagination
$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM penyakit $where_clause");
$total_records = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_records / $limit);

// Query data penyakit
$penyakit_query = mysqli_query($conn, "SELECT * FROM penyakit $where_clause ORDER BY kode_penyakit ASC LIMIT $start, $limit");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Data Penyakit - ParkinsonExpert</title>
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
<body class="bg-surface-container-low font-body-md text-on-surface antialiased h-screen overflow-hidden flex">
    
    <!-- Shared Component: SideNavBar -->
    <aside class="fixed left-0 top-0 h-full w-[280px] bg-inverse-surface shadow-md flex flex-col py-md z-45">
        <!-- Header -->
        <div class="px-md mb-lg flex items-center gap-sm">
            <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center shrink-0 border border-outline-variant">
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
            <a class="flex items-center gap-sm text-surface-variant px-4 py-3 hover:bg-on-secondary-fixed-variant transition-all" href="admin_dashboard.php">
                <span class="material-symbols-outlined">dashboard</span>
                Dashboard
            </a>
            <!-- Active Tab: Disease Data -->
            <a class="flex items-center gap-sm bg-primary-container text-on-primary-container border-l-4 border-primary px-4 py-3 scale-95 transition-transform" href="admin_penyakit.php">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">neurology</span>
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

    <!-- Main Content Area -->
    <main class="flex-1 ml-[280px] p-margin-mobile md:p-margin-desktop min-h-screen bg-surface overflow-y-auto">
        <!-- Header -->
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-xl gap-md">
            <div>
                <h1 class="font-headline-lg text-headline-lg text-on-surface">Data Penyakit</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">Kelola data penyakit Parkinson dan referensi klinis terkait.</p>
            </div>
            
            <div class="flex items-center gap-sm w-full sm:w-auto">
                <!-- Search Form -->
                <form method="GET" action="admin_penyakit.php" class="relative w-full sm:w-64">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                    <input class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded font-body-sm text-body-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" name="search" placeholder="Cari penyakit..." type="text" value="<?php echo htmlspecialchars($search); ?>"/>
                </form>
                
                <button class="flex items-center justify-center gap-xs bg-primary text-on-primary px-4 py-2 h-[40px] rounded font-label-md text-label-md hover:bg-on-primary-fixed-variant transition-colors whitespace-nowrap shadow-sm" onclick="document.getElementById('modal-tambah').classList.remove('hidden')">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Tambah Data
                </button>
            </div>
        </header>

        <!-- Alert messages -->
        <?php if (!empty($message)): ?>
            <div class="bg-tertiary-container text-on-tertiary-container p-sm rounded border border-tertiary/20 mb-md flex items-center gap-xs text-body-sm">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                <span><?php echo $message; ?></span>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="bg-error-container text-on-error-container p-sm rounded border border-error/20 mb-md flex items-center gap-xs text-body-sm">
                <span class="material-symbols-outlined text-[20px]">error</span>
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <!-- Data Table Container -->
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden mb-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="border-b border-primary bg-primary text-on-primary">
                            <th class="py-3 px-4 font-label-md text-label-md text-on-primary w-16">Kode</th>
                            <th class="py-3 px-4 font-label-md text-label-md text-on-primary w-48">Nama Penyakit</th>
                            <th class="py-3 px-4 font-label-md text-label-md text-on-primary w-24">Nilai Bayes</th>
                            <th class="py-3 px-4 font-label-md text-label-md text-on-primary min-w-[200px]">Deskripsi</th>
                            <th class="py-3 px-4 font-label-md text-label-md text-on-primary min-w-[200px]">Solusi</th>
                            <th class="py-3 px-4 font-label-md text-label-md text-on-primary w-24 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="font-body-sm text-body-sm text-on-surface">
                        <?php if (mysqli_num_rows($penyakit_query) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($penyakit_query)): ?>
                                <tr class="border-b border-outline-variant hover:bg-surface-container-low transition-colors group">
                                    <td class="py-3 px-4 font-label-md text-label-md"><?php echo htmlspecialchars($row['kode_penyakit']); ?></td>
                                    <td class="py-3 px-4 font-medium"><?php echo htmlspecialchars($row['nama_penyakit']); ?></td>
                                    <td class="py-3 px-4">
                                        <span class="bg-primary-container text-on-primary-container px-2 py-1 rounded text-xs font-semibold"><?php echo number_format($row['nilai_bayes'], 2); ?></span>
                                    </td>
                                    <td class="py-3 px-4 text-on-surface-variant max-w-[250px] truncate" title="<?php echo htmlspecialchars($row['deskripsi']); ?>">
                                        <?php echo htmlspecialchars($row['deskripsi']); ?>
                                    </td>
                                    <td class="py-3 px-4 text-on-surface-variant max-w-[250px] truncate" title="<?php echo htmlspecialchars($row['solusi']); ?>">
                                        <?php echo htmlspecialchars($row['solusi']); ?>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="flex justify-center gap-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button class="p-1 text-secondary hover:text-primary transition-colors" title="Edit" 
                                                    onclick='openEditModal(<?php echo json_encode($row); ?>)'>
                                                <span class="material-symbols-outlined text-[20px]">edit</span>
                                            </button>
                                            <a href="admin_penyakit.php?action=delete&id=<?php echo $row['id_penyakit']; ?>" 
                                               class="p-1 text-secondary hover:text-error transition-colors" title="Hapus" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus penyakit <?php echo htmlspecialchars($row['nama_penyakit']); ?>? Penghapusan ini juga menghapus aturan relasi yang terkait.')">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-6 text-center text-on-surface-variant">Tidak ada data penyakit ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="border-t border-outline-variant p-4 flex items-center justify-between bg-surface-container-lowest">
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Menampilkan <?php echo $total_records > 0 ? $start + 1 : 0; ?> hingga <?php echo min($start + $limit, $total_records); ?> dari <?php echo $total_records; ?> data
                </p>
                <?php if ($total_pages > 1): ?>
                    <div class="flex gap-2">
                        <!-- Prev Page Button -->
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" 
                           class="px-3 py-1 border border-outline-variant rounded hover:bg-surface-container-low font-label-sm text-label-sm flex items-center <?php echo $page <= 1 ? 'pointer-events-none opacity-50' : ''; ?>">
                            Sebelumnya
                        </a>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                               class="px-3 py-1 rounded font-label-sm text-label-sm <?php echo $page == $i ? 'bg-primary text-on-primary' : 'border border-outline-variant hover:bg-surface-container-low'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <!-- Next Page Button -->
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" 
                           class="px-3 py-1 border border-outline-variant rounded hover:bg-surface-container-low font-label-sm text-label-sm flex items-center <?php echo $page >= $total_pages ? 'pointer-events-none opacity-50' : ''; ?>">
                            Selanjutnya
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Modal Tambah Data -->
    <div class="fixed inset-0 z-50 hidden bg-on-surface/50 backdrop-blur-sm flex items-center justify-center p-4" id="modal-tambah">
        <div class="bg-surface-container-lowest w-full max-w-2xl rounded-xl shadow-lg border border-outline-variant flex flex-col max-h-[90%] overflow-hidden animate-[fadeIn_0.2s_ease-out]">
            <div class="p-6 border-b border-outline-variant flex justify-between items-center">
                <h3 class="font-headline-sm text-headline-sm text-on-surface">Tambah Data Penyakit</h3>
                <button class="text-secondary hover:text-on-surface transition-colors" onclick="document.getElementById('modal-tambah').classList.add('hidden')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="admin_penyakit.php" method="POST" class="flex flex-col overflow-hidden">
                <input type="hidden" name="action" value="tambah">
                <div class="p-6 overflow-y-auto space-y-md">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
                        <div class="space-y-xs">
                            <label class="font-label-sm text-label-sm text-on-surface block">Kode Penyakit</label>
                            <input name="kode_penyakit" required class="w-full p-2 bg-surface-container-lowest border border-outline-variant rounded font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Contoh: P08" type="text"/>
                        </div>
                        <div class="space-y-xs">
                            <label class="font-label-sm text-label-sm text-on-surface block">Nilai Probabilitas (Bayes)</label>
                            <input name="nilai_bayes" required class="w-full p-2 bg-surface-container-lowest border border-outline-variant rounded font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" max="1" min="0" placeholder="0.00 - 1.00" step="0.01" type="number"/>
                        </div>
                    </div>
                    <div class="space-y-xs">
                        <label class="font-label-sm text-label-sm text-on-surface block">Nama Penyakit / Stadium</label>
                        <input name="nama_penyakit" required class="w-full p-2 bg-surface-container-lowest border border-outline-variant rounded font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Masukkan nama penyakit" type="text"/>
                    </div>
                    <div class="space-y-xs">
                        <label class="font-label-sm text-label-sm text-on-surface block">Deskripsi Klinis</label>
                        <textarea name="deskripsi" class="w-full p-2 bg-surface-container-lowest border border-outline-variant rounded font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none" placeholder="Jelaskan gejala dan kondisi..." rows="3"></textarea>
                    </div>
                    <div class="space-y-xs">
                        <label class="font-label-sm text-label-sm text-on-surface block">Solusi / Rekomendasi Penanganan</label>
                        <textarea name="solusi" class="w-full p-2 bg-surface-container-lowest border border-outline-variant rounded font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none" placeholder="Langkah penanganan yang disarankan..." rows="3"></textarea>
                    </div>
                </div>
                <div class="p-6 border-t border-outline-variant bg-surface-container flex justify-end gap-sm">
                    <button type="button" class="px-4 py-2 border border-outline text-secondary rounded font-label-md text-label-md hover:bg-surface-variant transition-colors h-[40px]" onclick="document.getElementById('modal-tambah').classList.add('hidden')">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded font-label-md text-label-md hover:bg-on-primary-fixed-variant transition-colors h-[40px]">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Data -->
    <div class="fixed inset-0 z-50 hidden bg-on-surface/50 backdrop-blur-sm flex items-center justify-center p-4" id="modal-edit">
        <div class="bg-surface-container-lowest w-full max-w-2xl rounded-xl shadow-lg border border-outline-variant flex flex-col max-h-[90%] overflow-hidden animate-[fadeIn_0.2s_ease-out]">
            <div class="p-6 border-b border-outline-variant flex justify-between items-center">
                <h3 class="font-headline-sm text-headline-sm text-on-surface">Ubah Data Penyakit</h3>
                <button class="text-secondary hover:text-on-surface transition-colors" onclick="document.getElementById('modal-edit').classList.add('hidden')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="admin_penyakit.php" method="POST" class="flex flex-col overflow-hidden">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_penyakit" id="edit-id">
                <div class="p-6 overflow-y-auto space-y-md">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
                        <div class="space-y-xs">
                            <label class="font-label-sm text-label-sm text-on-surface block">Kode Penyakit</label>
                            <input name="kode_penyakit" id="edit-kode" required class="w-full p-2 bg-surface-container-lowest border border-outline-variant rounded font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Contoh: P08" type="text"/>
                        </div>
                        <div class="space-y-xs">
                            <label class="font-label-sm text-label-sm text-on-surface block">Nilai Probabilitas (Bayes)</label>
                            <input name="nilai_bayes" id="edit-nilai" required class="w-full p-2 bg-surface-container-lowest border border-outline-variant rounded font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" max="1" min="0" placeholder="0.00 - 1.00" step="0.01" type="number"/>
                        </div>
                    </div>
                    <div class="space-y-xs">
                        <label class="font-label-sm text-label-sm text-on-surface block">Nama Penyakit / Stadium</label>
                        <input name="nama_penyakit" id="edit-nama" required class="w-full p-2 bg-surface-container-lowest border border-outline-variant rounded font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Masukkan nama penyakit" type="text"/>
                    </div>
                    <div class="space-y-xs">
                        <label class="font-label-sm text-label-sm text-on-surface block">Deskripsi Klinis</label>
                        <textarea name="deskripsi" id="edit-deskripsi" class="w-full p-2 bg-surface-container-lowest border border-outline-variant rounded font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none" placeholder="Jelaskan gejala dan kondisi..." rows="3"></textarea>
                    </div>
                    <div class="space-y-xs">
                        <label class="font-label-sm text-label-sm text-on-surface block">Solusi / Rekomendasi Penanganan</label>
                        <textarea name="solusi" id="edit-solusi" class="w-full p-2 bg-surface-container-lowest border border-outline-variant rounded font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none" placeholder="Langkah penanganan yang disarankan..." rows="3"></textarea>
                    </div>
                </div>
                <div class="p-6 border-t border-outline-variant bg-surface-container flex justify-end gap-sm">
                    <button type="button" class="px-4 py-2 border border-outline text-secondary rounded font-label-md text-label-md hover:bg-surface-variant transition-colors h-[40px]" onclick="document.getElementById('modal-edit').classList.add('hidden')">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded font-label-md text-label-md hover:bg-on-primary-fixed-variant transition-colors h-[40px]">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(penyakit) {
            document.getElementById('modal-edit').classList.remove('hidden');
            document.getElementById('edit-id').value = penyakit.id_penyakit;
            document.getElementById('edit-kode').value = penyakit.kode_penyakit;
            document.getElementById('edit-nama').value = penyakit.nama_penyakit;
            document.getElementById('edit-nilai').value = penyakit.nilai_bayes;
            document.getElementById('edit-deskripsi').value = penyakit.deskripsi;
            document.getElementById('edit-solusi').value = penyakit.solusi;
        }
    </script>
</body>
</html>
