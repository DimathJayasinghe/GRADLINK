<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/rightSidebarStyles.css">

<div class="right-sidebar">
    <!-- Upcoming Events Card -->
    <div class="card">
        <div class="card-title">
            <span>Upcoming Events</span>
        </div>
        <div class="event">
            <div class="event-category"><i class="fas fa-users"></i><span>IEEE CS Chapter</span></div>
            <div class="event-name">AI Workshop</div>
            <div class="sidebar-datetime">
                <span class="sidebar-date"><i class="far fa-calendar-alt"></i><span>18 Aug</span></span>
                <span class="sidebar-time"><i class="far fa-clock"></i><span>10:00 AM</span></span>
                <span class="event-location">@S401</span>
            </div>
        </div>
        <div class="event">
            <div class="event-category"><i class="fas fa-university"></i><span>UCSC Alumni</span></div>
            <div class="event-name">Tech Talk: Scaling Systems</div>
            <div class="sidebar-datetime">
                <span class="sidebar-date"><i class="far fa-calendar-alt"></i><span>22 Aug</span></span>
                <span class="sidebar-time"><i class="far fa-clock"></i><span>05:30 PM</span></span>
                <span class="event-location">Online</span>
            </div>
        </div>
        <div class="event">
            <div class="event-category"><i class="fas fa-rocket"></i><span>Startup Club</span></div>
            <div class="event-name">Founder Fireside</div>
            <div class="sidebar-datetime">
                <span class="sidebar-date"><i class="far fa-calendar-alt"></i><span>25 Aug</span></span>
                <span class="sidebar-time"><i class="far fa-clock"></i><span>06:00 PM</span></span>
                <span class="event-location">@W104</span>
            </div>
        </div>
        <div class="show-more">Show more</div>
    </div>

    <!-- Open Fundraisers Card -->
    <div class="card">
        <div class="card-title">
            <span>Open Fundraisers</span>
        </div>
        <div id="fundraisers-container">
            <!-- Loading state -->
            <div class="loading-state" style="padding: 2rem; text-align: center; color: var(--muted);">
                <i class="fas fa-spinner fa-spin" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                <p style="margin: 0;">Loading campaigns...</p>
            </div>
        </div>
        <div class="show-more">
            <a href="<?php echo URLROOT; ?>/fundraiser/all" style="text-decoration: none; color: inherit;">View all</a>
        </div>
    </div>

    <!-- Recently Online section removed per request -->
</div>

<script>
// Load active fundraiser campaigns
(function() {
    const API_URL = '<?php echo URLROOT; ?>/fundraiser/getActiveCampaigns?limit=3';
    const container = document.getElementById('fundraisers-container');
    
    // Fetch campaigns from API
    fetch(API_URL)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.campaigns && data.campaigns.length > 0) {
                // Clear loading state
                container.innerHTML = '';
                
                // Render each campaign
                data.campaigns.forEach(campaign => {
                    const campaignElement = createCampaignElement(campaign);
                    container.appendChild(campaignElement);
                });
            } else {
                // No campaigns found
                container.innerHTML = `
                    <div style="padding: 2rem; text-align: center; color: var(--muted);">
                        <i class="fas fa-hand-holding-heart" style="font-size: 2rem; opacity: 0.3; margin-bottom: 0.5rem;"></i>
                        <p style="margin: 0;">No active campaigns at the moment</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading campaigns:', error);
            container.innerHTML = `
                <div style="padding: 2rem; text-align: center; color: var(--danger);">
                    <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                    <p style="margin: 0;">Failed to load campaigns</p>
                </div>
            `;
        });
    
    // Helper function to create campaign element
    function createCampaignElement(campaign) {
        const eventDiv = document.createElement('div');
        eventDiv.className = 'event';
        eventDiv.style.cursor = 'pointer';

        const status = String(campaign.status || '').toLowerCase();
        const isExpired = campaign.days_left === null || ['completed', 'cancelled', 'rejected'].includes(status);

        if (isExpired) {
            eventDiv.classList.add('is-expired');
        }
        
        // Make it clickable
        eventDiv.onclick = function() {
            window.location.href = '<?php echo URLROOT; ?>/fundraiser/show/' + campaign.id;
        };
        
        // Category/Club section
        const categoryDiv = document.createElement('div');
        categoryDiv.className = 'event-category';
        categoryDiv.innerHTML = `<i class="fas fa-hand-holding-heart"></i><span>${escapeHtml(campaign.club_name)}</span>`;
        
        // Campaign title
        const nameDiv = document.createElement('div');
        nameDiv.className = 'event-name';
        nameDiv.textContent = campaign.title;
        
        // Progress info
        const dateDiv = document.createElement('div');
        dateDiv.className = 'sidebar-event-date';
        
        const raisedAmount = formatCurrency(campaign.raised_amount);
        const targetAmount = formatCurrency(campaign.target_amount);
        const percentage = campaign.percentage;
        
        let progressText = `<i class="fas fa-donate"></i><span>Rs.${raisedAmount} raised of Rs.${targetAmount}</span>`;
        
        dateDiv.innerHTML = progressText;
        
        // Add progress bar
        const progressBar = document.createElement('div');
        progressBar.style.cssText = 'margin-top: 0.5rem; background: rgba(255,255,255,0.1); border-radius: 4px; height: 4px; overflow: hidden;';
        const progressFill = document.createElement('div');
        const progressColor = isExpired ? 'linear-gradient(90deg, #7a7a7a, #5a5a5a)' : 'linear-gradient(90deg, #4caf50, #2e7d32)';
        progressFill.style.cssText = `background: ${progressColor}; height: 100%; width: ${Math.min(percentage, 100)}%; transition: width 0.4s ease;`;
        progressBar.appendChild(progressFill);
        
        // Add days left if applicable
        if (isExpired) {
            const expiredSpan = document.createElement('div');
            expiredSpan.className = 'sidebar-expired-text';
            expiredSpan.innerHTML = '<i class="fas fa-hourglass-end"></i> Expired';
            dateDiv.appendChild(expiredSpan);
        } else if (campaign.days_left !== null) {
            const daysLeftSpan = document.createElement('div');
            daysLeftSpan.style.cssText = 'margin-top: 0.25rem; font-size: 0.8rem; color: var(--muted);';
            daysLeftSpan.innerHTML = `<i class="far fa-clock"></i> ${campaign.days_left} days left`;
            dateDiv.appendChild(daysLeftSpan);
        }
        
        // Assemble the element
        eventDiv.appendChild(categoryDiv);
        eventDiv.appendChild(nameDiv);
        eventDiv.appendChild(dateDiv);
        eventDiv.appendChild(progressBar);
        
        return eventDiv;
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Helper function to format currency
    function formatCurrency(amount) {
        return Number(amount).toLocaleString('en-US', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }
})();
</script>