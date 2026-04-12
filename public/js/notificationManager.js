/**
 * Notification Manager
 * Handles fetching, displaying, and updating notifications
 */

class NotificationManager {
    constructor(options = {}) {
        this.urlRoot = options.urlRoot || '';
        this.pollInterval = options.pollInterval || 60000; // 1 minute default
        this.badgeElement = null;
        this.modalElement = null;
        this.listElement = null;
        this.isPolling = false;
        this.currentCount = 0;
        
        this.init();
    }

    init() {
        // Find DOM elements
        this.badgeElement = document.getElementById('notificationBadge');
        this.modalElement = document.getElementById('notificationModal');
        this.listElement = document.getElementById('notificationList');
        
        // Initial fetch
        this.fetchCount();
        
        // Start polling
        this.startPolling();
        
        // Setup modal close handlers
        this.setupModalHandlers();
    }

    setupModalHandlers() {
        // Close button handler
        const closeBtn = document.getElementById('closeNotificationModal');
        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.closeModal();
            });
        }

        // Close on outside click - redirect to notifications page
        if (this.modalElement) {
            this.modalElement.addEventListener('click', (e) => {
                if (e.target === this.modalElement) {
                    this.closeModal();
                }
            });
        }

        // Mark all as read button
        const markAllBtn = document.getElementById('markAllReadBtn');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', () => this.markAllAsRead());
        }
    }

    async fetchCount() {
        try {
            const response = await fetch(`${this.urlRoot}/notification/count`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.success) {
                this.updateBadge(data.count);
            }
        } catch (error) {
            console.error('Failed to fetch notification count:', error);
        }
    }

    async fetchNotifications() {
        try {
            const response = await fetch(`${this.urlRoot}/notification/fetchNewNotifications`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.success) {
                this.renderNotifications(data.notifications);
            }
        } catch (error) {
            console.error('Failed to fetch notifications:', error);
            this.showError('Failed to load notifications');
        }
    }

    updateBadge(count) {
        this.currentCount = count;
        
        if (this.badgeElement) {
            if (count > 0) {
                this.badgeElement.textContent = count > 99 ? '99+' : count;
                this.badgeElement.style.display = 'flex';
            } else {
                this.badgeElement.style.display = 'none';
            }
        }
    }

    renderNotifications(notifications) {
        if (!this.listElement) return;

        if (!notifications || notifications.length === 0) {
            this.listElement.innerHTML = `
                <div class="no-notifications">
                    <i class="fas fa-bell-slash"></i>
                    <p>No notifications yet</p>
                </div>
            `;
            return;
        }

        this.listElement.innerHTML = notifications.map(notification => 
            this.createNotificationHTML(notification)
        ).join('');
    }
    createNotificationHTML(notification) {
        const content = notification.content || {};
        const isUnread = !notification.is_read;
        const timeAgo = this.formatTimeAgo(notification.created_at);
        
        // Determine icon based on type
        const iconMap = {
            'like': 'heart',
            'comment': 'comment',
            'follow_request': 'user-plus',
            'started_following': 'user-check',
            'new_message': 'envelope',
            'event_update': 'calendar-day',
            'post_approval': 'check-circle',
            'fundraiser_update': 'hand-holding-heart',
            'system_announcement': 'bullhorn',
            'admin_message': 'shield-alt'
        };
        
        const icon = iconMap[notification.type] || 'bell';
        const text = typeof content === 'object' ? (content.text || 'New notification') : content;
        
        // Special handling for follow_request notifications
        if (notification.type === 'follow_request') {
            // Use reference_id which is the requester's user_id
            const requesterId = notification.reference_id;
            const processed = notification.is_read; // If read, it means it was already processed
            
            // If already processed (read), don't show buttons
            if (processed) {
                return `
                    <div class="notification-item read" 
                         data-notification-id="${notification.id}"
                         data-type="${notification.type}"
                         data-reference-id="${notification.reference_id}"
                         data-requester-id="${requesterId}">
                        <div class="notification-icon ${notification.type}">
                            <i class="fas fa-${icon}"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-text">${this.escapeHTML(text)}</div>
                            <div class="notification-time">${timeAgo}</div>
                            <div class="notification-processed" style="margin-top: 4px; font-size: 12px; color: #999; font-style: italic;">
                                Already processed
                            </div>
                        </div>
                    </div>
                `;
            }
            
            return `
                <div class="notification-item ${isUnread ? 'unread' : 'read'}" 
                     data-notification-id="${notification.id}"
                     data-type="${notification.type}"
                     data-reference-id="${notification.reference_id}"
                     data-requester-id="${requesterId}">
                    <div class="notification-icon ${notification.type}">
                        <i class="fas fa-${icon}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-text">${this.escapeHTML(text)}</div>
                        <div class="notification-time">${timeAgo}</div>
                        <div class="notification-actions" style="margin-top: 8px; display: flex; gap: 8px;">
                            <button class="btn-approve" 
                                    onclick="event.stopPropagation(); window.notificationManager && window.notificationManager.handleFollowRequest(${requesterId}, 'approve', ${notification.id})"
                                    style="padding: 4px 12px; background: var(--primary-color, #4CAF50); color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                Accept
                            </button>
                            <button class="btn-reject" 
                                    onclick="event.stopPropagation(); window.notificationManager && window.notificationManager.handleFollowRequest(${requesterId}, 'reject', ${notification.id})"
                                    style="padding: 4px 12px; background: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                Reject
                            </button>
                        </div>
                    </div>
                    ${isUnread ? '<div class="unread-indicator"></div>' : ''}
                </div>
            `;
        }
        
        // Special handling for new_message notifications - click to redirect to messages
        if (notification.type === 'new_message') {
            const senderId = notification.reference_id;
            return `
                <div class="notification-item ${isUnread ? 'unread' : 'read'}" 
                     data-notification-id="${notification.id}"
                     data-type="${notification.type}"
                     data-reference-id="${notification.reference_id}"
                     onclick="window.location='${this.urlRoot}/messages?user=${senderId}'"
                     style="cursor: pointer;">
                    <div class="notification-icon ${notification.type}">
                        <i class="fas fa-${icon}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-text">${this.escapeHTML(text)}</div>
                        <div class="notification-time">${timeAgo}</div>
                    </div>
                    ${isUnread ? '<div class="unread-indicator"></div>' : ''}
                </div>
            `;
        }
        
        // Standard notification
        return `
            <div class="notification-item ${isUnread ? 'unread' : 'read'}" 
                 data-notification-id="${notification.id}"
                 data-type="${notification.type}"
                 data-reference-id="${notification.reference_id}"
                 onclick="${content.link ? `window.location='${this.urlRoot + content.link}'` : ''}">
                <div class="notification-icon ${notification.type}">
                    <i class="fas fa-${icon}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-text">${this.escapeHTML(text)}</div>
                    <div class="notification-time">${timeAgo}</div>
                </div>
                ${isUnread ? '<div class="unread-indicator"></div>' : ''}
            </div>
        `;
    }

    formatTimeAgo(timestamp) {
        if (!timestamp) return 'Just now';
        
        const now = new Date();
        const past = new Date(timestamp.replace(' ', 'T'));
        const diffMs = now - past;
        const diffSec = Math.floor(diffMs / 1000);
        const diffMin = Math.floor(diffSec / 60);
        const diffHour = Math.floor(diffMin / 60);
        const diffDay = Math.floor(diffHour / 24);
        
        if (diffSec < 60) return 'Just now';
        if (diffMin < 60) return `${diffMin}m ago`;
        if (diffHour < 24) return `${diffHour}h ago`;
        if (diffDay < 7) return `${diffDay}d ago`;
        
        return past.toLocaleDateString();
    }

    escapeHTML(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async markAsRead(notificationId) {
        try {
            const response = await fetch(`${this.urlRoot}/notification/markAsRead?nId=${notificationId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.success) {
                // Update badge count
                this.fetchCount();
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch(`${this.urlRoot}/notification/markAllAsRead`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.success) {
                // Update UI
                if (this.listElement) {
                    this.listElement.querySelectorAll('.notification-item').forEach(item => {
                        if ((item.dataset.type || '') === 'follow_request') {
                            return;
                        }
                        item.classList.remove('unread');
                        item.classList.add('read');
                        const indicator = item.querySelector('.unread-indicator');
                        if (indicator) indicator.remove();
                    });
                }
                this.updateBadge(0);
            }
        } catch (error) {
            console.error('Failed to mark all as read:', error);
        }
    }

    openModal() {
        if (this.modalElement) {
            this.modalElement.classList.remove('hidden');
            this.modalElement.style.display = 'flex';
            
            // Fetch latest notifications when opening
            this.fetchNotifications();
        }
    }

    closeModal() {
        if (this.modalElement) {
            this.modalElement.classList.add('hidden');
            this.modalElement.style.display = 'none';
        }
    }

    toggleModal() {
        if (this.modalElement && this.modalElement.classList.contains('hidden')) {
            this.openModal();
        } else {
            this.closeModal();
        }
    }

    startPolling() {
        if (this.isPolling) return;
        
        this.isPolling = true;
        this.pollIntervalId = setInterval(() => {
            this.fetchCount();
        }, this.pollInterval);
    }

    stopPolling() {
        if (this.pollIntervalId) {
            clearInterval(this.pollIntervalId);
            this.isPolling = false;
        }
    }

    showError(message) {
        if (this.listElement) {
            this.listElement.innerHTML = `
                <div class="notification-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>${message}</p>
                </div>
            `;
        }
    }

    async handleFollowRequest(requesterId, action, notificationId) {
        try {
            const endpoint = action === 'approve' 
                ? `${this.urlRoot}/profile/approveFollowRequest` 
                : `${this.urlRoot}/profile/rejectFollowRequest`;
            
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ requester_id: requesterId })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Mark notification as read
                await this.markAsRead(notificationId);
                
                // Update the notification to show it's been processed (hide buttons)
                const notifElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (notifElement) {
                    const actionsDiv = notifElement.querySelector('.notification-actions');
                    if (actionsDiv) {
                        actionsDiv.innerHTML = `
                            <div class="notification-processed" style="font-size: 12px; color: #999; font-style: italic;">
                                ${action === 'approve' ? 'Request accepted' : 'Request rejected'}
                            </div>
                        `;
                    }
                    notifElement.classList.remove('unread');
                    notifElement.classList.add('read');
                    const indicator = notifElement.querySelector('.unread-indicator');
                    if (indicator) indicator.remove();
                }
                
                // Update badge count
                this.fetchCount();
                
                // Show success message
                this.showToast(action === 'approve' ? 'Follow request accepted' : 'Follow request rejected', 'success');
            } else {
                this.showToast(data.error || 'Failed to process request', 'error');
            }
        } catch (error) {
            console.error('Failed to handle follow request:', error);
            this.showToast('Failed to process request', 'error');
        }
    }

    showToast(message, type = 'info') {
        // Simple toast notification
        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 20px;
            background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
            color: white;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            z-index: 10000;
            font-size: 14px;
            transition: opacity 0.3s;
        `;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Export for use in other scripts
if (typeof window !== 'undefined') {
    window.NotificationManager = NotificationManager;
}
