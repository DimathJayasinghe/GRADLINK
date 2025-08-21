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
			$this->db->bind(':u',$uid); $this->db->bind(':c',$content); return $this->db->execute();
		}
		try {
			$this->db->query('INSERT INTO posts (user_id,content,image) VALUES (:u,:c,:i)');
			$this->db->bind(':u',$uid); $this->db->bind(':c',$content); $this->db->bind(':i',$image); return $this->db->execute();
		} catch (Throwable $e) {
			// If schema not updated yet (Unknown column 'image'), retry without image
			if(stripos($e->getMessage(),'unknown column')!==false && stripos($e->getMessage(),"image")!==false){
				$this->db->query('INSERT INTO posts (user_id,content) VALUES (:u,:c)');
				$this->db->bind(':u',$uid); $this->db->bind(':c',$content); return $this->db->execute();
			}
			throw $e; // Different error, rethrow
		}
	}
	public function getFeed($limit=20){
		$this->db->query('SELECT p.*, u.name, u.profile_image, (SELECT COUNT(*) FROM post_likes l WHERE l.post_id=p.id) likes,(SELECT COUNT(*) FROM comments c WHERE c.post_id=p.id) comments FROM posts p JOIN users u ON u.id=p.user_id ORDER BY p.created_at DESC LIMIT :l');
		$this->db->bind(':l',(int)$limit,PDO::PARAM_INT); return $this->db->resultSet(); }
	public function addComment($pid,$uid,$content){ $this->db->query('INSERT INTO comments (post_id,user_id,content) VALUES (:p,:u,:c)'); $this->db->bind(':p',$pid); $this->db->bind(':u',$uid); $this->db->bind(':c',$content); return $this->db->execute(); }
	public function getComments($pid){ $this->db->query('SELECT c.id,c.content,c.created_at,u.name,u.profile_image FROM comments c JOIN users u ON u.id=c.user_id WHERE c.post_id=:p ORDER BY c.created_at ASC'); $this->db->bind(':p',$pid); return $this->db->resultSet(); }
	public function toggleLike($pid,$uid){ $this->db->query('SELECT 1 FROM post_likes WHERE post_id=:p AND user_id=:u'); $this->db->bind(':p',$pid); $this->db->bind(':u',$uid); $exists=$this->db->single(); if($exists){ $this->db->query('DELETE FROM post_likes WHERE post_id=:p AND user_id=:u'); $this->db->bind(':p',$pid); $this->db->bind(':u',$uid); $this->db->execute(); return 'unliked'; } $this->db->query('INSERT INTO post_likes (post_id,user_id) VALUES (:p,:u)'); $this->db->bind(':p',$pid); $this->db->bind(':u',$uid); $this->db->execute(); return 'liked'; }
	public function isLiked($pid,$uid){ $this->db->query('SELECT 1 FROM post_likes WHERE post_id=:p AND user_id=:u'); $this->db->bind(':p',$pid); $this->db->bind(':u',$uid); return (bool)$this->db->single(); }
}
?>
