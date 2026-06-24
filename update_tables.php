<?php
$dir = __DIR__;
$files = glob($dir . '/*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, '<table') !== false) {
        // Change the <thead> or <tr> in thead to have bg-primary
        // And change text-on-primary / text-on-primary in <th> to text-on-primary
        
        $lines = explode("\n", $content);
        $in_thead = false;
        
        for ($i = 0; $i < count($lines); $i++) {
            if (strpos($lines[$i], '<thead') !== false) {
                $in_thead = true;
            }
            if ($in_thead) {
                // If it's a <tr> inside thead, remove bg-primary text-on-primary
                $lines[$i] = str_replace('bg-primary text-on-primary', 'bg-primary text-on-primary', $lines[$i]);
                // Change text colors in <th>
                $lines[$i] = str_replace('text-on-primary', 'text-on-primary', $lines[$i]);
                $lines[$i] = str_replace('text-on-primary', 'text-on-primary', $lines[$i]);
                // Remove border-outline-variant for clean orange look if it's there
                if (strpos($lines[$i], '<tr') !== false) {
                    $lines[$i] = str_replace('border-outline-variant', 'border-primary', $lines[$i]);
                    // If it doesn't have bg-primary yet, let's add it
                    if (strpos($lines[$i], 'bg-primary') === false) {
                        $lines[$i] = str_replace('class="', 'class="bg-primary text-on-primary ', $lines[$i]);
                    }
                }
            }
            if (strpos($lines[$i], '</thead') !== false) {
                $in_thead = false;
            }
        }
        
        file_put_contents($file, implode("\n", $lines));
        echo "Updated table headers in: " . basename($file) . "\n";
    }
}
?>
