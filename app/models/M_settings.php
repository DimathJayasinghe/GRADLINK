<?php
class M_settings extends Database {
    
    /**
     * Get user by ID
     */
    public function getUserById($userId) {
        $sql = "SELECT * FROM users WHERE id = :user_id LIMIT 1";
        
        $this->query($sql);
        $this->bind(':user_id', $userId);
        
        return $this->single();
    }
    
    /**
     * Update user name and display name
     */
    public function updateName($userId, $name, $displayName = null) {
        $sql = "UPDATE users 
                SET name = :name, 
                    display_name = :display_name 
                WHERE id = :user_id";
        
        $this->query($sql);
        $this->bind(':user_id', $userId);
        $this->bind(':name', $name);
        $this->bind(':display_name', $displayName);
        
        return $this->execute();
    }
    
    /**
     * Update user bio
     */
    public function updateBio($userId, $bio) {
        $sql = "UPDATE users 
                SET bio = :bio 
                WHERE id = :user_id";
        
        $this->query($sql);
        $this->bind(':user_id', $userId);
        $this->bind(':bio', $bio);
        
        return $this->execute();
    }
    
    /**
     * Update user email
     */
    public function updateEmail($userId, $email) {
        $sql = "UPDATE users 
                SET email = :email 
                WHERE id = :user_id";
        
        $this->query($sql);
        $this->bind(':user_id', $userId);
        $this->bind(':email', $email);
        
        return $this->execute();
    }
    
    /**
     * Update user password
     */
    public function updatePassword($userId, $hashedPassword) {
        $sql = "UPDATE users 
                SET password = :password 
                WHERE id = :user_id";
        
        $this->query($sql);
        $this->bind(':user_id', $userId);
        $this->bind(':password', $hashedPassword);
        
        return $this->execute();
    }
    
    /**
     * Check if email already exists (excluding current user)
     */
    public function emailExists($email, $excludeUserId = null) {
        $sql = "SELECT id FROM users WHERE email = :email";
        
        if ($excludeUserId) {
            $sql .= " AND id != :exclude_id";
        }
        
        $sql .= " LIMIT 1";
        
        $this->query($sql);
        $this->bind(':email', $email);
        
        if ($excludeUserId) {
            $this->bind(':exclude_id', $excludeUserId);
        }
        
        return $this->single() !== false;
    }
    
    /**
     * Delete user account and all related data
     */
    public function deleteAccount($userId) {
        try {
            // Start transaction
            $this->beginTransaction();
            
            // Delete related data in order (delete child records first to avoid foreign key issues)
            
            // Delete certificates
            try {
                $this->query("DELETE FROM certificates WHERE user_id = :user_id");
                $this->bind(':user_id', $userId);
                $this->execute();
            } catch (Exception $e) {
                error_log("Error deleting certificates: " . $e->getMessage());
                throw new Exception("Failed to delete certificates: " . $e->getMessage());
            }
            
            // Delete comments
            $this->query("DELETE FROM comments WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete post likes
            $this->query("DELETE FROM post_likes WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete posts
            $this->query("DELETE FROM posts WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete notifications (both sent and received)
            $this->query("DELETE FROM notifications WHERE receiver_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete follow requests (both sent and received)
            $this->query("DELETE FROM follow_requests WHERE requester_id = :user_id OR target_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete followers (both follower and followed)
            $this->query("DELETE FROM followers WHERE follower_id = :user_id OR followed_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete messages
            $this->query("DELETE FROM messages WHERE sender_id = :user_id OR receiver_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete event attendees
            $this->query("DELETE FROM event_attendees WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete event bookmarks
            $this->query("DELETE FROM event_bookmarks WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete event images for events organized by this user
            $this->query("DELETE FROM event_images WHERE event_id IN (SELECT id FROM events WHERE organizer_id = :user_id)");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete event requests
            $this->query("DELETE FROM event_requests WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete events organized
            $this->query("DELETE FROM events WHERE organizer_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Delete user profile visibility
            $this->query("DELETE FROM user_profiles_visibility WHERE user_id = :user_id");
            $this->bind(':user_id', $userId);
            $this->execute();
            
            // Finally delete user
            $this->query("DELETE FROM users WHERE id = :user_id");
            $this->bind(':user_id', $userId);
            $result = $this->execute();
            
            // Commit transaction
            $this->commit();
            
            return $result;
        } catch (Exception $e) {
            // Rollback on error
            $this->rollBack();
            throw $e;
        }
    }
}
?>