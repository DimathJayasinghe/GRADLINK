<?php ob_start(); ?>
<style>
    .fundraise-header {
        margin-bottom: 1rem;
    }

    .fundraise-header h2 {
        margin-bottom: 0.35rem;
    }

    .fundraise-subtitle {
        font-size: 0.92rem;
        color: var(--muted);
        margin-bottom: 1rem;
    }

    .fundraise-controls {
        display: grid;
        grid-template-columns: 1.4fr 1fr 1fr 1fr;
        gap: 0.65rem;
        margin-bottom: 0.75rem;
    }

    .fundraise-control {
        width: 100%;
        padding: 0.55rem 0.7rem;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        background: var(--input);
        color: var(--text);
        font-size: 0.88rem;
    }

    .fundraise-results {
        margin: 0 0 0.85rem;
        font-size: 0.82rem;
        color: var(--muted);
    }

    .cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
        padding-bottom: 20px;
    }

    .card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 0.9rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        color: var(--text);
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        min-height: 205px;
    }

    .card:hover {
        box-shadow: 0 7px 16px rgba(0, 0, 0, 0.14);
        background: rgba(15, 21, 24, 0.55);
    }

    .card.is-hidden {
        display: none;
    }

    .card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.45rem;
    }

    .card-head h3 {
        margin: 0;
        color: var(--link);
        font-size: 0.98rem;
        font-weight: 600;
        line-height: 1.35;
        display: -webkit-box;
        line-clamp: 2;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .status-tag {
        display: inline-flex;
        align-items: center;
        padding: 0.12rem 0.45rem;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 700;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .status-tag.status-approved,
    .status-tag.status-active {
        background: rgba(40, 167, 69, 0.2);
        color: #33c366;
    }

    .status-tag.status-expired {
        background: rgba(255, 255, 255, 0.08);
        color: var(--muted);
    }

    .club-name {
        margin: 0;
        font-size: 0.8rem;
        color: var(--muted);
    }

    .amount-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        color: var(--text);
    }

    .amount-label {
        color: var(--muted);
        margin-right: 0.2rem;
    }

    .progress-container {
        position: relative;
        background: var(--border);
        border-radius: 999px;
        height: 8px;
        overflow: hidden;
        margin-top: 0.1rem;
    }

    .progress-fill {
        display: block;
        background: linear-gradient(90deg, #4caf50, #2e7d32);
        height: 100%;
        border-radius: 999px;
        transition: width 0.35s ease;
    }

    .progress-text {
        margin: 0;
        font-size: 0.77rem;
        color: var(--muted);
    }

    .card-bottom {
        margin-top: auto;
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
    }

    .view-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        padding: 0.45rem 0.65rem;
        background: var(--link);
        color: #fff;
        border-radius: var(--radius-sm);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.82rem;
        transition: background 0.2s ease;
    }

    .view-btn:hover {
        background: #2563eb;
    }

    .report-btn {
        background: #c0394c;
        border: none;
        cursor: pointer;
    }

    .report-btn:hover {
        background: #a93242;
    }

    .fundraise-report-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.62);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1300;
        padding: 1rem;
    }

    .fundraise-report-modal {
        width: min(540px, 100%);
        background: var(--bg-alt, #161b22);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 1rem;
    }

    .fundraise-report-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.8rem;
    }

    .fundraise-report-header h3 {
        margin: 0;
    }

    .fundraise-report-close {
        border: 1px solid var(--border);
        border-radius: 8px;
        background: transparent;
        color: var(--muted);
        cursor: pointer;
        padding: 0.3rem 0.55rem;
    }

    .fundraise-report-group {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        margin-bottom: 0.75rem;
    }

    .fundraise-report-group label {
        font-size: 0.86rem;
        color: var(--muted);
    }

    .fundraise-report-group select,
    .fundraise-report-group textarea,
    .fundraise-report-group input {
        width: 100%;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        background: var(--input, #0f141a);
        color: var(--text);
        padding: 0.55rem 0.7rem;
    }

    .fundraise-report-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.6rem;
        margin-top: 0.7rem;
    }

    .no-campaigns,
    .no-filter-results {
        margin-top: 1rem;
        color: var(--muted);
        font-size: 0.9rem;
    }

    .no-filter-results.is-hidden {
        display: none;
    }

    @media (max-width: 1100px) {
        .fundraise-controls {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 680px) {
        .fundraise-controls {
            grid-template-columns: 1fr;
        }

        .cards-container {
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 10px;
        }
    }
</style>
<?php $styles = ob_get_clean(); ?>

<?php
$sidebar_left = [
    ['label' => 'View All Fundraise Requests', 'url' => '/fundraiser/all', 'active' => true, 'icon' => 'list'],
    ['label' => 'View my Fundraise Requests', 'url' => '/fundraiser/myrequests', 'active' => false, 'icon' => 'user'],
    ['label' => 'Create Fundraise Request', 'url' => '/fundraiser/request', 'active' => false, 'icon' => 'plus-circle'],
]
?>


<?php ob_start(); ?>
<div>
    <div class="fundraise-header">
        <h2>Open Campaigns</h2>
        <p class="fundraise-subtitle">Explore all current fundraisers and support a cause today.</p>
    </div>
    <?php if (!empty($data['fundraise_reqs'])): ?>
        <div class="fundraise-controls">
            <input id="campaignSearch" class="fundraise-control" type="text" placeholder="Search by campaign, club, or headline">
            <select id="campaignStateFilter" class="fundraise-control">
                <option value="all">All states</option>
                <option value="active">Active only</option>
                <option value="expired">Expired only</option>
            </select>
            <select id="campaignProgressFilter" class="fundraise-control">
                <option value="all">All progress</option>
                <option value="low">Below 25%</option>
                <option value="mid">25% - 74%</option>
                <option value="high">75% and above</option>
            </select>
            <select id="campaignSort" class="fundraise-control">
                <option value="newest">Sort: Newest</option>
                <option value="deadline">Sort: Ending Soon</option>
                <option value="progress">Sort: Most Funded</option>
            </select>
        </div>

        <p id="campaignResultCount" class="fundraise-results"></p>

        <div class="cards-container" id="campaignCards">
            <?php
            foreach ($data['fundraise_reqs'] as $req):
                $targetAmount = max(0, (float)($req->target_amount ?? 0));
                $raisedAmount = max(0, (float)($req->raised_amount ?? 0));
                $percentage = $targetAmount > 0 ? min(100, ($raisedAmount / $targetAmount) * 100) : 0;

                $now = new DateTime();
                $deadlineObj = null;
                if (!empty($req->deadline)) {
                    try {
                        $deadlineObj = new DateTime($req->deadline);
                    } catch (Exception $e) {
                        $deadlineObj = null;
                    }
                }
                $expired = $deadlineObj ? ($deadlineObj < $now) : false;
                $daysLeft = $deadlineObj ? max(0, (int)$now->diff($deadlineObj)->days) : 0;
                $deadlineUnix = $deadlineObj ? (int)$deadlineObj->format('U') : 0;

                $createdAt = !empty($req->created_at) ? strtotime($req->created_at) : time();

                $searchBlob = strtolower(trim(
                    ($req->title ?? '') . ' ' .
                        ($req->club_name ?? '') . ' ' .
                        ($req->headline ?? '')
                ));

                $statusClass = $expired ? 'status-expired' : 'status-' . strtolower((string)$req->status);
                $statusLabel = $expired ? 'Expired' : (string)$req->status;
            ?>
                <article
                    class="card"
                    data-search="<?php echo htmlspecialchars($searchBlob, ENT_QUOTES, 'UTF-8'); ?>"
                    data-expired="<?php echo $expired ? '1' : '0'; ?>"
                    data-progress="<?php echo number_format($percentage, 2, '.', ''); ?>"
                    data-deadline="<?php echo $deadlineUnix; ?>"
                    data-created="<?php echo (int)$createdAt; ?>">
                    <div class="card-head">
                        <h3><?php echo htmlspecialchars($req->title); ?></h3>
                        <span class="status-tag <?php echo htmlspecialchars($statusClass); ?>"><?php echo htmlspecialchars($statusLabel); ?></span>
                    </div>

                    <p class="club-name"><?php echo htmlspecialchars($req->club_name); ?></p>

                    <div class="amount-row">
                        <span><span class="amount-label">Goal:</span> Rs.<?php echo number_format($targetAmount, 0); ?></span>
                        <span><span class="amount-label">Raised:</span> Rs.<?php echo number_format($raisedAmount, 0); ?></span>
                    </div>

                    <div class="progress-container">
                        <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                    </div>

                    <div class="card-bottom">
                        <p class="progress-text">
                            <?php if ($deadlineObj): ?>
                                <?php if ($expired): ?>
                                    Ended
                                <?php else: ?>
                                    <?php echo $daysLeft; ?> days left
                                <?php endif; ?>
                            <?php else: ?>
                                Ongoing campaign
                            <?php endif; ?>
                        </p>

                        <div style="display:flex; gap:0.45rem;">
                            <button class="view-btn" style="flex:1;border-radius: 5px;height: fit-content; background-color: var(--text);"
                                onclick="window.location.href='<?php echo URLROOT; ?>/fundraiser/show/<?php echo $req->req_id; ?>'">
                                View
                            </button>
                            <button
                                type="button"
                                class="view-btn report-btn report-fundraiser-btn"
                                style="flex:1;"
                                data-fundraiser-id="<?php echo (int)$req->req_id; ?>">Report</button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <p id="campaignNoResults" class="no-filter-results is-hidden">No campaigns match your search and filters.</p>

        <div id="fundraiserReportModal" class="fundraise-report-overlay" style="display:none;">
            <div class="fundraise-report-modal">
                <div class="fundraise-report-header">
                    <h3>Report Fundraiser Campaign</h3>
                    <button type="button" class="fundraise-report-close" data-action="close">X</button>
                </div>
                <form id="fundraiser-report-form" novalidate>
                    <input type="hidden" id="fundraiserReportId" value="">
                    <div class="fundraise-report-group">
                        <label for="fundraiserReportCategory">Category</label>
                        <select id="fundraiserReportCategory" required>
                            <option value="" disabled selected>Select a category</option>
                            <option>Spam</option>
                            <option>Harassment or bullying</option>
                            <option>Hate or abusive content</option>
                            <option>Misinformation</option>
                            <option>Fraud or suspicious fundraising</option>
                            <option>Illegal or dangerous acts</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="fundraise-report-group">
                        <label for="fundraiserReportDetails">Details (optional)</label>
                        <textarea id="fundraiserReportDetails" rows="4" placeholder="Add any details or context..."></textarea>
                    </div>
                    <div class="fundraise-report-group">
                        <label for="fundraiserReportLink">Reference link (optional)</label>
                        <input type="url" id="fundraiserReportLink" placeholder="https://..." />
                    </div>
                    <div class="fundraise-report-actions">
                        <button type="button" class="view-btn" data-action="cancel">Cancel</button>
                        <button type="submit" class="view-btn">Submit Report</button>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <p class="no-campaigns">No fundraise requests found.</p>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>

<?php ob_start(); ?>
(() => {
const grid = document.getElementById('campaignCards');
if (!grid) return;

const cards = Array.from(grid.querySelectorAll('.card'));
const searchInput = document.getElementById('campaignSearch');
const stateFilter = document.getElementById('campaignStateFilter');
const progressFilter = document.getElementById('campaignProgressFilter');
const sortSelect = document.getElementById('campaignSort');
const resultCount = document.getElementById('campaignResultCount');
const noResults = document.getElementById('campaignNoResults');

const toNumber = (value) => {
const n = Number(value);
return Number.isFinite(n) ? n : 0;
};

const matchesProgress = (progress, filter) => {
if (filter === 'low') return progress < 25;
    if (filter==='mid' ) return progress>= 25 && progress < 75;
        if (filter==='high' ) return progress>= 75;
        return true;
        };

        const applyFilters = () => {
        const query = (searchInput?.value || '').trim().toLowerCase();
        const state = stateFilter?.value || 'all';
        const progressRange = progressFilter?.value || 'all';
        const sortBy = sortSelect?.value || 'newest';

        const visible = cards.filter((card) => {
        const searchBlob = (card.dataset.search || '').toLowerCase();
        const expired = card.dataset.expired === '1';
        const progress = toNumber(card.dataset.progress);

        if (query && !searchBlob.includes(query)) return false;
        if (state === 'active' && expired) return false;
        if (state === 'expired' && !expired) return false;
        if (!matchesProgress(progress, progressRange)) return false;
        return true;
        });

        visible.sort((a, b) => {
        if (sortBy === 'progress') {
        return toNumber(b.dataset.progress) - toNumber(a.dataset.progress);
        }

        if (sortBy === 'deadline') {
        const aExpired = a.dataset.expired === '1';
        const bExpired = b.dataset.expired === '1';
        if (aExpired !== bExpired) return aExpired ? 1 : -1;
        return toNumber(a.dataset.deadline) - toNumber(b.dataset.deadline);
        }

        return toNumber(b.dataset.created) - toNumber(a.dataset.created);
        });

        cards.forEach((card) => card.classList.add('is-hidden'));
        visible.forEach((card) => {
        card.classList.remove('is-hidden');
        grid.appendChild(card);
        });

        if (resultCount) {
        resultCount.textContent = `Showing ${visible.length} of ${cards.length} campaigns`;
        }

        if (noResults) {
        noResults.classList.toggle('is-hidden', visible.length > 0);
        }
        };

        searchInput?.addEventListener('input', applyFilters);
        stateFilter?.addEventListener('change', applyFilters);
        progressFilter?.addEventListener('change', applyFilters);
        sortSelect?.addEventListener('change', applyFilters);

        applyFilters();
        })();

        (() => {
        const reportButtons = document.querySelectorAll('.report-fundraiser-btn');
        const reportModal = document.getElementById('fundraiserReportModal');
        const reportForm = document.getElementById('fundraiser-report-form');
        const reportId = document.getElementById('fundraiserReportId');
        const reportCategory = document.getElementById('fundraiserReportCategory');
        const reportDetails = document.getElementById('fundraiserReportDetails');
        const reportLink = document.getElementById('fundraiserReportLink');
        const reportEndpoint = '<?php echo URLROOT; ?>/report/submitReport/fundraiser';

        if (!reportButtons.length || !reportModal || !reportForm || !reportId) {
        return;
        }

        const notify = (message) => {
        if (typeof show_popup === 'function') {
        show_popup(message);
        return;
        }
        alert(message);
        };

        const closeReportModal = () => {
        reportModal.style.display = 'none';
        };

        reportButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
        reportId.value = String(Number(btn.dataset.fundraiserId || 0));
        reportModal.style.display = 'flex';
        });
        });

        reportModal.querySelector('[data-action="close"]')?.addEventListener('click', closeReportModal);
        reportModal.querySelector('[data-action="cancel"]')?.addEventListener('click', closeReportModal);
        reportModal.addEventListener('click', (e) => {
        if (e.target === reportModal) {
        closeReportModal();
        }
        });

        reportForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const fundraiserId = Number(reportId.value || 0);
        if (!fundraiserId) {
        notify('Invalid fundraiser id for report');
        return;
        }

        const category = reportCategory ? reportCategory.value : '';
        if (!category) {
        notify('Please select a report category');
        return;
        }

        const submitBtn = reportForm.querySelector('button[type="submit"]');
        const previousText = submitBtn ? submitBtn.textContent : 'Submit Report';
        if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
        }

        try {
        const fd = new FormData();
        fd.append('fundraiser_id', String(fundraiserId));
        fd.append('category', category);
        fd.append('details', reportDetails ? reportDetails.value.trim() : '');
        const linkValue = reportLink ? reportLink.value.trim() : '';
        if (linkValue) {
        fd.append('link', linkValue);
        }

        const response = await fetch(reportEndpoint, {
        method: 'POST',
        body: fd
        });

        const json = await response.json().catch(() => null);
        if (!response.ok || !json || (json.success !== true && json.status !== 'success')) {
        throw new Error((json && json.message) ? json.message : 'Failed to submit campaign report');
        }

        notify('Thanks for your report. Our team will review this campaign.');
        reportForm.reset();
        closeReportModal();
        } catch (err) {
        console.error('Fundraiser report submission failed', err);
        notify(err && err.message ? err.message : 'Failed to submit campaign report');
        } finally {
        if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = previousText;
        }
        }
        });
        })();
        <?php $scripts = ob_get_clean(); ?>

        <?php require APPROOT . '/views/request_dashboards/request_dashboard_layout_adapter.php'; ?>