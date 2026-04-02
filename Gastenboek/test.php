<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Case Sensitivity Report</h1>";

function checkDirectory($dir) {
    if (!is_dir($dir)) {
        echo "<p style='color:red'>Directory not found: $dir</p>";
        return;
    }
    
    echo "<h2>Files in $dir:</h2><ul>";
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                echo "<li><strong>Directory:</strong> $file</li>";
            } else {
                echo "<li><strong>File:</strong> $file</li>";
            }
        }
    }
    echo "</ul>";
}

// Check key directories
checkDirectory(__DIR__ . '/classes');
checkDirectory(__DIR__ . '/includes');
checkDirectory(__DIR__ . '/view');
checkDirectory(__DIR__ . '/uploads');

// Check if files exist with various case combinations
function checkFileVariants($basename, $dir) {
    echo "<h2>Checking for case variants of $basename in $dir:</h2>";
    
    $variants = [
        strtolower($basename),
        strtoupper($basename),
        ucfirst(strtolower($basename))
    ];
    
    foreach ($variants as $variant) {
        $path = $dir . '/' . $variant;
        if (file_exists($path)) {
            echo "<p style='color:green'>Found: $variant</p>";
        } else {
            echo "<p style='color:red'>Not found: $variant</p>";
        }
    }
}

// Check important files
checkFileVariants('database.php', __DIR__ . '/classes');
checkFileVariants('imagehandler.php', __DIR__ . '/classes');
checkFileVariants('message.php', __DIR__ . '/classes');
checkFileVariants('user.php', __DIR__ . '/classes');
checkFileVariants('bootstrap.php', __DIR__ . '/includes');
?>