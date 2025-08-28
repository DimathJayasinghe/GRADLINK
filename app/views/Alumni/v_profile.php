<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Profile</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/color-pallate.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .main-container {
            display: flex;
            width: 100%;
            height: 100vh;
            background-color: var(--bg);
        }
        .left-panel {
            /* background-color: white; */
            width: 100%;
            flex: 1;
        }
        .center-panel {
            /* background-color: lightgray; */
            width: 100%;
            flex: 2;
            overflow-y: auto;
        }
         .center-panel {
            display: flex;
            flex-direction: column;
            width: 100%;
            padding: 10px;
            flex: 2;
            gap: 1rem;
            overflow-y: auto;
            max-height: 100vh;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none;  /* IE and Edge */
        }
        .center-panel::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

    .profile {
        width: 100%;
        min-height: 200px;
        position: relative;
    }
    .profile > .profile-up-part {
        width: 100%;
        height: 30%;
        border-radius: 5px 5px 0 0;
        background-color: var(--card);
        position: relative;
    }
    .profile > .profile-up-part > .profile-edit-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 32px;
        height: 32px;
        background-color: var(--btn);
        color: var(--text);
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: all 0.2s ease;
        z-index: 10;
    }
    .profile > .profile-up-part > .profile-edit-btn:hover {
        background-color: var(--primary);
        transform: translateY(-2px);
    }
    .profile > .profile-down-part {
        width: 100%;
        height: 70%;
        border-radius: 0 0 5px 5px;
        background-color: var(--input);
        position: relative;
    }
    .profile > .profile-down-part > .profile-image {
        display: flex;
        position: absolute;
        z-index: 10;
        top: -30px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 60px;
        background-color: gray;
        border-radius: 5px;
    }
    .profile > .profile-down-part > .profile-name {
        padding-top: 40px;
        font-weight: bold;
        font-size: 1.5em;
        text-align: center;
    }
    .profile > .profile-down-part > .profile-bio {
        font-size: 0.8em;
        max-width: 200px;
        margin: 0 auto 10px;
        text-align: center;
        color: var(--muted);
    }

   /* Fix for the post-or-info section */
    .center-panel > .post-or-info {
        display: flex;
        width: 100%;
        height: 44px;
        gap: 10em;
        padding: 0.5rem;
        background-color: var(--bg);
        border-radius: 0.5rem;
    }

    .center-panel > .post-or-info > .posts {
    display: flex;
    width: 45%;
    height: 100%;
    background-color: var(--btn);
    cursor: pointer;
    font-weight: bold;
    justify-content: center;
    align-items: center;
    border: 2px dashed transparent;
    transition: background-color 0.2s;
    border-radius: 5px;
    color: var(--text);
}

.center-panel > .post-or-info > .posts:hover {
    background-color: var(--link-hover);
}

.center-panel > .posts-section {
    display: flex;
    flex-direction: column;
    gap: 1em;
    width: 100%;
    margin-top: 1em;
    background-color: var(--input);
    border-radius: 8px;
    padding: 20px 10px;
    min-height: 200px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.03);
}

.post-placeholder {
    background-color: var(--card);
    border-radius: 8px;
    min-height: 200px;
    width: 100%;
    display: flex;
    flex-direction: column;
    margin-bottom: 1em;
}

.post-placeholder-image {
    width: 100%;
    aspect-ratio: 1/1;
    background-color: var(--surface-3);
    border-radius: 8px 8px 0 0;
    display: flex;
    justify-content: center;
    align-items: center;
    color: var(--muted);
    font-weight: 500;
}

.post-placeholder-content {
    padding: 15px;
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    min-height: 80px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.post-placeholder-text {
    height: 10px;
    background-color: var(--surface-3);
    border-radius: 4px;
    width: 95%;
}

.post-placeholder-text-short {
    height: 10px;
    background-color: var(--surface-3);
    border-radius: 4px;
    width: 70%;
}

.post-placeholder-actions {
    display: flex;
    width: 100%;
    height: 50px;
}

.post-placeholder-button {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    background-color: var(--surface-3);
}

.post-placeholder-like, 
.post-placeholder-dislike {
    flex: 1;
}

.post-placeholder-comment {
    flex: 3;
}

    

    .center-panel > .post-or-info > .info {
        display: flex;
        width: 45%;
        height: 100%;
        border-radius: 5px;
        background-color: var(--btn);
        cursor: pointer;
        font-weight: bold;
        justify-content: center;
        align-items: center;
        border: 2px dashed transparent;
        transition: background-color 0.2s;
        color: var(--text);
    }

    .center-panel > .post-or-info > .info:hover {
        background-color: var(--link-hover);
    }

    .center-panel > .post-or-info > .active-tab {
        background-color: var(--primary);
        color: var(--surface-0);
    }


    

    
    

    .center-panel > .info-section {
        display: flex;
        flex-direction: column;
        gap: 1em;
        width: 100%;
        margin-top: 1em;
        background: var(--bg);
        border-radius: 8px;
        padding: 20px 10px;
        min-height: 200px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
    }

    


.project-card,
.certificate-card {
    display: flex;
    flex-direction: row;
    align-items: center;
        width: 100%;
        min-height: 60px;
        background-color: var(--card);
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 1em;
    }

    
    .project-card-image,
    .certificate-card-image {
        width: 60px;
        height: 60px;
        background-color: #595959;
        margin-right: 10px;
        border-radius: 5px;
    }

   .project-card-title,
   .certificate-card-title {
       font-weight: bold;
       color: var(--muted);
   }

    

    .right-panel{
        flex: 1;
        width: 100%;
        padding: 10px;
    }
    
</style>
</head>
<body>
    <div class = main-container>
        <div class= left-panel></div>
        <div class= center-panel>
            <div class="profile">
                <div class="profile-up-part">
                    <div class="profile-edit-btn">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                </div>
                <div class="profile-down-part">
            <div class="profile-image"></div>
            <div class="profile-name"></div>
            <div class="profile-bio"></div>
        </div>
    </div>
    <div class="post-or-info">
        <div class="posts active-tab" id="postsTab" onclick="showTab('posts')">POSTS</div>
        <div class="info" id="infoTab" onclick="showTab('info')">INFO</div>
    </div>

    <!-- Posts Section -->
    <div class="posts-section" id="postsSection">
        <!-- Import post section -->
        <div class="post-placeholder">
        <div class="post-placeholder-image">Post Image</div>
        <div class="post-placeholder-content">
            <div class="post-placeholder-text"></div>
            <div class="post-placeholder-text"></div>
            <div class="post-placeholder-text-short"></div>
        </div>
        <div class="post-placeholder-actions">
            <div class="post-placeholder-button post-placeholder-like"></div>
            <div class="post-placeholder-button post-placeholder-dislike"></div>
            <div class="post-placeholder-button post-placeholder-comment"></div>
        </div>
        </div> 
        <!-- Add another placeholder post -->
        <div class="post-placeholder">
        <div class="post-placeholder-image">Post Image</div>
        <div class="post-placeholder-content">
            <div class="post-placeholder-text"></div>
            <div class="post-placeholder-text"></div>
            <div class="post-placeholder-text-short"></div>
        </div>
        <div class="post-placeholder-actions">
            <div class="post-placeholder-button post-placeholder-like"></div>
            <div class="post-placeholder-button post-placeholder-dislike"></div>
            <div class="post-placeholder-button post-placeholder-comment"></div>
        </div>
    </div>
    </div>

    <!-- Info Section: Certificates and Projects -->
    <div class="info-section" id="infoSection" style = display:none>
        <div class="certificates-title" style="font-weight:bold; font-size:1.2em;">Certificates</div>
        <?php foreach ($data['certificates'] as $certificate): ?>
            <div class="certificate-card">
                <div class="certificate-card-image"></div>
                <div class="certificate-card-title"><?= htmlspecialchars($certificate['title']) ?></div>
            </div>
        <?php endforeach; ?>

        <div class="projects-title" style="font-weight:bold; font-size:1.2em; margin-top:1em;">Projects</div>
        <?php foreach ($data['projects'] as $project): ?>
            <div class="project-card">
                <div class="project-card-image"></div>
                <div class="project-card-title"><?= htmlspecialchars($project['title']) ?></div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
    <script>
function showTab(tab) {
    const postsTab = document.getElementById('postsTab');
    const infoTab = document.getElementById('infoTab');
    const postsSection = document.getElementById('postsSection');
    const infoSection = document.getElementById('infoSection');

    if (tab === 'posts') {
        postsTab.classList.add('active-tab');
        infoTab.classList.remove('active-tab');
        postsSection.style.display = '';
        infoSection.style.display = 'none';
    } else {
        infoTab.classList.add('active-tab');
        postsTab.classList.remove('active-tab');
        infoSection.style.display = '';
        postsSection.style.display = 'none';
    }
}
</script>
    <div class="right-panel"></div>
</body>