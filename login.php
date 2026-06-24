<?php
// login.php
require_once 'config.php';
require_once 'auth.php';

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = "Username dan Password tidak boleh kosong!";
    } else {
        if (login($username, $password, $conn)) {
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Username atau Password salah!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login Admin - ParkinsonExpert</title>
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
                      "DEFAULT": "0.5rem",
                      "lg": "1rem",
                      "xl": "1.5rem",
                      "full": "9999px"
              }
            }
          }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F7F5F2] min-h-screen flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-[450px] rounded-[20px] p-8 shadow-[0_8px_30px_rgb(0,0,0,0.08)] flex flex-col gap-6">
        
        <!-- Header -->
        <div class="text-center flex flex-col gap-2 mb-2">
            <h1 class="text-[26px] font-bold text-primary tracking-tight">ParkinsonExpert</h1>
            <h2 class="text-lg font-medium text-gray-600">Portal Admin</h2>
            <p class="text-sm text-gray-500 mt-1">Masukkan kredensial Anda untuk masuk ke dashboard.</p>
        </div>

        <!-- Alert Error -->
        <?php if (!empty($error)): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm border border-red-100 flex items-center gap-3">
                <span class="material-symbols-outlined text-[20px]">error</span>
                <span class="font-medium"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form class="flex flex-col gap-5" action="login.php" method="POST">
            <!-- Username Input -->
            <div class="flex flex-col gap-2">
                <label class="text-sm font-semibold text-gray-700" for="username">Username</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-[22px]">person</span>
                    <input class="w-full pl-12 pr-4 h-[50px] bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-gray-800" id="username" name="username" placeholder="Masukkan username" required type="text" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"/>
                </div>
            </div>

            <!-- Password Input -->
            <div class="flex flex-col gap-2">
                <label class="text-sm font-semibold text-gray-700" for="password">Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-[22px]">lock</span>
                    <input class="w-full pl-12 pr-4 h-[50px] bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-gray-800" id="password" name="password" placeholder="Masukkan password" required type="password"/>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col gap-4 mt-2">
                <button class="w-full bg-primary text-white h-[52px] rounded-xl font-semibold text-[15px] hover:bg-orange-600 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center" type="submit">
                    Masuk
                </button>
                <a href="index.php" class="text-center text-sm font-medium text-gray-500 hover:text-primary transition-colors mt-2">
                    Kembali ke Beranda
                </a>
            </div>
        </form>
    </div>
</body>
</html>
