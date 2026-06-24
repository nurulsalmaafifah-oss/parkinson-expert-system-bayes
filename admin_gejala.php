<?php
// admin_gejala.php
require_once 'config.php';
require_once 'auth.php';

// Proteksi halaman admin
check_login();

$message = "";
$error = "";

// 1. Proses Delete
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = intval($_GET['id']);
    $delete = mysqli_query($conn, "DELETE FROM gejala WHERE id_gejala = $id");
    if ($delete) {
        $message = "Data gejala berhasil dihapus!";
    } else {
        $error = "Gagal menghapus data gejala: " . mysqli_error($conn);
    }
}

// 2. Proses Tambah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $kode = mysqli_real_escape_string($conn, $_POST['kode_gejala']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_gejala']);
    $nilai = floatval($_POST['nilai_gejala']);

    // Validasi kode unik
    $check = mysqli_query($conn, "SELECT * FROM gejala WHERE kode_gejala = '$kode'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Kode gejala '$kode' sudah terdaftar!";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO gejala (kode_gejala, nama_gejala, nilai_gejala) VALUES ('$kode', '$nama', $nilai)");
        if ($insert) {
            $message = "Gejala baru berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan gejala: " . mysqli_error($conn);
        }
    }
}

// 3. Proses Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = intval($_POST['id_gejala']);
    $kode = mysqli_real_escape_string($conn, $_POST['kode_gejala']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_gejala']);
    $nilai = floatval($_POST['nilai_gejala']);

    // Validasi kode unik selain record ini
    $check = mysqli_query($conn, "SELECT * FROM gejala WHERE kode_gejala = '$kode' AND id_gejala != $id");
    if (mysqli_num_rows($check) > 0) {
        $error = "Kode gejala '$kode' sudah digunakan oleh gejala lain!";
    } else {
        $update = mysqli_query($conn, "UPDATE gejala SET kode_gejala = '$kode', nama_gejala = '$nama', nilai_gejala = $nilai WHERE id_gejala = $id");
        if ($update) {
            $message = "Data gejala berhasil diperbarui!";
        } else {
            $error = "Gagal memperbarui gejala: " . mysqli_error($conn);
        }
    }
}

// 4. Hitung Kode Gejala Otomatis Berikutnya
$max_query = mysqli_query($conn, "SELECT kode_gejala FROM gejala ORDER BY id_gejala DESC LIMIT 1");
if (mysqli_num_rows($max_query) > 0) {
    $last_code = mysqli_fetch_assoc($max_query)['kode_gejala'];
    $num = intval(substr($last_code, 1)) + 1;
    $next_code = "G" . str_pad($num, 2, "0", STR_PAD_LEFT);
} else {
    $next_code = "G01";
}

// 5. Pencarian dan Pagination Setup
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$limit = 10; // Menampilkan 10 data per halaman
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $limit;

$where_clause = "";
if (!empty($search)) {
    $where_clause = "WHERE kode_gejala LIKE '%$search%' OR nama_gejala LIKE '%$search%'";
}

// Hitung total records untuk pagination
$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM gejala $where_clause");
$total_records = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_records / $limit);

// Query data gejala
$gejala_query = mysqli_query($conn, "SELECT * FROM gejala $where_clause ORDER BY kode_gejala ASC LIMIT $start, $limit");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Data Gejala - ParkinsonExpert</title>
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
            <!-- Active Tab: Symptom Data -->
            <a class="flex items-center gap-sm bg-primary-container text-on-primary-container border-l-4 border-primary px-4 py-3 scale-95 transition-transform" href="admin_gejala.php">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">format_list_bulleted</span>
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
                <h1 class="font-headline-lg text-headline-lg text-on-surface">Data Gejala</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">Kelola daftar gejala Parkinson untuk basis pengetahuan sistem pakar.</p>
            </div>
            
            <div class="flex items-center gap-sm w-full sm:w-auto">
                <!-- Search Form -->
                <form method="GET" action="admin_gejala.php" class="relative w-full sm:w-64">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                    <input class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded font-body-sm text-body-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" name="search" placeholder="Cari gejala..." type="text" value="<?php echo htmlspecialchars($search); ?>"/>
                </form>
                
                <button class="flex items-center justify-center gap-xs bg-primary text-on-primary px-4 py-2 h-[40px] rounded font-label-md text-label-md hover:bg-on-primary-fixed-variant transition-colors whitespace-nowrap shadow-sm" onclick="document.getElementById('modal-tambah').classList.remove('hidden')">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Tambah Gejala
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

        <!-- Data Table Card -->
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant shadow-sm overflow-hidden mb-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="bg-primary text-on-primary border-b border-primary">
                            <th class="py-3 px-4 font-label-md text-label-md text-on-primary w-16">No</th>
                            <th class="py-3 px-4 font-label-md text-label-md text-on-primary w-32">Kode Gejala</th>
                            <th class="py-3 px-4 font-label-md text-label-md text-on-primary">Nama Gejala</th>
                            <th class="py-3 px-4 font-label-md text-label-md text-on-primary w-32 text-right">Nilai Gejala</th>
                            <th class="py-3 px-4 font-label-md text-label-md text-on-primary w-24 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="font-body-md text-body-md">
                        <?php if (mysqli_num_rows($gejala_query) > 0): 
                            $no = $start + 1;
                            while ($row = mysqli_fetch_assoc($gejala_query)): 
                        ?>
                            <tr class="border-b border-outline-variant hover:bg-surface-container-low transition-colors group">
                                <td class="py-3 px-4 text-on-surface-variant"><?php echo $no++; ?></td>
                                <td class="py-3 px-4 font-semibold text-primary"><?php echo htmlspecialchars($row['kode_gejala']); ?></td>
                                <td class="py-3 px-4 text-on-surface"><?php echo htmlspecialchars($row['nama_gejala']); ?></td>
                                <td class="py-3 px-4 text-right font-mono text-on-surface-variant"><?php echo number_format($row['nilai_gejala'], 2); ?></td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex justify-center gap-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button class="text-secondary hover:text-primary transition-colors p-1" title="Edit"
                                                onclick='openEditModal(<?php echo json_encode($row); ?>)'>
                                            <span class="material-symbols-outlined" style="font-size: 20px;">edit</span>
                                        </button>
                                        <a href="admin_gejala.php?action=delete&id=<?php echo $row['id_gejala']; ?>" 
                                           class="text-secondary hover:text-error transition-colors p-1" title="Hapus"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus gejala <?php echo htmlspecialchars($row['kode_gejala']); ?>? Penghapusan ini juga menghapus aturan relasi yang terkait.')">
                                            <span class="material-symbols-outlined" style="font-size: 20px;">delete</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="py-6 text-center text-on-surface-variant">Tidak ada data gejala ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="border-t border-outline-variant p-4 flex items-center justify-between bg-surface-container-lowest">
                <span class="font-body-sm text-body-sm text-on-surface-variant">
                    Menampilkan <?php echo $total_records > 0 ? $start + 1 : 0; ?>-<?php echo min($start + $limit, $total_records); ?> dari <?php echo $total_records; ?> gejala
                </span>
                <?php if ($total_pages > 1): ?>
                    <div class="flex gap-2">
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" 
                           class="w-8 h-8 rounded border border-outline-variant flex items-center justify-center text-on-surface-variant hover:bg-surface-container transition-colors <?php echo $page <= 1 ? 'pointer-events-none opacity-50' : ''; ?>">
                            <span class="material-symbols-outlined" style="font-size: 20px;">chevron_left</span>
                        </a>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                               class="w-8 h-8 rounded font-label-sm flex items-center justify-center <?php echo $page == $i ? 'bg-primary text-on-primary' : 'border border-outline-variant hover:bg-surface-container transition-colors'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" 
                           class="w-8 h-8 rounded border border-outline-variant flex items-center justify-center text-on-surface-variant hover:bg-surface-container transition-colors <?php echo $page >= $total_pages ? 'pointer-events-none opacity-50' : ''; ?>">
                            <span class="material-symbols-outlined" style="font-size: 20px;">chevron_right</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Modal: Tambah Gejala -->
    <div class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-on-surface/50 backdrop-blur-sm" id="modal-tambah">
        <div class="bg-surface-container-lowest w-full max-w-md rounded-lg shadow-xl border border-outline-variant relative flex flex-col max-h-[90%] overflow-hidden animate-[fadeIn_0.2s_ease-out]">
            <div class="p-6 border-b border-outline-variant flex justify-between items-center">
                <h3 class="font-headline-sm text-headline-sm text-on-surface">Tambah Data Gejala</h3>
                <button class="text-on-surface-variant hover:text-on-surface transition-colors" onclick="document.getElementById('modal-tambah').classList.add('hidden')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="admin_gejala.php" method="POST" class="flex flex-col overflow-hidden">
                <input type="hidden" name="action" value="tambah">
                <div class="p-6 flex flex-col gap-4 overflow-y-auto">
                    <div>
                        <label class="block font-label-md text-label-md text-on-surface mb-xs" for="kode_gejala">Kode Gejala</label>
                        <input class="w-full h-10 px-3 rounded border border-outline-variant bg-surface-container text-on-surface-variant focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-body-md text-body-md font-semibold" id="kode_gejala" name="kode_gejala" readonly type="text" value="<?php echo $next_code; ?>"/>
                        <p class="font-body-sm text-body-sm text-secondary mt-1 text-xs">Kode dibuat otomatis untuk kontinuitas data.</p>
                    </div>
                    <div>
                        <label class="block font-label-md text-label-md text-on-surface mb-xs" for="nama_gejala">Nama Gejala</label>
                        <textarea class="w-full p-3 rounded border border-outline-variant bg-surface-container-lowest text-on-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-body-md text-body-md resize-none" id="nama_gejala" name="nama_gejala" placeholder="Masukkan deskripsi gejala..." rows="3" required></textarea>
                    </div>
                    <div>
                        <label class="block font-label-md text-label-md text-on-surface mb-xs" for="nilai_gejala">Bobot Gejala</label>
                        <input class="w-full h-10 px-3 rounded border border-outline-variant bg-surface-container-lowest text-on-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-body-md text-body-md" id="nilai_gejala" max="1" min="0" name="nilai_gejala" placeholder="0.0 - 1.0" step="0.01" type="number" required/>
                    </div>
                </div>
                <div class="p-6 border-t border-outline-variant bg-surface-container-low flex justify-end gap-3">
                    <button class="h-10 px-4 rounded border border-outline text-secondary font-label-md text-label-md hover:bg-surface-container transition-colors" onclick="document.getElementById('modal-tambah').classList.add('hidden')" type="button">Batal</button>
                    <button class="h-10 px-6 rounded bg-primary text-on-primary font-label-md text-label-md hover:bg-on-primary-fixed-variant transition-colors" type="submit">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Gejala -->
    <div class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-on-surface/50 backdrop-blur-sm" id="modal-edit">
        <div class="bg-surface-container-lowest w-full max-w-md rounded-lg shadow-xl border border-outline-variant relative flex flex-col max-h-[90%] overflow-hidden animate-[fadeIn_0.2s_ease-out]">
            <div class="p-6 border-b border-outline-variant flex justify-between items-center">
                <h3 class="font-headline-sm text-headline-sm text-on-surface">Ubah Data Gejala</h3>
                <button class="text-on-surface-variant hover:text-on-surface transition-colors" onclick="document.getElementById('modal-edit').classList.add('hidden')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form action="admin_gejala.php" method="POST" class="flex flex-col overflow-hidden">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_gejala" id="edit-id">
                <div class="p-6 flex flex-col gap-4 overflow-y-auto">
                    <div>
                        <label class="block font-label-md text-label-md text-on-surface mb-xs" for="edit-kode">Kode Gejala</label>
                        <input class="w-full h-10 px-3 rounded border border-outline-variant bg-surface-container text-on-surface-variant focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-body-md text-body-md font-semibold" id="edit-kode" name="kode_gejala" required type="text"/>
                    </div>
                    <div>
                        <label class="block font-label-md text-label-md text-on-surface mb-xs" for="edit-nama">Nama Gejala</label>
                        <textarea class="w-full p-3 rounded border border-outline-variant bg-surface-container-lowest text-on-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-body-md text-body-md resize-none" id="edit-nama" name="nama_gejala" placeholder="Masukkan deskripsi gejala..." rows="3" required></textarea>
                    </div>
                    <div>
                        <label class="block font-label-md text-label-md text-on-surface mb-xs" for="edit-nilai">Nilai Gejala (Bobot CF)</label>
                        <input class="w-full h-10 px-3 rounded border border-outline-variant bg-surface-container-lowest text-on-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-body-md text-body-md" id="edit-nilai" max="1" min="0" name="nilai_gejala" placeholder="0.0 - 1.0" step="0.01" type="number" required/>
                    </div>
                </div>
                <div class="p-6 border-t border-outline-variant bg-surface-container-low flex justify-end gap-3">
                    <button class="h-10 px-4 rounded border border-outline text-secondary font-label-md text-label-md hover:bg-surface-container transition-colors" onclick="document.getElementById('modal-edit').classList.add('hidden')" type="button">Batal</button>
                    <button class="h-10 px-6 rounded bg-primary text-on-primary font-label-md text-label-md hover:bg-on-primary-fixed-variant transition-colors" type="submit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(gejala) {
            document.getElementById('modal-edit').classList.remove('hidden');
            document.getElementById('edit-id').value = gejala.id_gejala;
            document.getElementById('edit-kode').value = gejala.kode_gejala;
            document.getElementById('edit-nama').value = gejala.nama_gejala;
            document.getElementById('edit-nilai').value = gejala.nilai_gejala;
        }
    </script>
</body>
</html>
