<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undergraduate Profile</title>
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
            display: flex;
            flex-direction: column;
            gap: 2em;
            padding: 10px;

            & > .post-creation {
                display: flex;
                flex-direction: column;
                width: 100%;
                height: 100px;
                background-color: var(--card);
                padding: 10px;
                border-radius: 5px;

                & > .post-image-caption {
                    display: flex;
                    width: 100%;
                    height: 70%;
                    background-color: #e6e6e6;
                    padding: 10px;
                    border-radius: 5px;

                    & > .post-image {
                        display: flex;
                        width: 13%;
                        height: 100%;
                        background-color: #595959;
                        margin-right: 10px;
                        border-radius: 5px;
                    }
                    & > .post-caption {
                        display: flex;
                        width: 87%;
                        height: 100%;
                        background-color: #d9d9d9;
                        border-radius: 5px;
                    }
                }
                & > .post-creation-buttons {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 10px;
                    border-radius: 5px;
                }
            }

            & > .badges {
                display: flex;
                flex-direction: column;
                width: 100%;
                height: 100px;
                background-color: var(--card);
                padding: 10px;
                border-radius: 5px;
                gap: 1em;

                & > .badge-title {
                    font-weight: bold;
                }
                & > .badge-list {
                    display: flex;
                    flex-direction: row;
                    justify-content: flex-start;
                    gap: 1em;

                    & > .badge {
                        display: flex;
                        width: 30px;
                        height: 30px;
                        background-color: #595959;
                        border-radius: 5px;
                    }
                }
            }

            & > .analytics {
                display: flex;
                flex-direction: column;
                width: 100%;
                height: 400px;
                background-color: var(--card);
                padding: 10px;
                border-radius: 5px;

                & > .analytics-title {
                    font-weight: bold;
                    font-size: 1.2em;
                }

                & > hr {
                    border: none;
                    height: 1px;
                    background-color: #606060;
                    width: 100%;
                }
                & > .analytics-content {
                    display: flex;
                    flex-direction: column;
                    gap: 1em;

                    & > .analytics-profiles,
                    & > .analytics-post-impressions,
                    & > .analytics-connections {
                        display: flex;
                        flex-direction: row;
                        width: 100%;
                        height: 60px;
                        background-color: var(--input);
                        padding: 10px;
                        border-radius: 5px;
                    }
                }
                & > .analytics-footer {
                    display: flex;
                    height: 40px;
                    justify-content: center;
                    align-items: flex-end;
                    background-color: var(--input);
                    cursor: pointer;
                }
            }
        }

        /* Center panel */
        .center-panel {
            /* background-color: white; */
            display: flex;
            flex-direction: column;
            width: 100%;
            padding: 10px;
            flex: 2;
            gap: 1rem;
            overflow-y: auto;
            max-height: 100vh;
        }

    .profile {
        width: 100%;
        height: 200px;
        position: relative;
    }
    .profile > .profile-up-part {
        width: 100%;
        height: 50%;
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
        height: 60%;
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


    

    & > .posts-section {
        display: flex;
        flex-direction: column;
        gap: 1em;
        width: 100%;
        margin-top: 1em;
        background: #f7f7f7;
        border-radius: 8px;
        padding: 20px 10px;
        min-height: 200px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
    }

    & > .info-section {
        display: flex;
        flex-direction: column;
        gap: 1em;
        width: 100%;
        margin-top: 1em;
        background: #f7f7f7;
        border-radius: 8px;
        padding: 20px 10px;
        min-height: 200px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
    }

    & .post-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        min-height: 60px;
        background-color: var(--card);
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 1em;
    }
    & .post-card-actions {
    display: flex;
    width: 100%;
    height: 50px;
}

& .post-card-action-button {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    background-color: var(--surface-3);
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
}

& .post-card-action-button:hover {
    background-color: var(--primary);
}

& .post-card-like-button,
& .post-card-dislike-button {
    flex: 1;
    color: var(--text);
}

& .post-card-comment-button {
    flex: 3;
    justify-content: center;
    color: var(--text);
}

& .project-card,
& .certificate-card {
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

    & .post-card-image {
        width: 80%;
        aspect-ratio: 4 / 3;
        background-color: var(--input);
        
        margin-bottom: 10px;
    }
    & .project-card-image,
    & .certificate-card-image {
        width: 60px;
        height: 60px;
        background-color: #595959;
        margin-right: 10px;
        border-radius: 5px;
    }

    & .post-card-title,
    & .project-card-title,
    & .certificate-card-title {
        font-weight: bold;
        color: var(--muted);
    }


        /* right side panel */
        .right-panel {
            /* background-color: white; */
            flex: 1;
            width: 100%;
            padding: 10px;

            & > .events {
                display: flex;
                flex-direction: column;
                width: 100%;
                height: 400px;
                background-color: var(--card);
                padding: 10px;
                border-radius: 5px;

                & > .event-list-title {
                    font-weight: bold;
                    font-size: 1.2em;
                }
                & > hr {
                    border: none;
                    height: 1px;
                    background-color: #606060;
                    width: 100%;
                }
                & > .event-list {
                    display: flex;
                    flex-direction: column;
                    gap: 1em;
                    padding: 10px;

                    & > .event-card {
                        display: flex;
                        flex-direction: row;
                        width: 100%;
                        height: 60px;
                        background-color: var(--input);
                        padding: 10px;
                        border-radius: 5px;

                        & > .event-card-image {
                            display: flex;
                            width: 15%;
                            aspect-ratio: 1/1;
                            background-color: #595959;
                            margin-right: 10px;
                            border-radius: 5px;
                        }
                    }
                }
            }
        }

    </style>
</head>
<body>
    <div class="main-container">
        <div class="left-panel">
            <div class="post-creation">
                <div class="post-image-caption">
                    <div class="post-image"></div>
                    <div class="post-caption">
                        <textarea name="caption" id="caption" rows="2" cols="50">Post something...</textarea>
                    </div>
                </div>
                <div class="post-creation-buttons">
                    <button type="submit">Attach</button>
                    <button type="button">Post</button>
                </div>
            </div>
            <?php
            // Example badges array. Replace with your dynamic data source.
            $badges = [
                ['class' => 'badge'],
                ['class' => 'badge'],
                ['class' => 'badge'],
                // Add or remove badges as needed
            ];
            ?>
            <div class="badges">
                <div class="badge-title">Badges</div>
                <div class="badge-list">
                    <?php foreach ($badges as $badge): ?>
                        <div class="<?php echo htmlspecialchars($badge['class']); ?>"></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="analytics">
            <div class="analytics-title">Analytics</div>
            <div class="analytics-privacy">Private to you</div>
            <hr>
            <div class="analytics-content">
                <div class="analytics-profiles">21 profile views</div>
                <div class="analytics-post-impressions">0 post impressions</div>
                <div class="analytics-connections">40 connections</div>
            </div>
            <div class="analytics-footer">Show all analytics -></div>
        </div>
        </div>

        <div class="center-panel">
            <div class="profile">
                <div class="profile-up-part">
                    <div class="profile-edit-btn">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                </div>
                <div class="profile-down-part">
            <div class="profile-image"></div>
            <div class="profile-name"><?= $data['userDetails']['name'] ?></div>
            <div class="profile-bio"><?= $data['userDetails']['bio'] ?></div>
        </div>
    </div>
    <div class="post-or-info">
        <div class="posts active-tab" id="postsTab" onclick="showTab('posts')">POSTS</div>
        <div class="info" id="infoTab" onclick="showTab('info')">INFO</div>
    </div>

    <!-- Posts Section -->
    <div class="posts-section" id="postsSection">
        <?php foreach ($data['posts'] as $post): ?>
            <div class="post-card">
                <div class="post-card-image"></div>
                <div class="post-card-actions">
                    <button class="post-card-action-button post-card-like-button">
                        <i class="fas fa-thumbs-up"></i>
                    </button>
                    <button class="post-card-action-button post-card-dislike-button">
                        <i class="fas fa-thumbs-down"></i>
                    </button>
                    <button class="post-card-action-button post-card-comment-button">
                        Comment
                    </button>
                </div>
                <div>
                    <div class="post-card-title"><?= htmlspecialchars($post['title']) ?></div>
                    <div class="post-card-content"><?= htmlspecialchars($post['content']) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Info Section: Certificates and Projects -->
    <div class="info-section" id="infoSection" style="display:none;">
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


        <div class="right-panel">
            <div class="events">
            <div class="event-list-title">Upcoming Events</div>
            <hr>

            <!-- Event Section -->
            <div class="event-list">
                <?php foreach ($data['events'] as $event): ?>
                    <div class="event-card">
                        <div class="event-card-image"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
