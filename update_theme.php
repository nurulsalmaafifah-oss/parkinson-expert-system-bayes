<?php
$dir = __DIR__;
$files = glob($dir . '/*.php');

$newColors = '            "colors": {
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
            },';

foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'tailwind-config') !== false) {
        // Regex to replace the "colors": { ... } block
        $content = preg_replace('/"colors"\s*:\s*\{.*?\}(?=\s*,\s*"borderRadius"|\s*,\s*"spacing"|\s*,\s*"fontFamily")/s', trim($newColors, ','), $content);
        file_put_contents($file, $content);
        echo "Updated colors in: " . basename($file) . "\n";
    }
}
?>
