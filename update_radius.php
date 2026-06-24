<?php
$dir = __DIR__;
$files = glob($dir . '/*.php');

$newBorderRadius = '            "borderRadius": {
                    "DEFAULT": "12px",
                    "lg": "16px",
                    "xl": "20px",
                    "full": "9999px"
            },';

foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'tailwind-config') !== false) {
        $content = preg_replace('/"borderRadius"\s*:\s*\{.*?\}(?=\s*,\s*"spacing")/s', trim($newBorderRadius, ','), $content);
        file_put_contents($file, $content);
        echo "Updated borderRadius in: " . basename($file) . "\n";
    }
}
?>
