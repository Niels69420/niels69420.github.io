<?php
// Fix bootstrap.php in the includes directory
function autoload($className) {
    // Get the calling script's directory
    $callerPath = debug_backtrace()[0]['file'];
    $callerDir = dirname($callerPath);
    
    // Determine if we're in the root directory or a subdirectory
    $inSubdirectory = (strpos($callerDir, 'view') !== false);
    
    // Set the correct path to classes based on caller location
    if ($inSubdirectory) {
        $classesPath = "../classes/";  // For files in view/ directory
    } else {
        $classesPath = "classes/";     // For files in root directory
    }
    
    // Try multiple paths with different case variations
    $paths = [
        $classesPath . $className . ".php",
        $classesPath . strtolower($className) . ".php",
        $classesPath . ucfirst(strtolower($className)) . ".php"
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return true;
        }
    }
    
    // For debugging
    error_log("Failed to autoload class: $className. Tried paths: " . implode(", ", $paths));
    return false;
}

// Register the autoloader
spl_autoload_register('autoload');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>