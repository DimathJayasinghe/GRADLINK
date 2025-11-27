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
                    window.location.href = `${this.urlRoot}/notification`;
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

        // Add click handlers to redirect to notifications page
        this.listElement.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', (e) => {
                // Redirect to notifications page
                window.location.href = `${this.urlRoot}/notification`;
            });
        });
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
            'event_update': 'calendar-day',
            'post_approval': 'check-circle',
            'fundraiser_update': 'hand-holding-heart',
            'system_announcement': 'bullhorn',
            'admin_message': 'shield-alt'
        };
        
        const icon = iconMap[notification.type] || 'bell';
        const text = typeof content === 'object' ? (content.text || 'New notification') : content;
        
        return `
            <div class="notification-item ${isUnread ? 'unread' : 'read'}" 
                 data-notification-id="${notification.id}"
                 data-type="${notification.type}"
                 data-reference-id="${notification.reference_id}">
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
}

// Export for use in other scripts
if (typeof window !== 'undefined') {
    window.NotificationManager = NotificationManager;
}
