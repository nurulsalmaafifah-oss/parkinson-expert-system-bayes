<?php
// admin_basis.php
require_once 'config.php';
require_once 'auth.php';

// Proteksi halaman admin
check_login();

$message = "";
$error = "";

// 1. Proses Delete Rule
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = intval($_GET['id']);
    $delete = mysqli_query($conn, "DELETE FROM rule_bayes WHERE id_rule = $id");
    if ($delete) {
        $message = "Aturan basis pengetahuan berhasil dihapus!";
    } else {
        $error = "Gagal menghapus aturan: " . mysqli_error($conn);
    }
}

// 2. Proses Tambah Rule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $id_penyakit = intval($_POST['id_penyakit']);
    $id_gejala = intval($_POST['id_gejala']);
    $bobot = floatval($_POST['bobot']);

    // Cek apakah relasi penyakit & gejala tersebut sudah ada
    $check = mysqli_query($conn, "SELECT * FROM rule_bayes WHERE id_penyakit = $id_penyakit AND id_gejala = $id_gejala");
    if (mysqli_num_rows($check) > 0) {
        $error = "Aturan untuk penyakit dan gejala tersebut sudah ada! Silakan edit aturan yang sudah ada jika ingin mengubah bobot.";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO rule_bayes (id_penyakit, id_gejala, bobot) VALUES ($id_penyakit, $id_gejala, $bobot)");
        if ($insert) {
            $message = "Aturan baru berhasil disimpan!";
        } else {
            $error = "Gagal menyimpan aturan: " . mysqli_error($conn);
        }
    }
}

// 3. Proses Edit Rule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id_rule = intval($_POST['id_rule']);
    $id_penyakit = intval($_POST['id_penyakit']);
    $id_gejala = intval($_POST['id_gejala']);
    $bobot = floatval($_POST['bobot']);

    // Cek duplikasi jika dipindahkan ke penyakit/gejala lain
    $check = mysqli_query($conn, "SELECT * FROM rule_bayes WHERE id_penyakit = $id_penyakit AND id_gejala = $id_gejala AND id_rule != $id_rule");
    if (mysqli_num_rows($check) > 0) {
        $error = "Aturan untuk penyakit dan gejala tersebut sudah ada di data lain!";
    } else {
        $update = mysqli_query($conn, "UPDATE rule_bayes SET id_penyakit = $id_penyakit, id_gejala = $id_gejala, bobot = $bobot WHERE id_rule = $id_rule");
        if ($update) {
            $message = "Aturan berhasil diperbarui!";
        } else {
            $error = "Gagal memperbarui aturan: " . mysqli_error($conn);
        }
    }
}

// 4. Pencarian dan Pagination
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $limit;

$where_clause = "";
if (!empty($search)) {
    $where_clause = "WHERE p.nama_penyakit LIKE '%$search%' OR g.nama_gejala LIKE '%$search%' OR p.kode_penyakit LIKE '%$search%' OR g.kode_gejala LIKE '%$search%'";
}

// Total records
$total_query = mysqli_query($conn, "
    SELECT COUNT(*) as total 
    FROM rule_bayes r
    JOIN penyakit p ON r.id_penyakit = p.id_penyakit
    JOIN gejala g ON r.id_gejala = g.id_gejala
    $where_clause
");
$total_records = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_records / $limit);

// Query data rule
$rule_query = mysqli_query($conn, "
    SELECT r.*, p.nama_penyakit, p.kode_penyakit, g.nama_gejala, g.kode_gejala 
    FROM rule_bayes r
    JOIN penyakit p ON r.id_penyakit = p.id_penyakit
    JOIN gejala g ON r.id_gejala = g.id_gejala
    $where_clause
    ORDER BY p.kode_penyakit ASC, g.kode_gejala ASC
    LIMIT $start, $limit
");

// Query penyakit untuk select option
$penyakit_list = [];
$penyakit_q = mysqli_query($conn, "SELECT id_penyakit, kode_penyakit, nama_penyakit FROM penyakit ORDER BY kode_penyakit ASC");
while ($row = mysqli_fetch_assoc($penyakit_q)) {
    $penyakit_list[] = $row;
}

// Query gejala untuk select option
$gejala_list = [];
$gejala_q = mysqli_query($conn, "SELECT id_gejala, kode_gejala, nama_gejala FROM gejala ORDER BY kode_gejala ASC");
while ($row = mysqli_fetch_assoc($gejala_q)) {
    $gejala_list[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Basis Pengetahuan - ParkinsonExpert</title>
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
            <a class="flex items-center gap-sm text-surface-variant px-4 py-3 hover:bg-on-secondary-fixed-variant transition-all" href="admin_penyakit.php">
                <span class="material-symbols-outlined">neurology</span>
                Data Penyakit
            </a>
            <a class="flex items-center gap-sm text-surface-variant px-4 py-3 hover:bg-on-secondary-fixed-variant transition-all" href="admin_gejala.php">
                <span class="material-symbols-outlined">format_list_bulleted</span>
                Data Gejala
            </a>
            <!-- Active Tab: Knowledge Base -->
            <a class="flex items-center gap-sm bg-primary-container text-on-primary-container border-l-4 border-primary px-4 py-3 scale-95 transition-transform" href="admin_basis.php">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">psychology</span>
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
                <h1 class="font-headline-lg text-headline-lg text-on-surface">Basis Pengetahuan</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">Kelola relasi penyakit, gejala, dan bobot probabilitas Teorema Bayes.</p>
            </div>
            
            <div class="flex items-center gap-sm w-full sm:w-auto">
                <!-- Search Form -->
                <form method="GET" action="admin_basis.php" class="relative w-full sm:w-64">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                    <input class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded font-body-sm text-body-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" name="search" placeholder="Cari penyakit/gejala..." type="text" value="<?php echo htmlspecialchars($search); ?>"/>
                </form>
                
                <button class="flex items-center justify-center gap-xs bg-primary text-on-primary px-4 py-2 h-[40px] rounded font-label-md text-label-md hover:bg-on-primary-fixed-variant transition-colors whitespace-nowrap shadow-sm" onclick="document.getElementById('modal-tambah').classList.remove('hidden')">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Tambah Aturan
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
                <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead>
                        <tr class="border-b border-primary bg-primary text-on-primary">
                            <th class="py-3 px-4 font-label-md text-label-md text-on-primary w-24">ID Aturan</th>
                            <th class="py-3 px-4 font-label-md text-label-md text-on-primary">Penyakit</th>
                            <th class="py-3 px-4 font-label-md text-label-md text-on-primary">Gejala</th>
                            <th class="py-3 px-4 font-label-md text-label-md text-on-primary w-28">Bobot Bayes</th>
                            <th class="py-3 px-4 font-label-md text-label-md text-on-primary w-24 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="font-body-sm text-body-sm text-on-surface">
                        <?php if (mysqli_num_rows($rule_query) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($rule_query)): ?>
                                <tr class="border-b border-outline-variant hover:bg-surface-container-low transition-colors group">
                                    <td class="py-3 px-4 font-mono font-semibold text-secondary">R<?php echo str_pad($row['id_rule'], 3, "0", STR_PAD_LEFT); ?></td>
                                    <td class="py-3 px-4 font-medium">[<?php echo $row['kode_penyakit']; ?>] <?php echo htmlspecialchars($row['nama_penyakit']); ?></td>
                                    <td class="py-3 px-4 text-on-surface-variant">[<?php echo $row['kode_gejala']; ?>] <?php echo htmlspecialchars($row['nama_gejala']); ?></td>
                                    <td class="py-3 px-4">
                                        <span class="bg-primary-container text-on-primary-container px-2 py-1 rounded text-xs font-semibold"><?php echo number_format($row['bobot'], 2); ?></span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="flex justify-center gap-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button class="p-1 text-secondary hover:text-primary transition-colors" title="Edit" 
                                                    onclick='openEditModal(<?php echo json_encode($row); ?>)'>
                                                <span class="material-symbols-outlined text-[20px]">edit</span>
                                            </button>
                                            <a href="admin_basis.php?action=delete&id=<?php echo $row['id_rule']; ?>" 
                                               class="p-1 text-secondary hover:text-error transition-colors" title="Hapus" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus aturan R<?php echo str_pad($row['id_rule'], 3, '0', STR_PAD_LEFT); ?>?')">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="py-6 text-center text-on-surface-variant">Tidak ada data aturan basis pengetahuan ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="border-t border-outline-variant p-4 flex items-center justify-between bg-surface-container-lowest">
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Menampilkan <?php echo $total_records > 0 ? $start + 1 : 0; ?> hingga <?php echo min($start + $limit, $total_records); ?> dari <?php echo $total_records; ?> aturan
                </p>
                <?php if ($total_pages > 1): ?>
                    <div class="flex gap-2">
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
                        
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" 
                           class="px-3 py-1 border border-outline-variant rounded hover:bg-surface-container-low font-label-sm text-label-sm flex items-center <?php echo $page >= $total_pages ? 'pointer-events-none opacity-50' : ''; ?>">
                            Selanjutnya
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Modal Tambah Aturan -->
    <div class="fixed inset-0 z-50 hidden bg-on-surface/50 backdrop-blur-sm flex items-center justify-center p-4" id="modal-tambah">
        <div class="bg-surface-container-lowest w-full max-w-lg rounded-xl shadow-lg border border-outline-variant flex flex-col overflow-hidden animate-[fadeIn_0.2s_ease-out]">
            <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-surface">
                <h3 class="font-headline-sm text-headline-sm text-on-surface">Tambah Aturan Baru</h3>
                <button class="text-secondary hover:text-on-surface transition-colors p-1 rounded-full" onclick="document.getElementById('modal-tambah').classList.add('hidden')">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <form action="admin_basis.php" method="POST" class="flex flex-col">
                <input type="hidden" name="action" value="tambah">
                <div class="p-6 flex flex-col gap-4">
                    
                    <div class="flex flex-col gap-xs">
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Penyakit (Disease)</label>
                        <div class="relative">
                            <select name="id_penyakit" required class="w-full appearance-none bg-surface-container-lowest border border-outline-variant rounded h-10 px-3 pr-10 font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all cursor-pointer">
                                <option disabled selected value="">Pilih penyakit...</option>
                                <?php foreach ($penyakit_list as $p): ?>
                                    <option value="<?php echo $p['id_penyakit']; ?>">[<?php echo $p['kode_penyakit']; ?>] <?php echo htmlspecialchars($p['nama_penyakit']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none">expand_more</span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col gap-xs">
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Gejala (Symptom)</label>
                        <div class="relative">
                            <select name="id_gejala" required class="w-full appearance-none bg-surface-container-lowest border border-outline-variant rounded h-10 px-3 pr-10 font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all cursor-pointer">
                                <option disabled selected value="">Pilih gejala...</option>
                                <?php foreach ($gejala_list as $g): ?>
                                    <option value="<?php echo $g['id_gejala']; ?>">[<?php echo $g['kode_gejala']; ?>] <?php echo htmlspecialchars($g['nama_gejala']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none">expand_more</span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col gap-xs">
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Bobot Bayes (P(E|H))</label>
                        <div class="flex items-center gap-sm">
                            <input name="bobot" required class="w-full bg-surface-container-lowest border border-outline-variant rounded h-10 px-3 font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" max="1" min="0" step="0.01" type="number" value="0.50"/>
                            <span class="font-body-sm text-body-sm text-on-surface-variant whitespace-nowrap">0.00 s.d 1.00</span>
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t border-outline-variant bg-surface flex justify-end gap-sm">
                    <button type="button" class="px-4 h-10 rounded border border-outline text-secondary font-label-md text-label-md hover:bg-surface-container-high transition-colors" onclick="document.getElementById('modal-tambah').classList.add('hidden')">Batal</button>
                    <button type="submit" class="px-6 h-10 rounded bg-primary text-on-primary font-label-md text-label-md hover:bg-on-primary-fixed-variant transition-colors shadow-sm">Simpan Aturan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Aturan -->
    <div class="fixed inset-0 z-50 hidden bg-on-surface/50 backdrop-blur-sm flex items-center justify-center p-4" id="modal-edit">
        <div class="bg-surface-container-lowest w-full max-w-lg rounded-xl shadow-lg border border-outline-variant flex flex-col overflow-hidden animate-[fadeIn_0.2s_ease-out]">
            <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-surface">
                <h3 class="font-headline-sm text-headline-sm text-on-surface">Ubah Aturan</h3>
                <button class="text-secondary hover:text-on-surface transition-colors p-1 rounded-full" onclick="document.getElementById('modal-edit').classList.add('hidden')">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <form action="admin_basis.php" method="POST" class="flex flex-col">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_rule" id="edit-id">
                <div class="p-6 flex flex-col gap-4">
                    
                    <div class="flex flex-col gap-xs">
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Penyakit (Disease)</label>
                        <div class="relative">
                            <select name="id_penyakit" id="edit-penyakit" required class="w-full appearance-none bg-surface-container-lowest border border-outline-variant rounded h-10 px-3 pr-10 font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all cursor-pointer">
                                <?php foreach ($penyakit_list as $p): ?>
                                    <option value="<?php echo $p['id_penyakit']; ?>">[<?php echo $p['kode_penyakit']; ?>] <?php echo htmlspecialchars($p['nama_penyakit']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none">expand_more</span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col gap-xs">
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Gejala (Symptom)</label>
                        <div class="relative">
                            <select name="id_gejala" id="edit-gejala" required class="w-full appearance-none bg-surface-container-lowest border border-outline-variant rounded h-10 px-3 pr-10 font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all cursor-pointer">
                                <?php foreach ($gejala_list as $g): ?>
                                    <option value="<?php echo $g['id_gejala']; ?>">[<?php echo $g['kode_gejala']; ?>] <?php echo htmlspecialchars($g['nama_gejala']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none">expand_more</span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col gap-xs">
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Bobot Bayes (P(E|H))</label>
                        <div class="flex items-center gap-sm">
                            <input name="bobot" id="edit-bobot" required class="w-full bg-surface-container-lowest border border-outline-variant rounded h-10 px-3 font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" max="1" min="0" step="0.01" type="number"/>
                            <span class="font-body-sm text-body-sm text-on-surface-variant whitespace-nowrap">0.00 s.d 1.00</span>
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t border-outline-variant bg-surface flex justify-end gap-sm">
                    <button type="button" class="px-4 h-10 rounded border border-outline text-secondary font-label-md text-label-md hover:bg-surface-container-high transition-colors" onclick="document.getElementById('modal-edit').classList.add('hidden')">Batal</button>
                    <button type="submit" class="px-6 h-10 rounded bg-primary text-on-primary font-label-md text-label-md hover:bg-on-primary-fixed-variant transition-colors shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(rule) {
            document.getElementById('modal-edit').classList.remove('hidden');
            document.getElementById('edit-id').value = rule.id_rule;
            document.getElementById('edit-penyakit').value = rule.id_penyakit;
            document.getElementById('edit-gejala').value = rule.id_gejala;
            document.getElementById('edit-bobot').value = rule.bobot;
        }
    </script>
</body>
</html>
