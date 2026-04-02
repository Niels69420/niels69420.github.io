<?php
/**
 * PSR-4 compliant autoloader
 */
spl_autoload_register(function ($class) {
    // Base directory for all classes
    $baseDir = __DIR__ . '/../classes/';
    
    // Convert class name to file path (case-insensitive)
    $file = $baseDir . strtolower($class) . '.php';
    
    // Check if file exists and include it
    if (file_exists($file)) {
        require $file;
        return true;
    }
    
    return false;
});
?>