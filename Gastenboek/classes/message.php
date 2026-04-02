<?php
require_once "database.php";
require_once "imagehandler.php";

class Message {
    private $db;
    private $imageHandler;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->imageHandler = new ImageHandler();
    }
    
    public function getAllMessages() {
        $sql = "SELECT m.id, m.title, m.message, m.created_at, m.image_path, m.user_id, u.username 
                FROM messages m 
                JOIN users u ON m.user_id = u.id 
                ORDER BY m.created_at DESC";
        
        return $this->db->query($sql);
    }
    
    public function getUserMessage($messageId, $userId) {
        $sql = "SELECT * FROM messages WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $messageId, $userId);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    public function createMessage($userId, $title, $message, $imageData = null) {
        $image_path = null;
        $image_err = "";
        
        // Process image if provided
        if ($imageData !== null && $imageData['error'] != UPLOAD_ERR_NO_FILE) {
            list($image_path, $image_err) = $this->imageHandler->processImage($imageData);
            if (!empty($image_err)) {
                return [false, $image_err, null];
            }
        }
        
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Insert message
            $sql = "INSERT INTO messages (user_id, title, message, image_path) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("isss", $userId, $title, $message, $image_path);
            $stmt->execute();
            $stmt->close();
            
            // Update user's last message time
            $sql = "UPDATE users SET last_message_time = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();
            
            // Commit transaction
            $this->db->commit();
            
            return [true, "", null];
            
        } catch (Exception $e) {
            $this->db->rollback();
            
            // Delete uploaded image if exists
            if ($image_path && file_exists($image_path)) {
                unlink($image_path);
            }
            
            return [false, "Database error: " . $e->getMessage(), null];
        }
    }
    
    public function updateMessage($messageId, $userId, $title, $message, $imageData = null, $originalImage = null, $removeImage = false) {
        $image_path = $originalImage;
        $image_err = "";
        
        // Process new image if uploaded
        if ($imageData !== null && $imageData['error'] != UPLOAD_ERR_NO_FILE) {
            list($image_path, $image_err) = $this->imageHandler->processImage($imageData);
            if (!empty($image_err)) {
                return [false, $image_err];
            }
        }
        
        // Handle remove image checkbox
        if ($removeImage) {
            if (!empty($originalImage) && file_exists($originalImage)) {
                unlink($originalImage);
            }
            $image_path = null;
        }
        
        // Update database
        $sql = "UPDATE messages SET title = ?, message = ?, image_path = ? WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssii", $title, $message, $image_path, $messageId, $userId);
        
        if ($stmt->execute()) {
            // If update successful and image has changed, delete old image
            if ($image_path != $originalImage && !empty($originalImage) && file_exists($originalImage) && !$removeImage) {
                unlink($originalImage);
            }
            $stmt->close();
            return [true, ""];
        } else {
            // If update fails and a new image was uploaded, delete it
            if ($image_path != $originalImage && !empty($image_path) && file_exists($image_path)) {
                unlink($image_path);
            }
            $stmt->close();
            return [false, "Failed to update message"];
        }
    }
    
    public function deleteMessage($messageId, $userId) {
        // Get the image path first
        $sql = "SELECT image_path FROM messages WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $messageId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $image_path = $row["image_path"];
            $stmt->close();
            
            // Delete the message
            $sql = "DELETE FROM messages WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $messageId);
            
            if ($stmt->execute()) {
                // Delete associated image if exists
                if (!empty($image_path) && file_exists($image_path)) {
                    unlink($image_path);
                }
                $stmt->close();
                return true;
            }
            $stmt->close();
        }
        return false;
    }
}