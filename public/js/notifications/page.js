class Notification_page_manager{
    constructor(options = {}) {
        this.urlRoot = options.urlRoot || '';
        this.pollInterval = options.pollInterval || 60000; // 1 minute default
        this.listElement = null;
        this.isPolling = false;
        this.currentCount = 0;

        this.init();
    }


    init(){
        // Find DOM elements
        this.listElement = document.querySelector('.notification-container');
        this.fetchAndRenderNotifications();
            
        // Initial fetch
        // this.startPolling();
    }

    startPolling() {
        if (this.isPolling) return;
        this.isPolling = true;
        this.fetchNotifications();
        this.pollingIntervalId = setInterval(() => this.fetchNotifications(), this.pollInterval); 
        this.listenFotClicksAndPausePolling();  
    }

    async fetchAndRenderNotifications(){
        try{
            const response = await fetch(`${this.urlRoot}/notification/fetchNewNotifications`,{
                headers: {'X-Requested-With': `XMLHttpRequest`}
            });

            const data = await response.json();
            if (data.success){
                this.renderNotifications(data.notifications);
            }
        }catch(error){
            console.error('Error fetching notifications:', error);
            this.showError('Failed to load notifications.');
        }
    }

    // listenFotClicksAndPausePolling(){
    //     if (!this.listElement) return;
    //     this.listElement.addEventListener('click', () => {
    //         if (this.isPolling){
    //             clearInterval(this.pollingIntervalId);
    //             this.isPolling = false;
    //         }
    //     });
    // }


    renderNotifications(notifications){
        if (!this.listElement) return;
        if (!notifications || notifications.length === 0){
            this.listElement.innerHTML = this.showNotificationsHTML();
            return;
        }else{
            notifications.forEach(notification => {
                this.listElement.innerHTML += this.createNotificationHTML(notification);
            });
        }
    }

    // HTML Templates
    showNotificationsHTML(){
        return `
            <div class="no-notifications">
                <i class="fas fa-bell-slash"></i>
                <p>No notifications yet</p>
            </div>
        `;
    }

    createNotificationHTML(notification){
        return `
            <div class="notification-item ${notification.read ? 'read' : 'unread'}" data-notification-id="${notification.id}">
                <div class="notification-icon">
                    <i class="${notification.icon || 'fas fa-info-circle'}"></i>
                </div>
                <div class="notification-content">
                    <p class="notification-message">${notification.message}</p>
                    <span class="notification-timestamp">${notification.timestamp}</span>
                </div>
            </div>
        `;
    }

}