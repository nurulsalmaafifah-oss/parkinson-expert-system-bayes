<?php
$dir = __DIR__;
$files = ['diagnosa.php', 'export_excel.php', 'export_pdf.php', 'hasil.php'];

foreach ($files as $file) {
    $path = $dir . '/' . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        // Replace hex
        $content = str_replace('#004ac6', '#FF6B1A', $content);
        // Replace rgba for hover shadow in diagnosa.php
        $content = str_replace('rgba(0,74,198', 'rgba(255,107,26', $content);
        // Replace secondary chart/circle colors in hasil.php
        if ($file === 'hasil.php') {
            $content = str_replace('#e6e8ea', '#E8DDD3', $content);
        }
        
        file_put_contents($path, $content);
        echo "Replaced hardcoded colors in: " . $file . "\n";
    }
}
?>
