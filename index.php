<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Sistem Pakar Deteksi Dini Penyakit Parkinson</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
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
    </style>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
    <!-- TopNavBar -->
    <nav class="sticky top-0 z-50 flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop bg-surface dark:bg-inverse-surface h-16 border-b border-outline-variant dark:border-outline">
        <div class="flex items-center gap-md">
            <span class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed">ParkinsonExpert</span>
        </div>
        <div class="hidden md:flex items-center gap-lg">
            <a class="text-primary dark:text-primary-fixed border-b-2 border-primary pb-1 font-label-md text-label-md opacity-80 transition-all" href="index.php">Home</a>
            <a class="text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors font-label-md text-label-md" href="#info">Info</a>
            <a class="text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors font-label-md text-label-md" href="login.php">Login Admin</a>
        </div>
        <div class="md:hidden flex items-center">
            <button class="text-on-surface-variant" onclick="toggleMobileMenu()">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0;">menu</span>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-surface border-b border-outline-variant px-margin-mobile py-md flex flex-col gap-sm">
        <a class="text-primary font-label-md text-label-md" href="index.php">Home</a>
        <a class="text-on-surface-variant hover:text-primary font-label-md text-label-md" href="#info">Info</a>
        <a class="text-on-surface-variant hover:text-primary font-label-md text-label-md" href="login.php">Login Admin</a>
    </div>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col items-center justify-center p-margin-mobile md:p-margin-desktop">
        <!-- Hero Section -->
        <section class="max-w-4xl mx-auto text-center mt-xl mb-xl">
            <h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface mb-md">
                Sistem Pakar Deteksi Dini Penyakit Parkinson Menggunakan Metode Teorema Bayes
            </h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant mb-xl max-w-2xl mx-auto">
                Platform analisis neurologis mutakhir untuk membantu identifikasi awal gejala Parkinson. Pendekatan berbasis data untuk presisi klinis yang lebih baik.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-md">
                <a href="diagnosa.php" class="bg-primary text-on-primary font-label-md text-label-md h-10 px-lg rounded flex items-center justify-center hover:opacity-90 transition-opacity shadow-sm">
                    Mulai Diagnosa
                </a>
                <a href="#info" class="border border-outline text-primary font-label-md text-label-md h-10 px-lg rounded flex items-center justify-center hover:bg-surface-container transition-colors">
                    Pelajari Lebih Lanjut
                </a>
            </div>
        </section>

        <!-- Bento Grid Info Section -->
        <section id="info" class="max-w-6xl mx-auto w-full grid grid-cols-1 md:grid-cols-3 gap-gutter mb-xl">
            <div class="md:col-span-2 bg-surface-container-lowest rounded-lg border border-outline-variant p-md shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-sm mb-sm text-primary">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">neurology</span>
                        <h2 class="font-headline-sm text-headline-sm">Apa itu Penyakit Parkinson?</h2>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant">
                        Penyakit Parkinson adalah gangguan neurodegeneratif progresif yang memengaruhi sistem saraf dan bagian tubuh yang dikendalikan oleh saraf. Gejala muncul perlahan, seringkali dimulai dengan tremor yang hampir tidak terlihat pada satu tangan. Seiring waktu, kondisi ini dapat menyebabkan kekakuan atau perlambatan gerakan.
                    </p>
                </div>
            </div>
            <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-md shadow-sm">
                <div class="flex items-center gap-sm mb-sm text-secondary">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">biotech</span>
                    <h3 class="font-label-md text-label-md">Metode Teorema Bayes</h3>
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Sistem ini menggunakan probabilitas statistik berdasarkan data klinis untuk memprediksi kemungkinan keberadaan penyakit berdasarkan gejala yang diamati, memberikan wawasan analitis awal.
                </p>
            </div>
            <div class="bg-surface-container-lowest rounded-lg border border-outline-variant p-md shadow-sm">
                <div class="flex items-center gap-sm mb-sm text-secondary">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">format_list_bulleted</span>
                    <h3 class="font-label-md text-label-md">Evaluasi Gejala</h3>
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Penilaian komprehensif terhadap tremor, bradikinesia, rigiditas, dan ketidakstabilan postural untuk membangun profil klinis.
                </p>
            </div>
            <div class="md:col-span-2 bg-primary-container text-on-primary-container rounded-lg p-md shadow-sm flex flex-col justify-center items-start relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="font-headline-sm text-headline-sm mb-sm">Pentingnya Deteksi Dini</h2>
                    <p class="font-body-md text-body-md max-w-xl">
                        Intervensi awal sangat penting. Meskipun tidak ada obatnya, pengobatan dini dapat secara signifikan mengelola gejala dan mempertahankan kualitas hidup untuk waktu yang lebih lama.
                    </p>
                </div>
                <span class="material-symbols-outlined absolute -right-4 -bottom-8 text-[120px] opacity-10" style="font-variation-settings: 'FILL' 1;">psychology</span>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="w-full py-lg px-margin-mobile md:px-margin-desktop flex flex-col md:flex-row justify-between items-center gap-md bg-surface-container dark:bg-surface-container-high border-t border-outline-variant">
        <span class="font-label-md text-label-md font-semibold text-on-surface">© 2026 Neurological Research Institute. All rights reserved.</span>
        <div class="flex flex-wrap gap-md justify-center">
            <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="#">Privacy Policy</a>
            <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="#">Terms of Service</a>
            <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="#">Contact Support</a>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>
