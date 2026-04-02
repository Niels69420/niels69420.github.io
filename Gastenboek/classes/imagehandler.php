<?php
class ImageHandler {
    private $uploadDir;
    private $maxFileSize;
    private $allowedExtensions;
    private $maxDimension;
    
    public function __construct() {
        $this->uploadDir = "uploads/";
        $this->maxFileSize = 5 * 1024 * 1024; // 5MB
        $this->allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $this->maxDimension = 300; // Max width/height
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }
    
    public function processImage($imageData) {
        $image_path = null;
        $image_err = "";
        
        // Check for upload errors
        if ($imageData['error'] != UPLOAD_ERR_OK) {
            return [null, "Error uploading file. Error code: " . $imageData['error']];
        }
        
        // Check file extension
        $file_name = $imageData['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $this->allowedExtensions)) {
            return [null, "Only JPG, PNG and GIF images are allowed."];
        }
        
        // Check file size
        if ($imageData['size'] > $this->maxFileSize) {
            return [null, "Image must be less than 5MB in size."];
        }
        
        // Generate simple filename: picture_X.extension
        // First, find the next available number by scanning the directory
        $nextNumber = $this->getNextImageNumber();
        $image_name = "picture_" . $nextNumber . "." . $file_ext;
        $image_path = $this->uploadDir . $image_name;
        
        // Get image dimensions and type
        list($width, $height, $type) = getimagesize($imageData['tmp_name']);
        
        // Check if image needs resizing
        if ($width > $this->maxDimension || $height > $this->maxDimension) {
            // Calculate new dimensions while maintaining aspect ratio
            if ($width > $height) {
                $new_width = $this->maxDimension;
                $new_height = intval($height * ($this->maxDimension / $width));
            } else {
                $new_height = $this->maxDimension;
                $new_width = intval($width * ($this->maxDimension / $height));
            }
            
            // Create a new image with the new dimensions
            $source = null;
            $destination = imagecreatetruecolor($new_width, $new_height);
            
            // Preserve transparency for PNG and GIF
            if ($file_ext == 'png' || $file_ext == 'gif') {
                imagealphablending($destination, false);
                imagesavealpha($destination, true);
                $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
                imagefilledrectangle($destination, 0, 0, $new_width, $new_height, $transparent);
            }
            
            // Create source image based on file type
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $source = imagecreatefromjpeg($imageData['tmp_name']);
                    break;
                case IMAGETYPE_PNG:
                    $source = imagecreatefrompng($imageData['tmp_name']);
                    break;
                case IMAGETYPE_GIF:
                    $source = imagecreatefromgif($imageData['tmp_name']);
                    break;
            }
            
            // Resize the image
            if ($source) {
                imagecopyresampled($destination, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                
                // Save the resized image based on file type
                $save_success = false;
                switch ($type) {
                    case IMAGETYPE_JPEG:
                        $save_success = imagejpeg($destination, $image_path, 90); // 90 is the quality
                        break;
                    case IMAGETYPE_PNG:
                        $save_success = imagepng($destination, $image_path, 9); // 9 is the compression level
                        break;
                    case IMAGETYPE_GIF:
                        $save_success = imagegif($destination, $image_path);
                        break;
                }
                
                // Free up memory
                imagedestroy($source);
                imagedestroy($destination);
                
                if (!$save_success) {
                    return [null, "Failed to save the resized image."];
                }
            } else {
                return [null, "Failed to process the uploaded image."];
            }
        } else {
            // No resizing needed, just move the uploaded file
            if (!move_uploaded_file($imageData['tmp_name'], $image_path)) {
                return [null, "Failed to save the image."];
            }
        }
        
        return [$image_path, ""];
    }
    
    /**
     * Find the next available image number by scanning the uploads directory
     */
    private function getNextImageNumber() {
        $highest = 0;
        $pattern = '/picture_(\d+)\.(jpg|jpeg|png|gif)$/i';
        
        // If directory doesn't exist, return 1
        if (!is_dir($this->uploadDir)) {
            return 1;
        }
        
        // Scan the directory for image files
        $files = scandir($this->uploadDir);
        
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            
            // Check if this file matches our naming pattern
            if (preg_match($pattern, $file, $matches)) {
                $number = (int)$matches[1];
                if ($number > $highest) {
                    $highest = $number;
                }
            }
        }
        
        // Return the next number in sequence
        return $highest + 1;
    }
}