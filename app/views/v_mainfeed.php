<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GRADLINK - Main Feed</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            background-color: var(--bg);
            color: var(--text);
            height: 100vh;
            overflow: hidden;
        }

        .feed-container {
            display: flex;
            max-width: 1200px;
            width: 100%;
            height: 100vh;
        }

        /* Left Sidebar */
        .sidebar {
            width: 275px;
            padding: 20px;
            border-right: 1px solid var(--border);
        }

        .sidebar-menu {
            list-style: none;
            margin-top: 20px;
        }

        .sidebar-menu li {
            margin-bottom: 25px;
            font-size: 18px;
            display: flex;
            align-items: center;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }

        .sidebar-menu li:hover {
            color: var(--link);
        }

        .sidebar-menu li i, .sidebar-menu li img {
            margin-right: 15px;
            font-size: 22px;
        }

        .sidebar-menu .notification-badge {
            background-color: var(--link);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            position: relative;
            top: -10px;
            left: -10px;
        }

        .post-button {
            background-color: var(--btn);
            color: var(--btn-text);
            border: none;
            border-radius: 30px;
            padding: 15px 0;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: var(--transition);
        }

        .post-button:hover {
            background-color: var(--link);
            transform: translateY(-2px);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            border-right: 1px solid var(--border);
            max-width: 600px;
            overflow-y: auto;
        }

        .tabs {
            display: flex;
            border-bottom: 1px solid var(--border);
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 15px 0;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .tab:hover:not(.active) {
            background-color: rgba(158, 212, 220, 0.05);
        }

        .tab.active {
            border-bottom: 4px solid var(--link);
        }

        .compose-post {
            padding: 15px;
            border-bottom: 1px solid var(--border);
        }

        .compose-input {
            display: flex;
            margin-bottom: 15px;
        }

        .profile-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
        }

        .compose-input input {
            background: transparent;
            border: none;
            color: var(--text);
            font-size: 18px;
            width: 100%;
            outline: none;
        }

        .compose-actions {
            display: flex;
            justify-content: space-between;
            margin-left: 55px;
        }

        .compose-tools {
            display: flex;
        }

        .compose-tools i {
            color: var(--link);
            margin-right: 15px;
            font-size: 18px;
            cursor: pointer;
            transition: var(--transition);
        }

        .compose-tools i:hover {
            transform: scale(1.1);
        }

        .post-btn {
            background-color: var(--btn);
            color: var(--btn-text);
            border: none;
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .post-btn:hover {
            background-color: var(--link);
            transform: translateY(-2px);
        }

        .feed {
            overflow-y: auto;
        }

        .show-more {
            text-align: center;
            padding: 15px;
            color: var(--link);
            cursor: pointer;
            transition: var(--transition);
        }

        .show-more:hover {
            background-color: rgba(158, 212, 220, 0.05);
        }

        .post {
            border-bottom: 1px solid var(--border);
            padding: 15px;
            transition: var(--transition);
        }

        .post:hover {
            background-color: rgba(15, 21, 24, 0.5);
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .post-user {
            display: flex;
        }

        .post-user-info {
            margin-left: 10px;
        }

        .post-user-name {
            font-weight: 600;
        }

        .post-user-handle, .post-time {
            color: var(--muted);
        }

        .post-content {
            margin: 10px 0;
            line-height: 1.5;
        }

        .post-actions {
            display: flex;
            justify-content: space-around;
            margin-top: 15px;
            padding: 0 60px;
        }

        .post-actions div {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        .post-actions i {
            font-size: 18px;
            transition: var(--transition);
        }

        .post-actions i:hover {
            color: var(--link);
            transform: scale(1.2);
        }

        .post-media {
            margin-top: 10px;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            width: 100%;
            padding-bottom: 100%; /* This creates a square aspect ratio */
        }

        .post-media img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover; /* This maintains aspect ratio while filling the square */
        }

        /* Right Sidebar */
        .right-sidebar {
            width: 325px;
            padding: 15px;
        }

        .search-container {
            background-color: var(--input);
            border-radius: 20px;
            padding: 10px 15px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .search-container i {
            color: var(--muted);
            margin-right: 10px;
        }

        .search-container input {
            background: transparent;
            border: none;
            color: var(--text);
            width: 100%;
            outline: none;
            font-family: 'Poppins', sans-serif;
        }

        .card {
            background-color: var(--card);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--text);
        }

        .trend {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: var(--transition);
        }

        .trend:hover {
            transform: translateX(5px);
        }

        .trend:last-child {
            border-bottom: none;
        }

        .trend-category {
            font-size: 13px;
            color: var(--muted);
        }

        .trend-name {
            font-weight: 600;
            color: var(--text);
        }

        .trend-posts {
            font-size: 13px;
            color: var(--muted);
        }

        .follow-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border);
        }

        .follow-card:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .follow-info {
            display: flex;
            align-items: center;
        }

        .follow-details {
            margin-left: 10px;
        }

        .follow-name {
            font-weight: 600;
            color: var(--text);
        }

        .follow-handle {
            color: var(--muted);
            font-size: 13px;
        }

        .follow-btn {
            background-color: var(--text);
            color: var(--bg);
            border: none;
            border-radius: 20px;
            padding: 6px 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .follow-btn:hover {
            background-color: var(--link);
            color: var(--btn-text);
        }

        /* New attach button style */
        .attach-btn {
            background-color: transparent;
            color: var(--link);
            border: 1px solid var(--link);
            border-radius: 20px;
            padding: 6px 16px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: var(--transition);
        }

        .attach-btn:hover {
            background-color: rgba(158, 212, 220, 0.1);
            transform: translateY(-2px);
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(158, 212, 220, 0.3);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--link);
        }

        /* Logo styling */
        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .logo img {
            height: 40px;
            filter: drop-shadow(0 0 5px rgba(158, 212, 220, 0.3));
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .feed-container {
                max-width: 100%;
            }
        }

        @media (max-width: 992px) {
            .right-sidebar {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 20px 10px;
            }
            
            .sidebar-menu li span {
                display: none;
            }
            
            .sidebar-menu li i {
                margin-right: 0;
                font-size: 24px;
            }
            
            .post-button {
                font-size: 0;
                padding: 15px;
            }
            
            .post-button::after {
                content: "+";
                font-size: 24px;
                font-weight: 300;
            }
        }

        /* Post modal styles */
        .post-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .post-modal-content {
            background-color: var(--card);
            border-radius: 15px;
            padding: 20px;
            width: 90%;
            max-width: 600px;
            position: relative;
        }

        .post-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .modal-close-btn {
            background: none;
            border: none;
            color: var(--muted);
            font-size: 18px;
            cursor: pointer;
        }

        .modal-close-btn:hover {
            color: var(--link);
        }

        .post-modal-body {
            display: flex;
            flex-direction: column;
        }

        .post-input-container {
            display: flex;
            flex-direction: column;
        }

        .post-input-container textarea {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: 10px;
            padding: 10px;
            font-size: 16px;
            resize: none;
            height: 100px;
            margin-bottom: 10px;
        }

        .divider {
            height: 1px;
            background: var(--border);
            margin: 10px 0;
        }

        .post-modal-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .post-modal-tools {
            display: flex;
        }

        .tool-btn {
            background: none;
            border: none;
            color: var(--link);
            font-size: 18px;
            cursor: pointer;
            margin-right: 15px;
            transition: var(--transition);
        }

        .tool-btn:hover {
            transform: scale(1.1);
        }

        .modal-post-btn {
            background-color: var(--btn);
            color: var(--btn-text);
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .modal-post-btn:hover {
            background-color: var(--link);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="feed-container">
        <!-- Left Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <img src="<?php echo URLROOT; ?>/img/logo_white.png" alt="GRADLINK">
            </div>
            <ul class="sidebar-menu">
                <li><i class="fas fa-home"></i> <span>Home</span></li>
                <li><i class="fas fa-search"></i> <span>Explore</span></li>
                <li>
                    <i class="fas fa-bell"></i> 
                    <span>Notifications</span>
                    <span class="notification-badge">19</span>
                </li>
                <li><i class="fas fa-envelope"></i> <span>Messages</span></li>
                <li><i class="fas fa-bookmark"></i> <span>Bookmarks</span></li>
                <li><i class="fas fa-user"></i> <span>Profile</span></li>
                <li id="logout-btn" onclick="window.location.href='<?php echo URLROOT; ?>/logout'">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </li>
            </ul>
            <button class="post-button">Post</button>
        </div>

        <!-- Main Content -->
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

            <div class="show-more">Show 35 posts</div>

            <div class="feed">
                <!-- IEEE Event Announcement Post (Based on your sample image) -->
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
                        <div><i class="far fa-comment"></i> <?= rand(15, 45) ?></div>
                        <div><i class="fas fa-retweet"></i> <?= rand(30, 120) ?></div>
                        <div><i class="far fa-heart"></i> <?= rand(100, 350) ?></div>
                    </div>
                </div>

                <!-- Internship Opportunity Post -->
                <div class="post">
                    <div class="post-header">
                        <div class="post-user">
                            <img src="<?php echo URLROOT; ?>/img/company-profile.jpg" alt="User" class="profile-photo">
                            <div class="post-user-info">
                                <span class="post-user-name">TechInnovate Solutions</span>
                                <span class="post-user-handle">@TechInnovate</span>
                                <span class="post-time"> · Aug 18</span>
                                <div class="post-content">
                                    <p>We're looking for talented graduates for our Summer Internship Program! Apply now for positions in software development, data science, and UX design. Remote options available. #GradJobs #TechInternship</p>
                                </div>
                            </div>
                        </div>
                        <div class="post-menu">
                            <i class="fas fa-ellipsis-h"></i>
                        </div>
                    </div>
                    
                    <div class="post-actions">
                        <div><i class="far fa-comment"></i> <?= rand(20, 60) ?></div>
                        <div><i class="fas fa-retweet"></i> <?= rand(50, 150) ?></div>
                        <div><i class="far fa-heart"></i> <?= rand(80, 250) ?></div>
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
                        <div><i class="far fa-comment"></i> <?= rand(15, 45) ?></div>
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
                        <div><i class="far fa-comment"></i> <?= rand(30, 80) ?></div>
                        <div><i class="fas fa-retweet"></i> <?= rand(25, 60) ?></div>
                        <div><i class="far fa-heart"></i> <?= rand(200, 500) ?></div>
                    </div>
                </div>

                <!-- Original posts from the template -->
                <?php for ($i = 0; $i < 3; $i++) : ?>
                <div class="post">
                    <div class="post-header">
                        <div class="post-user">
                            <img src="<?php echo URLROOT; ?>/img/post-profile-<?= $i+1 ?>.jpg" alt="User" class="profile-photo">
                            <div class="post-user-info">
                                <span class="post-user-name">University Career Center</span>
                                <span class="post-user-handle">@UniCareerCenter</span>
                                <span class="post-time"> · Aug <?= 15-$i ?></span>
                                <div class="post-content">
                                    <p>Career fair coming up next week! Over 50 companies looking to hire recent graduates. Don't miss this opportunity to network and find your dream job! #CareerFair #GradJobs</p>
                                </div>
                            </div>
                        </div>
                        <div class="post-menu">
                            <i class="fas fa-ellipsis-h"></i>
                        </div>
                    </div>
                    
                    <?php if ($i % 2 == 0) : ?>
                    <div class="post-media">
                        <img src="<?php echo URLROOT; ?>/img/post-image-<?= $i+1 ?>.jpg" alt="Post image">
                    </div>
                    <?php endif; ?>
                    
                    <div class="post-actions">
                        <div><i class="far fa-comment"></i> <?= rand(5, 40) ?></div>
                        <div><i class="fas fa-retweet"></i> <?= rand(10, 100) ?></div>
                        <div><i class="far fa-heart"></i> <?= rand(50, 500) ?></div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="right-sidebar">
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search">
            </div>

            <div class="card">
                <div class="card-title">What's happening</div>
                
                <div class="trend">
                    <div class="trend-category">Career Development · Trending</div>
                    <div class="trend-name">#ResumeWorkshop</div>
                    <div class="trend-posts">25.8K posts</div>
                </div>
                
                <div class="trend">
                    <div class="trend-category">Academia · Trending</div>
                    <div class="trend-name">Graduate Research Funding</div>
                    <div class="trend-posts">62.2K posts</div>
                </div>
                
                <div class="trend">
                    <div class="trend-category">Networking · Trending</div>
                    <div class="trend-name">#AlumniConnections</div>
                    <div class="trend-posts">1,655 posts</div>
                </div>
                
                <div class="trend">
                    <div class="trend-category">Technology · Trending</div>
                    <div class="trend-name">AI in Education</div>
                    <div class="trend-posts">1.47M posts</div>
                </div>
                
                <div class="show-more">Show more</div>
            </div>

            <div class="card">
                <div class="card-title">Who to follow</div>
                
                <div class="follow-card">
                    <div class="follow-info">
                        <img src="<?php echo URLROOT; ?>/img/follow-1.jpg" alt="Profile" class="profile-photo">
                        <div class="follow-details">
                            <div class="follow-name">Tech Careers</div>
                            <div class="follow-handle">@TechCareers</div>
                        </div>
                    </div>
                    <button class="follow-btn">Follow</button>
                </div>
                
                <div class="follow-card">
                    <div class="follow-info">
                        <img src="<?php echo URLROOT; ?>/img/follow-2.jpg" alt="Profile" class="profile-photo">
                        <div class="follow-details">
                            <div class="follow-name">Grad Network</div>
                            <div class="follow-handle">@GradNetwork</div>
                        </div>
                    </div>
                    <button class="follow-btn">Follow</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this post modal popup HTML at the end of the body before the closing body tag -->
    <div class="post-modal" id="postModal">
        <div class="post-modal-content">
            <div class="post-modal-header">
                <button class="modal-close-btn"><i class="fas fa-times"></i></button>
            </div>
            <div class="post-modal-body">
                <img src="<?php echo URLROOT; ?>/img/default-profile.jpg" alt="Profile" class="profile-photo">
                <div class="post-input-container">
                    <textarea placeholder="What's happening?"></textarea>
                    <div class="divider"></div>
                    <div class="post-modal-actions">
                        <div class="post-modal-tools">
                            <button class="attach-btn">Attach</button>
                        </div>
                        <button class="modal-post-btn">Post</button>
                    </div>
                </div>
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
            document.querySelector('.show-more').addEventListener('click', function() {
                // You would typically load more posts here
                this.textContent = 'Loading...';
                setTimeout(() => {
                    this.textContent = 'Show more';
                }, 1000);
            });

            // Post modal functionality
            const postModal = document.getElementById('postModal');
            const postButton = document.querySelector('.post-button');
            const closeModalButton = document.querySelector('.modal-close-btn');

            postButton.addEventListener('click', function() {
                postModal.style.display = 'flex';
            });

            closeModalButton.addEventListener('click', function() {
                postModal.style.display = 'none';
            });

            // Close modal when clicking outside of the modal content
            window.addEventListener('click', function(event) {
                if (event.target === postModal) {
                    postModal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>