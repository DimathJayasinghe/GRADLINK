<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GRADLINK - Main Feed</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/mainfeed_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="feed-container">
        <!-- Include the left sidebar component -->
        <?php require APPROOT . '/views/inc/commponents/leftSideBar.php'; ?>

        <div class="main-content">
            <div class="tabs">
                <div class="tab active">For you</div>
                <div class="tab">Following</div>
            </div>

            <div class="compose-post">
                <div class="compose-input">
                    <img src="<?php echo URLROOT; ?>/img/default-profile.jpg" alt="Profile" class="profile-photo">
                    <input type="text" placeholder="What's happening?">
                </div>
                <div class="compose-actions">
                    <div class="compose-tools">
                        <button class="attach-btn">Attach</button>
                    </div>
                    <button class="post-btn">Post</button>
                </div>
            </div>

            

            <div class="feed">
                <!-- IEEE Event Announcement Post -->
                <div class="post">
                    <div class="post-header">
                        <div class="post-user">
                            <img src="<?php echo URLROOT; ?>/img/ieee-logo.jpg" alt="User" class="profile-photo">
                            <div class="post-user-info">
                                <span class="post-user-name">IEEE Student Branch</span>
                                <span class="post-user-handle">@IEEEStudentBranch</span>
                                <span class="post-time"> · Aug 19</span>
                                <div class="post-content">
                                    <p>📢 IEEE INTRODUCTORY SESSION COMING SOON! Join us to learn about exciting opportunities in tech. #IEEESCSC #IEEEUCSC #WIE</p>
                                </div>
                            </div>
                        </div>
                        <div class="post-menu">
                            <i class="fas fa-ellipsis-h"></i>
                        </div>
                    </div>

                    <div class="post-media">
                        <img src="<?php echo URLROOT; ?>/img/posts/sample_post.png" alt="Post image">
                    </div>

                    <div class="post-actions">
                        <div class="comment-btn" data-post-id="post-1234"><i class="far fa-comment"></i> <?= rand(15, 45) ?></div>
                        <div><i class="fas fa-retweet"></i> <?= rand(30, 120) ?></div>
                        <div><i class="far fa-heart"></i> <?= rand(100, 350) ?></div>
                    </div>
                </div>

                <!-- Research Collaboration Post -->
                <div class="post">
                    <div class="post-header">
                        <div class="post-user">
                            <img src="<?php echo URLROOT; ?>/img/professor-profile.jpg" alt="User" class="profile-photo">
                            <div class="post-user-info">
                                <span class="post-user-name">Dr. Sarah Johnson</span>
                                <span class="post-user-handle">@DrJohnson</span>
                                <span class="post-time"> · Aug 17</span>
                                <div class="post-content">
                                    <p>Looking for graduate students interested in AI ethics research. Funding available for qualified applicants. DM for details or email me at s.johnson@university.edu #AIResearch #GradStudies</p>
                                </div>
                            </div>
                        </div>
                        <div class="post-menu">
                            <i class="fas fa-ellipsis-h"></i>
                        </div>
                    </div>

                    <div class="post-media">
                        <img src="<?php echo URLROOT; ?>/img/posts/sample_post.png" alt="Post image">
                    </div>

                    <div class="post-actions">
                        <div class="comment-btn" data-post-id="post-9012"><i class="far fa-comment"></i> <?= rand(15, 45) ?></div>
                        <div><i class="fas fa-retweet"></i> <?= rand(20, 70) ?></div>
                        <div><i class="far fa-heart"></i> <?= rand(90, 200) ?></div>
                    </div>
                </div>

                <!-- Graduate Success Story Post -->
                <div class="post">
                    <div class="post-header">
                        <div class="post-user">
                            <img src="<?php echo URLROOT; ?>/img/alumni-profile.jpg" alt="User" class="profile-photo">
                            <div class="post-user-info">
                                <span class="post-user-name">Alex Chen</span>
                                <span class="post-user-handle">@AlexChenTech</span>
                                <span class="post-time"> · Aug 16</span>
                                <div class="post-content">
                                    <p>Just accepted my dream job at Google! Thanks to everyone at GRADLINK who helped me prepare for interviews and connect with the right people. Networking really does make all the difference! #GradSuccess #NewBeginnings</p>
                                </div>
                            </div>
                        </div>
                        <div class="post-menu">
                            <i class="fas fa-ellipsis-h"></i>
                        </div>
                    </div>

                    <div class="post-media">
                        <img src="<?php echo URLROOT; ?>/img/posts/sample_post.png" alt="Post image">
                    </div>

                    <div class="post-actions">
                        <div class="comment-btn" data-post-id="post-3456"><i class="far fa-comment"></i> <?= rand(30, 80) ?></div>
                        <div><i class="fas fa-retweet"></i> <?= rand(25, 60) ?></div>
                        <div><i class="far fa-heart"></i> <?= rand(200, 500) ?></div>
                    </div>
                </div>
            </div>
            <div class="show-more">Show new posts</div>
        </div>

        <!-- Include the right sidebar component -->
        <?php 
        $rightSidebarStylesIncluded = true; // Prevent duplicate styles
        require APPROOT . '/views/inc/commponents/rightSideBar.php'; 
        ?>
    </div>

    <!-- Comment Modal -->
    <div class="comment-modal" id="commentModal">
        <div class="comment-modal-content">
            <div class="comment-modal-header">
                <h3>Comments</h3>
                <button class="modal-close-btn" id="closeCommentModal"><i class="fas fa-times"></i></button>
            </div>

            <div class="comment-list" id="commentsList">
                <!-- Comments will be loaded here -->
            </div>

            <div class="comment-form">
                <img src="<?php echo URLROOT; ?>/img/default-profile.jpg" alt="Profile" class="profile-photo">
                <div class="comment-input-container">
                    <textarea id="commentText" placeholder="Write a comment..."></textarea>
                </div>
                <button class="comment-btn" id="submitComment">Comment</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Show more posts functionality
            document.querySelector('.main-content .show-more').addEventListener('click', function() {
                this.textContent = 'Loading...';
                setTimeout(() => {
                    this.textContent = 'Show more';
                }, 1000);
            });

            // Comment Modal Functionality
            const commentModal = document.getElementById('commentModal');
            const closeCommentModal = document.getElementById('closeCommentModal');
            const commentsList = document.getElementById('commentsList');
            const submitComment = document.getElementById('submitComment');
            const commentText = document.getElementById('commentText');

            // Sample comments data for different posts
            const commentsData = {
                'post-1234': [{
                        name: 'Jane Smith',
                        handle: '@janesmith',
                        content: 'This looks really interesting! Will this be recorded for those who can\'t attend?',
                        likes: 12
                    },
                    {
                        name: 'Mark Johnson',
                        handle: '@markjohnson',
                        content: 'I attended the last session and it was fantastic! Highly recommend it to all students.',
                        likes: 8
                    }
                ],
                'post-5678': [{
                        name: 'David Wilson',
                        handle: '@davidw',
                        content: 'Applied last year and had a great experience! The mentorship was invaluable.',
                        likes: 15
                    },
                    {
                        name: 'Sarah Lee',
                        handle: '@sarahlee',
                        content: 'Do they accept international students?',
                        likes: 7
                    }
                ],
                'post-9012': [{
                        name: 'Michael Chang',
                        handle: '@mchang',
                        content: 'I\'m currently working on something similar. Would love to collaborate!',
                        likes: 10
                    },
                    {
                        name: 'Emily Wang',
                        handle: '@emwang',
                        content: 'Is prior experience in AI required?',
                        likes: 6
                    }
                ],
                'post-3456': [{
                        name: 'Taylor Rodriguez',
                        handle: '@tayrod',
                        content: 'Congratulations! Can you share any interview tips that helped you succeed?',
                        likes: 18
                    },
                    {
                        name: 'Jordan Kim',
                        handle: '@jkim',
                        content: 'That\'s amazing! Google is my dream company too. Way to go!',
                        likes: 24
                    }
                ]
            };

            // Current post ID being viewed
            let currentPostId = null;

            // Add click event to all comment buttons in posts
            document.querySelectorAll('.comment-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const postId = this.getAttribute('data-post-id');

                    // Display the comment modal
                    commentModal.style.display = 'flex';
                    currentPostId = postId;

                    // Load comments for this post
                    loadComments(postId);

                    // Focus on the comment input
                    commentText.focus();
                });
            });

            // Close comment modal
            closeCommentModal.addEventListener('click', function() {
                commentModal.style.display = 'none';
            });

            // Close when clicking outside of the modal content
            window.addEventListener('click', function(event) {
                if (event.target === commentModal) {
                    commentModal.style.display = 'none';
                }
            });

            // Load comments for a specific post
            function loadComments(postId) {
                commentsList.innerHTML = '';

                const comments = commentsData[postId] || [];

                if (comments.length === 0) {
                    commentsList.innerHTML = '<div class="no-comments">No comments yet. Be the first to comment!</div>';
                    return;
                }

                comments.forEach(comment => {
                    const commentElement = document.createElement('div');
                    commentElement.className = 'comment';
                    commentElement.innerHTML = `
                        <img src="<?php echo URLROOT; ?>/img/default-profile.jpg" alt="Profile" class="profile-photo">
                        <div class="comment-user-info">
                            <div>
                                <span class="comment-user-name">${comment.name}</span>
                                <span class="comment-user-handle">${comment.handle}</span>
                            </div>
                            <div class="comment-content">
                                <p>${comment.content}</p>
                            </div>
                            <div class="comment-actions">
                                <div class="comment-action"><i class="far fa-heart"></i> ${comment.likes}</div>
                            </div>
                        </div>
                    `;
                    commentsList.appendChild(commentElement);
                });
            }

            // Submit a new comment
            submitComment.addEventListener('click', function() {
                const comment = commentText.value.trim();

                if (comment !== '') {
                    // Create new comment object
                    const newComment = {
                        name: 'You',
                        handle: '@yourusername',
                        content: comment,
                        likes: 0
                    };

                    // Add to data store
                    if (!commentsData[currentPostId]) {
                        commentsData[currentPostId] = [];
                    }
                    commentsData[currentPostId].unshift(newComment);

                    // Reload comments
                    loadComments(currentPostId);

                    // Clear input
                    commentText.value = '';
                }
            });

            // Allow pressing Enter to submit comment
            commentText.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    submitComment.click();
                }
            });
        });
    </script>
</body>
</html>