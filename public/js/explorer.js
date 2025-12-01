

class Explorer {
    constructor(options = {}) {
        this.currentFilter = options.currentFilter || 'all';
        this.urlRoot = options.urlRoot || window.URLROOT;
        this.searchTimeout = null;
        this.init();
    }

    init() {
        this.searchInput = document.querySelector('.search-input');
        this.searchResults = document.querySelector('.search-results');
        this.filterTabs = document.querySelectorAll('.filter-tab');
        
        if (!this.searchInput || !this.searchResults) return;

        // Focus on the search input when the page loads if no query exists
        if (this.searchInput.value === '') {
            this.searchInput.focus();
        }

        // Attach event listeners
        this.attachSearchListener();
        this.attachFilterListeners();
        this.attachPostCardListeners();
    }

    attachSearchListener() {
        this.searchInput.addEventListener('input', (e) => {
            clearTimeout(this.searchTimeout);
            const query = e.target.value.trim();

            if (query.length === 0) {
                this.showNoQueryMessage();
                return;
            }

            if (query.length > 2) {
                this.searchTimeout = setTimeout(() => {
                    this.performSearch(query);
                }, 500); // 500ms delay to reduce API calls
            }
        });

        // Handle form submission
        const searchForm = document.querySelector('.search-bar');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const query = this.searchInput.value.trim();
                if (query.length > 0) {
                    this.performSearch(query);
                }
            });
        }
    }

    attachFilterListeners() {
        this.filterTabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                const query = this.searchInput.value.trim();
                
                // If there's a query, prevent default and use AJAX
                if (query.length > 0) {
                    e.preventDefault();
                    const url = new URL(e.target.href);
                    const filter = url.searchParams.get('filter') || 'all';
                    this.currentFilter = filter;
                    this.performSearch(query);
                    
                    // Update active tab
                    this.filterTabs.forEach(t => t.classList.remove('active'));
                    e.target.classList.add('active');
                }
                // If no query, let the link navigate normally (don't prevent default)
            });
        });
    }

    performSearch(query) {
        // Show loading state
        this.showLoading();

        // Update the URL with the new search query without reloading the page
        const newUrl = new URL(window.location.href);
        newUrl.searchParams.set('q', query);
        newUrl.searchParams.set('filter', this.currentFilter);
        history.replaceState(null, '', newUrl.toString());

        // Perform AJAX search
        fetch(`${this.urlRoot}/explore/search?q=${encodeURIComponent(query)}&filter=${this.currentFilter}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                this.updateSearchResults(data.results, query);
            } else {
                this.showError('Search failed. Please try again.');
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            this.showError('An error occurred while searching. Please try again.');
        });
    }

    updateSearchResults(results, query) {
        // Clear loading state
        this.searchResults.innerHTML = '';

        // Check if there are any results
        const hasResults = this.checkHasResults(results);

        if (!hasResults) {
            this.showNoResults(query);
            return;
        }

        // For 'all' filter, limit to 2 results per section
        const isAllFilter = this.currentFilter === 'all';

        // Render users first (People)
        if ((this.currentFilter === 'all' || this.currentFilter === 'users' || this.currentFilter === 'alumni' || this.currentFilter === 'undergrad') && results.users) {
            const users = this.getUsersFromResults(results.users);
            if (users && users.length > 0) {
                const usersToShow = isAllFilter ? users.slice(0, 2) : users;
                this.renderUsers(usersToShow);
            }
        }

        // Render events second
        if ((this.currentFilter === 'all' || this.currentFilter === 'events') && results.events && results.events.length > 0) {
            const eventsToShow = isAllFilter ? results.events.slice(0, 2) : results.events;
            this.renderEvents(eventsToShow);
        }

        // Render posts last
        if ((this.currentFilter === 'all' || this.currentFilter === 'posts') && results.posts && results.posts.length > 0) {
            const postsToShow = isAllFilter ? results.posts.slice(0, 2) : results.posts;
            this.renderPosts(postsToShow);
        }
    }

    checkHasResults(results) {
        if (results.posts && results.posts.length > 0) return true;
        if (results.events && results.events.length > 0) return true;
        if (results.users) {
            const users = this.getUsersFromResults(results.users);
            if (users && users.length > 0) return true;
        }
        return false;
    }

    getUsersFromResults(usersObj) {
        if (Array.isArray(usersObj)) return usersObj;
        if (usersObj.all) return usersObj.all;
        if (usersObj.alumni) return usersObj.alumni;
        if (usersObj.undergrad) return usersObj.undergrad;
        return [];
    }

    renderPosts(posts) {
        const section = document.createElement('div');
        section.className = 'results-section';
        
        const header = `
            <div class="results-header">
                <h2>Posts</h2>
                ${this.currentFilter === 'all' ? `<a href="${this.urlRoot}/explore?q=${encodeURIComponent(this.searchInput.value)}&filter=posts" class="view-all">View all</a>` : ''}
            </div>
        `;
        
        section.innerHTML = header;
        
        const postCards = document.createElement('div');
        postCards.className = 'post-cards';
        postCards.id = 'posts-feed';
        
        posts.forEach(post => {
            const postCard = this.createPostCard(post);
            postCards.appendChild(postCard);
        });
        
        section.appendChild(postCards);
        this.searchResults.appendChild(section);
    }

    createPostCard(post) {
        const card = document.createElement('div');
        card.className = 'post-card';
        card.setAttribute('data-post-id', post.id);
        
        const alumniStar = post.role === 'alumni' ? '<span class="alumni-badge">★</span>' : '';
        const likedClass = post.is_liked ? 'liked' : '';
        const likeIcon = post.is_liked ? 'fas' : 'far';
        
        card.innerHTML = `
            <div class="post-card-header">
                <img src="${this.urlRoot}/media/profile/${post.profile_image || 'default.jpg'}"
                    alt="${this.escapeHtml(post.name)}" 
                    class="post-author-avatar"
                    onerror="this.onerror=null;this.src='${this.urlRoot}/media/profile/default.jpg';"
                    onclick="window.location.href='${this.urlRoot}/profile?userid=${post.user_id}';">
                <div class="post-author-info">
                    <div class="post-author-name" onclick="window.location.href='${this.urlRoot}/profile?userid=${post.user_id}';">
                        ${this.escapeHtml(post.name)}
                        ${alumniStar}
                    </div>
                    <div class="post-meta">
                        <span class="post-handle">@user${post.user_id}</span>
                        <span class="post-time">${new Date(post.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>
                    </div>
                </div>
            </div>
            <div class="post-card-content">
                <p class="post-text">${this.escapeHtml(post.content).replace(/\n/g, '<br>')}</p>
                ${post.image ? `
                    <div class="post-image-container">
                        <img src="${this.urlRoot}/media/post/${post.image}"
                            alt="Post image" 
                            class="post-image"
                            onerror="this.parentElement.style.display='none';">
                    </div>
                ` : ''}
            </div>
            <div class="post-card-footer">
                <div class="post-stats">
                    <button class="post-action-btn like-btn ${likedClass}" 
                            data-post-id="${post.id}">
                        <i class="${likeIcon} fa-heart"></i>
                        <span class="count like-count">${post.likes || 0}</span>
                    </button>
                    <button class="post-action-btn comment-btn" data-post-id="${post.id}">
                        <i class="far fa-comment"></i>
                        <span class="count comment-count">${post.comments || 0}</span>
                    </button>
                </div>
            </div>
            <div class="pc-comments" style="display:none;border-top:1px solid var(--border);margin-top:10px;padding-top:8px">
                <div class="pc-comments-list" style="max-height:200px;overflow:auto;color:var(--text-secondary)"></div>
                <div style="display:flex;gap:6px;margin-top:6px">
                    <input type="text" class="pc-comment-input" placeholder="Add a comment" style="flex:1;padding:6px;border:1px solid var(--border);background:var(--bg);color:var(--text);border-radius:4px" />
                    <button class="pc-comment-send" style="padding:6px 10px;background:var(--link);color:var(--text);border:none;border-radius:4px;cursor:pointer">Send</button>
                </div>
            </div>
        `;
        
        // Event listeners are handled via delegation in attachPostCardListeners()
        return card;
    }

    renderUsers(users) {
        const section = document.createElement('div');
        section.className = 'results-section';
        
        let title = 'People';
        if (this.currentFilter === 'alumni') title = 'Alumni';
        if (this.currentFilter === 'undergrad') title = 'Undergraduates';
        
        const header = `
            <div class="results-header">
                <h2>${title}</h2>
                ${this.currentFilter === 'all' ? `<a href="${this.urlRoot}/explore?q=${encodeURIComponent(this.searchInput.value)}&filter=users" class="view-all">View all</a>` : ''}
            </div>
        `;
        
        section.innerHTML = header;
        
        const userCards = document.createElement('div');
        userCards.className = 'user-cards';
        userCards.id = 'users-list';
        
        users.forEach(user => {
            const userCard = this.createUserCard(user);
            userCards.appendChild(userCard);
        });
        
        section.appendChild(userCards);
        this.searchResults.appendChild(section);
    }

    createUserCard(user) {
        const card = document.createElement('div');
        card.className = 'user-card';
        card.setAttribute('data-user-id', user.id);
        
        const roleBadge = user.role === 'alumni' ? ' ★' : '';
        
        card.innerHTML = `
            <img src="${this.urlRoot}/media/profile/${user.profile_image || 'default.jpg'}"
                alt="${this.escapeHtml(user.name)}" 
                class="user-avatar"
                onerror="this.onerror=null;this.src='${this.urlRoot}/media/profile/default.jpg';">
            <div class="user-info">
                <div class="user-name" title="${this.escapeHtml(user.name)}">
                    ${this.escapeHtml(user.name)}
                </div>
                <span class="user-role ${user.role}">
                    ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}${roleBadge}
                </span>
            </div>
        `;
        
        card.onclick = () => {
            window.location.href = `${this.urlRoot}/profile?userid=${user.id}`;
        };
        
        return card;
    }

    renderEvents(events) {
        const section = document.createElement('div');
        section.className = 'results-section';
        
        const header = `
            <div class="results-header">
                <h2>Events</h2>
                ${this.currentFilter === 'all' ? `<a href="${this.urlRoot}/explore?q=${encodeURIComponent(this.searchInput.value)}&filter=events" class="view-all">View all</a>` : ''}
            </div>
        `;
        
        section.innerHTML = header;
        
        const eventCards = document.createElement('div');
        eventCards.className = 'event-cards';
        eventCards.id = 'events-list';
        
        events.forEach(event => {
            const eventCard = this.createEventCard(event);
            eventCards.appendChild(eventCard);
        });
        
        section.appendChild(eventCards);
        this.searchResults.appendChild(section);
    }

    createEventCard(event) {
        const card = document.createElement('div');
        card.className = 'event-card';
        card.setAttribute('data-event-id', event.id);
        
        const eventDate = new Date(event.start_datetime);
        const day = eventDate.getDate();
        const month = eventDate.toLocaleDateString('en-US', { month: 'short' });
        const dateTimeFormatted = eventDate.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
        });
        
        const bookmarkIcon = event.is_bookmarked ? 'fas fa-bookmark' : 'far fa-bookmark';
        const bookmarkClass = event.is_bookmarked ? 'bookmarked' : '';
        
        card.innerHTML = `
            ${event.attachment_image ? `
                <div class="event-image" style="background-image: url('${this.urlRoot}/media/event/${event.attachment_image}')">
                    <div class="event-date">
                        <div class="date-day">${day}</div>
                        <div class="date-month">${month}</div>
                    </div>
                </div>
            ` : ''}
            <div class="event-content">
                <h3 class="event-title">
                    <a href="${this.urlRoot}/calender/show/${event.id}">
                        ${this.escapeHtml(event.title)}
                    </a>
                </h3>
                <p class="event-datetime">
                    <i class="fas fa-clock"></i>
                    ${dateTimeFormatted}
                </p>
                ${event.venue ? `
                    <p class="event-location">
                        <i class="fas fa-map-marker-alt"></i>
                        ${this.escapeHtml(event.venue)}
                    </p>
                ` : ''}
                ${event.description ? `
                    <p class="event-description">
                        ${this.escapeHtml(event.description.substring(0, 150))}${event.description.length > 150 ? '...' : ''}
                    </p>
                ` : ''}
            </div>
            <div class="event-footer">
                <div class="event-organizer">
                    <span class="organizer-name">
                        <i class="fas fa-user"></i>
                        ${this.escapeHtml(event.organizer_name || 'Unknown')}
                    </span>
                </div>
                <div class="event-actions">
                    <button class="bookmark-btn ${bookmarkClass}" data-event-id="${event.id}" onclick="window.explorer.toggleBookmark(${event.id})">
                        <i class="${bookmarkIcon}"></i>
                    </button>
                    <a href="${this.urlRoot}/calender/show/${event.id}" class="details-btn">
                        Details
                    </a>
                </div>
            </div>
        `;
        
        return card;
    }

    showLoading() {
        this.searchResults.innerHTML = `
            <div class="loading-state">
                <i class="fas fa-spinner fa-spin" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                <p>Searching...</p>
            </div>
        `;
    }

    showNoResults(query) {
        this.searchResults.innerHTML = `
            <div class="no-results">
                <i class="fas fa-search" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                <h3>No results found for "${this.escapeHtml(query)}"</h3>
                <p class="search-tip">
                    <i class="fas fa-lightbulb"></i> Try different keywords or check different filters
                </p>
            </div>
        `;
    }

    showNoQueryMessage() {
        this.searchResults.innerHTML = `
            <div class="no-results">
                <i class="fas fa-search" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                <h3>Start exploring by searching for something</h3>
                <p class="search-tip">
                    <i class="fas fa-lightbulb"></i> Try searching for posts, people, or events
                </p>
            </div>
        `;
    }

    showError(message) {
        this.searchResults.innerHTML = `
            <div class="error-state">
                <i class="fas fa-exclamation-circle" style="font-size: 48px; margin-bottom: 15px; color: #e74c3c;"></i>
                <h3>Oops! Something went wrong</h3>
                <p>${this.escapeHtml(message)}</p>
            </div>
        `;
    }

    // Attach event listeners to post cards using event delegation
    attachPostCardListeners() {
        // Use event delegation on the search results container
        this.searchResults.addEventListener('click', async (e) => {
            const target = e.target;
            
            // Handle like button clicks
            const likeBtn = target.closest('.like-btn');
            if (likeBtn) {
                const postId = likeBtn.getAttribute('data-post-id');
                const card = likeBtn.closest('.post-card');
                if (postId && card) {
                    await this.toggleLike(postId, card);
                }
                return;
            }
            
            // Handle comment button clicks
            const commentBtn = target.closest('.comment-btn');
            if (commentBtn) {
                const postId = commentBtn.getAttribute('data-post-id');
                const card = commentBtn.closest('.post-card');
                if (postId && card) {
                    await this.toggleCommentPanel(postId, card);
                }
                return;
            }
            
            // Handle comment send button clicks
            const sendBtn = target.closest('.pc-comment-send');
            if (sendBtn) {
                const card = sendBtn.closest('.post-card');
                const input = card.querySelector('.pc-comment-input');
                const list = card.querySelector('.pc-comments-list');
                const postId = card.getAttribute('data-post-id');
                if (postId && input && list) {
                    await this.sendComment(postId, input, list, card);
                }
                return;
            }
            
            // Handle comment author clicks
            const commentAuthor = target.closest('.comment-author');
            if (commentAuthor) {
                const userId = commentAuthor.getAttribute('data-user-id');
                if (userId) {
                    window.location.href = `${this.urlRoot}/profile?userid=${userId}`;
                }
                return;
            }
        });
        
        // Handle Enter key in comment inputs
        this.searchResults.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                const input = e.target.closest('.pc-comment-input');
                if (input) {
                    e.preventDefault();
                    const card = input.closest('.post-card');
                    const list = card.querySelector('.pc-comments-list');
                    const postId = card.getAttribute('data-post-id');
                    if (postId && list) {
                        this.sendComment(postId, input, list, card);
                    }
                }
            }
        });
    }

    async toggleCommentPanel(postId, card) {
        const commentPanel = card.querySelector('.pc-comments');
        const commentsList = card.querySelector('.pc-comments-list');
        
        if (!commentPanel || !commentsList) return;
        
        const isVisible = commentPanel.style.display !== 'none';
        commentPanel.style.display = isVisible ? 'none' : 'block';
        
        if (!isVisible && !commentPanel.dataset.loaded) {
            commentsList.innerHTML = 'Loading...';
            await this.loadComments(postId, commentsList, card);
            commentPanel.dataset.loaded = '1';
        }
    }

    // Action methods
    async toggleLike(postId, card) {
        // Find card if not provided
        if (!card) {
            card = document.querySelector(`.post-card[data-post-id="${postId}"]`);
        }
        
        const likeBtn = card.querySelector('.like-btn');
        if (!likeBtn) return;

        try {
            // Disable button to prevent double-clicks
            likeBtn.style.pointerEvents = 'none';

            const icon = likeBtn.querySelector('i');
            const countSpan = likeBtn.querySelector('.like-count');

            const response = await fetch(`${this.urlRoot}/post/like/${postId}`);
            const data = await response.json();

            if (data.status === 'error') {
                console.error('Like error:', data.message);
                return;
            }

            const liked = data.status === 'liked';
            likeBtn.classList.toggle('liked', liked);
            icon.classList.toggle('fas', liked);
            icon.classList.toggle('far', !liked);

            let count = parseInt(countSpan.textContent || '0', 10);
            count = liked ? count + 1 : Math.max(0, count - 1);
            countSpan.textContent = count;
        } catch (error) {
            console.error('Like action error:', error);
        } finally {
            // Re-enable button
            if (likeBtn) likeBtn.style.pointerEvents = '';
        }
    }

    async loadComments(postId, listElement, card) {
        try {
            const response = await fetch(`${this.urlRoot}/post/comments/${postId}`);
            const comments = await response.json();
            this.renderComments(comments, listElement, card);
        } catch (error) {
            console.error('Error loading comments:', error);
            listElement.innerHTML = '<em style="color:var(--muted)">Error loading comments</em>';
        }
    }

    renderComments(comments, listElement, card) {
        if (!Array.isArray(comments) || comments.length === 0) {
            listElement.innerHTML = '<em style="color:var(--muted)">No comments yet</em>';
            return;
        }

        listElement.innerHTML = comments.map(comment => {
            const relTime = this.getRelativeTime(comment.created_at);
            const star = comment.role === 'alumni' ? ' ★' : comment.role === 'admin' ? ' ★★' : '';
            const commentText = this.escapeHtml(comment.content || '');
            
            return `
                <div class="comment-item" style="margin-bottom:10px;padding:8px;background:var(--card);border-radius:4px">
                    <div class="bubble">
                        <strong class="comment-author" data-user-id="${comment.user_id}" style="cursor:pointer;color:var(--text)">
                            ${this.escapeHtml(comment.name || 'User')}${star}
                        </strong>
                        <br>
                        <span class="comment-text" style="color:var(--text-secondary);font-size:13px">${commentText}</span>
                        <span class="meta" style="color:var(--muted);font-size:11px;margin-left:8px">${relTime}</span>
                    </div>
                </div>
            `;
        }).join('');
    }

    async sendComment(postId, inputElement, listElement, card) {
        const text = inputElement.value.trim();
        if (!text) return;

        try {
            inputElement.disabled = true;
            
            const formData = new FormData();
            formData.append('content', text);

            const response = await fetch(`${this.urlRoot}/post/comment/${postId}`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            
            if (data.comments) {
                inputElement.value = '';
                this.renderComments(data.comments, listElement, card);
                
                // Update comment count
                const countSpan = card.querySelector('.comment-count');
                if (countSpan) {
                    countSpan.textContent = data.comments.length;
                }
            }
        } catch (error) {
            console.error('Error sending comment:', error);
        } finally {
            inputElement.disabled = false;
        }
    }

    getRelativeTime(timestamp) {
        if (!timestamp) return '';
        const date = new Date(timestamp.replace(' ', 'T'));
        const diff = (Date.now() - date.getTime()) / 1000;
        
        if (diff < 60) return Math.max(1, Math.floor(diff)) + 's';
        if (diff < 3600) return Math.floor(diff / 60) + 'm';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h';
        if (diff < 604800) return Math.floor(diff / 86400) + 'd';
        if (diff < 2629800) return Math.floor(diff / 604800) + 'w';
        return Math.floor(diff / 2629800) + 'mo';
    }

    toggleFollow(userId) {
        // TODO: Implement follow/unfollow functionality
        console.log('Toggle follow for user:', userId);
        // This should make an AJAX call to the follow controller
    }

    toggleBookmark(eventId) {
        // TODO: Implement bookmark/unbookmark functionality
        console.log('Toggle bookmark for event:', eventId);
        // This should make an AJAX call to the event bookmark controller
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize explorer when DOM is ready
let explorer;
document.addEventListener('DOMContentLoaded', function() {
    const currentFilter = new URLSearchParams(window.location.search).get('filter') || 'all';
    explorer = new Explorer({
        currentFilter: currentFilter,
        urlRoot: window.URLROOT
    });
    
    // Make explorer globally accessible
    window.explorer = explorer;
});