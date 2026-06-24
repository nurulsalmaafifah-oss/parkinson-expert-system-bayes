<?php
// diagnosa.php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Mulai Diagnosa - ParkinsonExpert</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
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
                      "label-md": [
                              "Inter"
                      ],
                      "body-sm": [
                              "Inter"
                      ],
                      "headline-md": [
                              "Inter"
                      ],
                      "body-lg": [
                              "Inter"
                      ],
                      "body-md": [
                              "Inter"
                      ],
                      "headline-sm": [
                              "Inter"
                      ],
                      "headline-lg": [
                              "Inter"
                      ],
                      "headline-lg-mobile": [
                              "Inter"
                      ],
                      "label-sm": [
                              "Inter"
                      ]
              },
              "fontSize": {
                      "label-md": [
                              "14px",
                              {
                                      "lineHeight": "20px",
                                      "letterSpacing": "0.05em",
                                      "fontWeight": "600"
                              }
                      ],
                      "body-sm": [
                              "14px",
                              {
                                      "lineHeight": "20px",
                                      "fontWeight": "400"
                              }
                      ],
                      "headline-md": [
                              "24px",
                              {
                                      "lineHeight": "32px",
                                      "letterSpacing": "-0.01em",
                                      "fontWeight": "600"
                              }
                      ],
                      "body-lg": [
                              "18px",
                              {
                                      "lineHeight": "28px",
                                      "fontWeight": "400"
                              }
                      ],
                      "body-md": [
                              "16px",
                              {
                                      "lineHeight": "24px",
                                      "fontWeight": "400"
                              }
                      ],
                      "headline-sm": [
                              "20px",
                              {
                                      "lineHeight": "28px",
                                      "fontWeight": "600"
                              }
                      ],
                      "headline-lg": [
                              "32px",
                              {
                                      "lineHeight": "40px",
                                      "letterSpacing": "-0.02em",
                                      "fontWeight": "700"
                              }
                      ],
                      "headline-lg-mobile": [
                              "24px",
                              {
                                      "lineHeight": "32px",
                                      "letterSpacing": "-0.01em",
                                      "fontWeight": "700"
                              }
                      ],
                      "label-sm": [
                              "12px",
                              {
                                      "lineHeight": "16px",
                                      "fontWeight": "500"
                              }
                      ]
              }
          },
          },
        }
      </script>
    <style>
        .keyakinan-slider {
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 6px;
            border-radius: 3px;
            background: #c3c6d7;
            outline: none;
            transition: background 0.2s;
        }
        .keyakinan-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #FF6B1A;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .keyakinan-slider::-webkit-slider-thumb:hover {
            transform: scale(1.15);
            box-shadow: 0 2px 6px rgba(255,107,26,0.35);
        }
        .keyakinan-slider::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #FF6B1A;
            cursor: pointer;
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .keyakinan-slider::-moz-range-track {
            height: 6px;
            border-radius: 3px;
            background: #c3c6d7;
        }
    </style>
</head>
<body class="bg-surface text-on-surface font-body-md antialiased min-h-screen flex flex-col">
    <!-- TopNavBar -->
    <nav class="sticky top-0 z-50 flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop bg-surface dark:bg-inverse-surface h-16 border-b border-outline-variant dark:border-outline">
        <div class="flex items-center gap-md">
            <a href="index.php" class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed tracking-tight">ParkinsonExpert</a>
        </div>
        <div class="hidden md:flex gap-lg">
            <a class="font-label-md text-label-md text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors flex items-center justify-center" href="index.php">
                Home
            </a>
            <a class="font-label-md text-label-md text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors flex items-center justify-center" href="login.php">
                Login Admin
            </a>
        </div>
    </nav>
    
    <!-- Main Content Area -->
    <main class="flex-grow p-margin-mobile md:p-margin-desktop w-full max-w-7xl mx-auto flex gap-gutter">
        <!-- Main Form Container -->
        <div class="flex-grow flex flex-col gap-lg w-full">
            <div class="mb-sm">
                <h1 class="font-headline-lg text-headline-lg text-on-surface mb-xs">Form Diagnosa</h1>
                <p class="font-body-md text-body-md text-on-surface-variant">Silakan isi data diri pasien dan pilih gejala yang dirasakan dengan cermat.</p>
            </div>
            
            <form action="proses_diagnosa.php" class="flex flex-col gap-lg" method="POST">
                <!-- Patient Info Card -->
                <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-md shadow-sm">
                    <h2 class="font-headline-sm text-headline-sm text-on-surface mb-md pb-xs border-b border-outline-variant">Informasi Pasien</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                        <!-- Name Input -->
                        <div class="flex flex-col gap-xs">
                            <label class="font-label-md text-label-md text-on-surface" for="nama_pasien">Nama Pasien</label>
                            <input class="h-10 px-sm border border-outline-variant rounded bg-surface text-on-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors" id="nama_pasien" name="nama_pasien" placeholder="Masukkan nama lengkap" required="" type="text"/>
                        </div>
                        <!-- Age Input -->
                        <div class="flex flex-col gap-xs">
                            <label class="font-label-md text-label-md text-on-surface" for="umur">Umur (Tahun)</label>
                            <input class="h-10 px-sm border border-outline-variant rounded bg-surface text-on-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors" id="umur" max="150" min="0" name="umur" placeholder="Contoh: 65" required="" type="number"/>
                        </div>
                        <!-- Gender Select -->
                        <div class="flex flex-col gap-xs">
                            <label class="font-label-md text-label-md text-on-surface" for="jenis_kelamin">Jenis Kelamin</label>
                            <select class="h-10 px-sm border border-outline-variant rounded bg-surface text-on-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors cursor-pointer" id="jenis_kelamin" name="jenis_kelamin" required="">
                                <option disabled="" selected="" value="">Pilih jenis kelamin</option>
                                <option value="Laki-Laki">Laki-Laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <!-- Address Input -->
                        <div class="flex flex-col gap-xs md:col-span-2">
                            <label class="font-label-md text-label-md text-on-surface" for="alamat">Alamat</label>
                            <textarea class="p-sm border border-outline-variant rounded bg-surface text-on-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors resize-y" id="alamat" name="alamat" placeholder="Masukkan alamat lengkap" required="" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Symptoms Card -->
                <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-md shadow-sm">
                    <div class="flex justify-between items-center mb-md pb-xs border-b border-outline-variant">
                        <div>
                            <h2 class="font-headline-sm text-headline-sm text-on-surface">Gejala yang Dirasakan</h2>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Centang semua gejala yang dialami oleh pasien.</p>
                        </div>
                        <span class="bg-primary-container text-on-primary-container font-label-sm text-label-sm px-sm py-xs rounded-full">Daftar Gejala</span>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-gutter gap-y-sm max-h-[500px] overflow-y-auto pr-xs">
                        <?php
                        $gejala_query = mysqli_query($conn, "SELECT * FROM gejala ORDER BY kode_gejala ASC");
                        if (mysqli_num_rows($gejala_query) > 0):
                            while ($gejala = mysqli_fetch_assoc($gejala_query)):
                        ?>
                            <!-- Symptom Checklist Item -->
                            <div class="flex flex-col p-sm rounded hover:bg-surface-container-low transition-colors group">
                                <label class="flex items-start gap-sm cursor-pointer">
                                    <div class="flex items-center h-6">
                                        <input class="w-4 h-4 text-primary border-outline-variant rounded focus:ring-primary cursor-pointer" name="gejala[]" type="checkbox" value="<?php echo $gejala['id_gejala']; ?>" onchange="toggleKeyakinan(this, <?php echo $gejala['id_gejala']; ?>)"/>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-label-md text-label-md text-on-surface group-hover:text-primary transition-colors"><?php echo $gejala['kode_gejala']; ?></span>
                                        <span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo htmlspecialchars($gejala['nama_gejala']); ?></span>
                                    </div>
                                </label>
                                <!-- Slider Tingkat Keyakinan (hidden by default) -->
                                <div id="keyakinan-<?php echo $gejala['id_gejala']; ?>" class="ml-8 mt-2 hidden">
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="font-label-sm text-label-sm text-secondary">Tingkat Keyakinan:</label>
                                        <span id="keyakinan-badge-<?php echo $gejala['id_gejala']; ?>" class="inline-block bg-primary-fixed text-on-primary-fixed-variant font-label-sm text-label-sm px-2 py-0.5 rounded-full">Pasti</span>
                                    </div>
                                    <input type="range" name="keyakinan[<?php echo $gejala['id_gejala']; ?>]" 
                                           min="0" max="1" step="0.1" value="0.8"
                                           class="keyakinan-slider"
                                           oninput="updateKeyakinanLabel(this, <?php echo $gejala['id_gejala']; ?>)" />
                                    <div class="flex justify-between mt-1">
                                        <span class="font-label-sm text-label-sm text-on-surface-variant">0.0</span>
                                        <span id="keyakinan-value-<?php echo $gejala['id_gejala']; ?>" class="font-label-sm text-label-sm text-primary font-semibold">0.8</span>
                                        <span class="font-label-sm text-label-sm text-on-surface-variant">1.0</span>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <div class="col-span-1 lg:col-span-2 text-center py-md text-on-surface-variant">Tidak ada data gejala tersedia.</div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Action Area -->
                <div class="flex justify-end pt-sm border-t border-outline-variant mt-sm">
                    <button class="h-10 px-md bg-primary text-on-primary font-label-md text-label-md rounded hover:bg-on-primary-fixed-variant transition-colors flex items-center gap-xs shadow-sm" type="submit">
                        <span class="material-symbols-outlined" style="font-size: 18px;">neurology</span>
                        Proses Diagnosa
                    </button>
                </div>
            </form>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="w-full py-lg px-margin-desktop flex flex-col md:flex-row justify-between items-center gap-md bg-surface-container dark:bg-surface-container-high border-t border-outline-variant mt-auto">
        <div class="font-label-md text-label-md font-semibold text-on-surface">
            ParkinsonExpert
        </div>
        <div class="font-body-sm text-body-sm text-secondary dark:text-secondary-fixed">
            © 2026 Neurological Research Institute. All rights reserved.
        </div>
        <div class="flex gap-md">
            <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="#">Privacy Policy</a>
            <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="#">Terms of Service</a>
            <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="#">Contact Support</a>
        </div>
    </footer>
    <script>
    function getKeyakinanLabel(value) {
        if (value <= 0.2) return 'Tidak Pasti';
        if (value <= 0.4) return 'Kurang Pasti';
        if (value <= 0.6) return 'Mungkin';
        if (value <= 0.8) return 'Pasti';
        return 'Sangat Pasti';
    }

    function updateKeyakinanLabel(slider, id) {
        const val = parseFloat(slider.value);
        document.getElementById('keyakinan-value-' + id).textContent = val.toFixed(1);
        document.getElementById('keyakinan-badge-' + id).textContent = getKeyakinanLabel(val);
    }

    function toggleKeyakinan(checkbox, id) {
        const el = document.getElementById('keyakinan-' + id);
        if (checkbox.checked) {
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
            const slider = el.querySelector('input[type="range"]');
            slider.value = 0.8;
            updateKeyakinanLabel(slider, id);
        }
    }
    </script>
</body>
</html>
