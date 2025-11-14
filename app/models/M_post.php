<?php
class M_post {
	private $db; public function __construct(){ $this->db=new Database; }
	/**
	 * Create a post. If the image column does not exist yet, it will gracefully
	 * fall back to inserting without the image instead of crashing.
	 */
	public function createPost($uid,$content,$image=null){
		if($image === null){
			// Simple path (no image provided)
			$this->db->query('INSERT INTO posts (user_id,content) VALUES (:u,:c)');
			$this->db->bind(':u',$uid); $this->db->bind(':c',$content);
			$ok = $this->db->execute();
			if(!$ok) return false;
			$id = $this->db->lastInsertId();
			return $id ? (int)$id : true; // return inserted id when possible
		}
		try {
			$this->db->query('INSERT INTO posts (user_id,content,image) VALUES (:u,:c,:i)');
			$this->db->bind(':u',$uid); $this->db->bind(':c',$content); $this->db->bind(':i',$image);
			$ok = $this->db->execute();
			if(!$ok) return false;
			$id = $this->db->lastInsertId();
			return $id ? (int)$id : true;
		} catch (Throwable $e) {
			// If schema not updated yet (Unknown column 'image'), retry without image
			if(stripos($e->getMessage(),'unknown column')!==false && stripos($e->getMessage(),"image")!==false){
				$this->db->query('INSERT INTO posts (user_id,content) VALUES (:u,:c)');
				$this->db->bind(':u',$uid); $this->db->bind(':c',$content);
				$ok = $this->db->execute();
				if(!$ok) return false;
				$id = $this->db->lastInsertId();
				return $id ? (int)$id : true;
			}
			throw $e; // Different error, rethrow
		}
	}
	public function getFeed($limit=20){
		$this->db->query('SELECT p.*, u.name, u.profile_image, u.role, (SELECT COUNT(*) FROM post_likes l WHERE l.post_id=p.id) likes,(SELECT COUNT(*) FROM comments c WHERE c.post_id=p.id) comments FROM posts p JOIN users u ON u.id=p.user_id ORDER BY p.created_at DESC LIMIT :l');
		$this->db->bind(':l',(int)$limit,PDO::PARAM_INT); return $this->db->resultSet(); }
		
	public function addComment($pid,$uid,$content){ $this->db->query('INSERT INTO comments (post_id,user_id,content) VALUES (:p,:u,:c)'); $this->db->bind(':p',$pid); $this->db->bind(':u',$uid); $this->db->bind(':c',$content); return $this->db->execute(); }
	public function getComments($pid){
		// Alias IDs to avoid name collisions and provide stable keys for frontend
		$this->db->query('SELECT c.id AS comment_id, c.content, c.created_at, u.name, u.profile_image, u.role, u.id AS user_id FROM comments c JOIN users u ON u.id=c.user_id WHERE c.post_id=:p ORDER BY c.created_at ASC');
		$this->db->bind(':p',$pid);
		return $this->db->resultSet();
	}
	public function toggleLike($pid, $uid) { 
		try {
			// Check if the post exists first
			$this->db->query('SELECT 1 FROM posts WHERE id = :pid');
			$this->db->bind(':pid', $pid);
			$postExists = $this->db->single();
			
			if (!$postExists) {
				return 'error_post_not_found';
			}
			
			// Check if already liked
			$this->db->query('SELECT 1 FROM post_likes WHERE post_id = :p AND user_id = :u');
			$this->db->bind(':p', $pid); 
			$this->db->bind(':u', $uid);
			$exists = $this->db->single();
			
			if ($exists) {
				// Remove like
				$this->db->query('DELETE FROM post_likes WHERE post_id = :p AND user_id = :u');
				$this->db->bind(':p', $pid);
				$this->db->bind(':u', $uid);
				$success = $this->db->execute();
				return $success ? 'unliked' : 'error_unlike_failed';
			} else {
				// Add like
				$this->db->query('INSERT INTO post_likes (post_id, user_id) VALUES (:p, :u)');
				$this->db->bind(':p', $pid);
				$this->db->bind(':u', $uid);
				$success = $this->db->execute();
				return $success ? 'liked' : 'error_like_failed';
			}
		} catch (Exception $e) {
			error_log('Like toggle error: ' . $e->getMessage());
			return 'error_db_exception';
		}
	}
	
	public function isLiked($pid, $uid) { 
		$this->db->query('SELECT 1 FROM post_likes WHERE post_id = :p AND user_id = :u');
		$this->db->bind(':p', $pid);
		$this->db->bind(':u', $uid);
		return (bool)$this->db->single();
	}
	
	/**
	 * Get post by ID
	 */
	public function getPostById($id) {
		$this->db->query('SELECT * FROM posts WHERE id = :id');
		$this->db->bind(':id', $id);
		return $this->db->single();
	}

	/**
	 * Get the user_id (owner) of a post
	 */
	public function getPostOwnerId($postId) {
		$this->db->query('SELECT user_id FROM posts WHERE id = :id');
		$this->db->bind(':id', (int)$postId);
		$row = $this->db->single();
		return $row ? (int)$row->user_id : null;
	}
	
	/**
	 * Update an existing post
	 */
	public function updatePost($id, $content, $image = null) {
		try {
			if ($image === null) {
				// Update without changing the image
				$this->db->query('UPDATE posts SET content = :content WHERE id = :id');
				$this->db->bind(':content', $content);
				$this->db->bind(':id', $id);
			} else {
				// Update with new image
				$this->db->query('UPDATE posts SET content = :content, image = :image WHERE id = :id');
				$this->db->bind(':content', $content);
				$this->db->bind(':image', $image);
				$this->db->bind(':id', $id);
			}
			
			return $this->db->execute();
		} catch (Throwable $e) {
			// If schema not updated yet (Unknown column 'image'), handle gracefully
			if (stripos($e->getMessage(), 'unknown column') !== false && stripos($e->getMessage(), "image") !== false) {
				// Try again without image field
				$this->db->query('UPDATE posts SET content = :content WHERE id = :id');
				$this->db->bind(':content', $content);
				$this->db->bind(':id', $id);
				return $this->db->execute();
			}
			return false;
		}
	}


	//ADMIN CONTENT MANAGEMENT METHODS
	public function adminGetPosts($status = 'all', $search = '') {
		$sql = 'SELECT p.*, u.name as author FROM posts p JOIN users u ON u.id = p.user_id';
		$where = [];
		$params = [];
		// No status column in posts table, so ignore status filter
		if ($search !== '') {
			$where[] = '(u.name LIKE :search OR p.content LIKE :search)';
			$params[':search'] = "%$search%";
		}
		if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
		$sql .= ' ORDER BY p.created_at DESC LIMIT 100';
		$this->db->query($sql);
		foreach ($params as $k => $v) $this->db->bind($k, $v);
		return $this->db->resultSet();
	}

	public function adminApprovePost($id) {
		// No status column, so just return true
		return true;
	}

	public function adminRejectPost($id) {
		// No status column, so just return true
		return true;
	}

	public function adminDeletePost($id) {
		try {
			// Attempt to fetch image name (schema may not have image column yet)
			$postImage = null;
			try {
				$this->db->query('SELECT image FROM posts WHERE id = :id');
				$this->db->bind(':id', $id);
				$row = $this->db->single();
				if ($row && isset($row->image)) {
					$postImage = $row->image;
				}
			} catch (Throwable $e) {
				// If image column doesn't exist yet, ignore and continue deletion flow
				if (stripos($e->getMessage(), 'unknown column') === false) {
					throw $e;
				}
			}

			// Delete comments first (correct table name is `comments`)
			$this->db->query('DELETE FROM comments WHERE post_id = :id');
			$this->db->bind(':id', $id);
			$this->db->execute();

			// Delete likes
			$this->db->query('DELETE FROM post_likes WHERE post_id = :id');
			$this->db->bind(':id', $id);
			$this->db->execute();

			// Finally delete the post itself
			$this->db->query('DELETE FROM posts WHERE id = :id');
			$this->db->bind(':id', $id);
			$result = $this->db->execute();

			// Remove associated image file if it exists and we successfully deleted the post
			if ($result && $postImage) {
				$path = APPROOT . '/storage/posts/' . $postImage;
				if (is_file($path)) {
					@unlink($path);
				}
			}

			return $result;
		} catch (Exception $e) {
			error_log("Error deleting post: " . $e->getMessage());
			return false;
		}
	}

	public function getUserById($userId){
		$this->db->query('SELECT id,role FROM users WHERE id = :id');
		$this->db->bind(':id', $userId);
		return $this->db->single();
	}
}
?>