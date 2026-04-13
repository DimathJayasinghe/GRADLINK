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

                        <a class="view-btn" href="<?php echo URLROOT; ?>/fundraiser/show/<?php echo $req->req_id; ?>">View Details</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <p id="campaignNoResults" class="no-filter-results is-hidden">No campaigns match your search and filters.</p>
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
        if (filter === 'mid') return progress >= 25 && progress < 75;
        if (filter === 'high') return progress >= 75;
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
<?php $scripts = ob_get_clean(); ?>

<?php require APPROOT . '/views/request_dashboards/request_dashboard_layout_adapter.php'; ?>