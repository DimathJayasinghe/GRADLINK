<div class="account_content bookmark-page">
	<style>
		.bookmark-toolbar {
			display: flex;
			flex-direction: column;
			gap: 10px;
			padding: 14px;
			border: 1px solid var(--border);
			border-radius: var(--radius-lg);
			background: rgba(255, 255, 255, 0.03);
			margin-bottom: 14px;
		}
		.bookmark-filter-row {
			display: grid;
			grid-template-columns: repeat(3, minmax(0, 1fr));
			gap: 10px;
		}
		.bookmark-filter-control {
			display: flex;
			flex-direction: column;
			gap: 6px;
		}
		.bookmark-filter-control label {
			font-size: 12px;
			color: var(--muted);
			font-weight: 600;
		}
		.bookmark-filter-control input,
		.bookmark-filter-control select {
			width: 100%;
			padding: 10px;
			border-radius: var(--radius-lg);
			border: 1px solid var(--border);
			background: var(--input);
			color: var(--text);
		}
		.bookmark-filter-actions {
			display: flex;
			justify-content: flex-end;
			gap: 8px;
		}
		.bookmark-list {
			display: grid;
			gap: 10px;
		}
		.bookmark-card {
			display: grid;
			grid-template-columns: 1fr auto;
			gap: 10px;
			padding: 12px;
			border: 1px solid var(--border);
			border-radius: var(--radius-lg);
			background: rgba(255, 255, 255, 0.02);
		}
		.bookmark-card .settings-option-details {
			max-width: 100%;
		}
		.bookmark-meta {
			display: flex;
			flex-wrap: wrap;
			gap: 8px;
			margin-top: 6px;
			font-size: 12px;
			color: var(--muted);
		}
		.bookmark-type-badge {
			display: inline-flex;
			align-items: center;
			gap: 4px;
			padding: 2px 8px;
			border-radius: 999px;
			background: rgba(255,255,255,0.08);
			font-weight: 600;
		}
		.bookmark-card-actions {
			display: flex;
			gap: 8px;
			align-items: flex-start;
			flex-wrap: wrap;
			justify-content: flex-end;
		}
		@media (max-width: 900px) {
			.bookmark-filter-row {
				grid-template-columns: 1fr;
			}
			.bookmark-card {
				grid-template-columns: 1fr;
			}
			.bookmark-card-actions {
				justify-content: flex-start;
			}
		}
	</style>

	<h2>Bookmarks</h2>
	<p class="settings-description">Manage all saved items in one place.</p>

	<div class="bookmark-toolbar">
		<div class="bookmark-filter-row">
			<div class="bookmark-filter-control">
				<label for="bookmarkSearch">Search</label>
				<input id="bookmarkSearch" type="text" placeholder="Search by title, description, or type">
			</div>
			<div class="bookmark-filter-control">
				<label for="bookmarkTypeFilter">Type</label>
				<select id="bookmarkTypeFilter">
					<option value="all">All types</option>
					<option value="events">Events</option>
					<option value="posts">Posts</option>
					<option value="messages">Messages</option>
					<option value="other">Other</option>
				</select>
			</div>
			<div class="bookmark-filter-control">
				<label for="bookmarkQuickTime">Bookmarked Time</label>
				<select id="bookmarkQuickTime">
					<option value="any">Any time</option>
					<option value="today">Today</option>
					<option value="last7">Last 7 days</option>
					<option value="last30">Last 30 days</option>
					<option value="thisMonth">This month</option>
					<option value="thisYear">This year</option>
				</select>
			</div>
		</div>

		<div class="bookmark-filter-row">
			<div class="bookmark-filter-control">
				<label for="bookmarkDateFrom">Bookmarked From</label>
				<input id="bookmarkDateFrom" type="date">
			</div>
			<div class="bookmark-filter-control">
				<label for="bookmarkDateTo">Bookmarked To</label>
				<input id="bookmarkDateTo" type="date">
			</div>
			<div class="bookmark-filter-control">
				<label for="bookmarkEventDateFrom">Event Date From</label>
				<input id="bookmarkEventDateFrom" type="date">
			</div>
		</div>

		<div class="bookmark-filter-row">
			<div class="bookmark-filter-control">
				<label for="bookmarkEventDateTo">Event Date To</label>
				<input id="bookmarkEventDateTo" type="date">
			</div>
			<div class="bookmark-filter-actions" style="grid-column: span 2; align-items: flex-end;">
				<button type="button" class="settings-btn-secondary" id="bookmarkResetFilters">Reset Filters</button>
			</div>
		</div>
	</div>

	<p class="settings-description" id="bookmarkFilterSummary" style="margin: 0 0 12px;">Showing 0 bookmarks</p>
	<p class="settings-description" id="bookmarkNoMatch" style="display:none; margin-top:0;">No bookmarks match the current filters.</p>

	<?php
		$bookmarks = isset($data['bookmarks']) && is_array($data['bookmarks']) ? $data['bookmarks'] : [];
		$groups = [
			'events' => ['label' => 'Events', 'icon' => 'calendar-alt'],
			'posts' => ['label' => 'Posts', 'icon' => 'newspaper'],
			'messages' => ['label' => 'Messages', 'icon' => 'envelope'],
			'other' => ['label' => 'Other', 'icon' => 'bookmark'],
		];

		$shorten = function($text, $max = 180) {
			$text = trim((string)$text);
			if ($text === '') return '';
			if (function_exists('mb_strlen') && function_exists('mb_substr')) {
				return mb_strlen($text) > $max ? mb_substr($text, 0, $max) . '...' : $text;
			}
			return strlen($text) > $max ? substr($text, 0, $max) . '...' : $text;
		};
	?>

	<?php foreach ($groups as $type => $config): ?>
		<?php
			$items = array_values(array_filter($bookmarks, function($item) use ($type) {
				return strtolower((string)($item->bookmark_type ?? '')) === $type;
			}));
		?>
		<div class="settings-section bookmark-group" data-bookmark-group="<?= htmlspecialchars($type) ?>">
			<h3><i class="fas fa-<?= htmlspecialchars($config['icon']) ?>" style="margin-right:8px;"></i><?= htmlspecialchars($config['label']) ?></h3>
			<div class="section-divider"></div>

			<?php if (empty($items)): ?>
				<p class="settings-description" data-empty-state="1" style="margin-bottom:0;">No <?= htmlspecialchars(strtolower($config['label'])) ?> bookmarks yet.</p>
			<?php else: ?>
				<div class="bookmark-list">
					<?php foreach ($items as $item): ?>
						<?php
							$title = trim((string)($item->title ?? 'Untitled'));
							$subtitle = trim((string)($item->subtitle ?? ''));
							$description = $shorten((string)($item->description ?? ''));
							$url = trim((string)($item->url ?? ''));
							$refId = (int)($item->reference_id ?? 0);
							$href = '';
							if ($url !== '') {
								$href = preg_match('/^https?:\/\//i', $url) ? $url : (URLROOT . $url);
							}

							$bookmarkedTs = !empty($item->created_at) ? strtotime((string)$item->created_at) : false;
							$bookmarkedDate = $bookmarkedTs ? date('Y-m-d', $bookmarkedTs) : '';
							$bookmarkedDisplay = $bookmarkedTs ? date('M d, Y h:i A', $bookmarkedTs) : 'Unknown';

							$eventTs = !empty($item->event_date) ? strtotime((string)$item->event_date) : false;
							$eventDate = $eventTs ? date('Y-m-d', $eventTs) : '';
							$eventDisplay = $eventTs ? date('M d, Y h:i A', $eventTs) : '';

							$searchBlob = strtolower(trim($type . ' ' . $title . ' ' . $subtitle . ' ' . $description));
						?>
						<div
							class="bookmark-card bookmark-item"
							data-type="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>"
							data-reference-id="<?= htmlspecialchars((string)$refId, ENT_QUOTES, 'UTF-8') ?>"
							data-search="<?= htmlspecialchars($searchBlob, ENT_QUOTES, 'UTF-8') ?>"
							data-bookmarked-date="<?= htmlspecialchars($bookmarkedDate, ENT_QUOTES, 'UTF-8') ?>"
							data-event-date="<?= htmlspecialchars($eventDate, ENT_QUOTES, 'UTF-8') ?>"
						>
							<div class="settings-option-details">
								<h4 style="margin-bottom: 6px;"><?= htmlspecialchars($title) ?></h4>
								<?php if ($subtitle !== ''): ?>
									<p><?= htmlspecialchars($subtitle) ?></p>
								<?php endif; ?>
								<?php if ($description !== ''): ?>
									<p style="margin-top:6px;"><?= htmlspecialchars($description) ?></p>
								<?php endif; ?>

								<div class="bookmark-meta">
									<span class="bookmark-type-badge"><i class="fas fa-<?= htmlspecialchars($config['icon']) ?>"></i><?= htmlspecialchars($config['label']) ?></span>
									<span>Bookmarked: <?= htmlspecialchars($bookmarkedDisplay) ?></span>
									<?php if ($eventDisplay !== ''): ?>
										<span>Event Date: <?= htmlspecialchars($eventDisplay) ?></span>
									<?php endif; ?>
								</div>
							</div>

							<div class="bookmark-card-actions">
								<?php if ($href !== ''): ?>
									<a class="settings-btn" href="<?= htmlspecialchars($href) ?>">Open</a>
								<?php endif; ?>
								<button
									type="button"
									class="settings-btn settings-btn-danger js-remove-bookmark"
									data-type="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>"
									data-reference-id="<?= htmlspecialchars((string)$refId, ENT_QUOTES, 'UTF-8') ?>"
								>
									Remove
								</button>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
	const root = document.querySelector('.bookmark-page');
	if(!root) return;

	const searchInput = document.getElementById('bookmarkSearch');
	const typeFilter = document.getElementById('bookmarkTypeFilter');
	const quickTimeFilter = document.getElementById('bookmarkQuickTime');
	const bookmarkedFromInput = document.getElementById('bookmarkDateFrom');
	const bookmarkedToInput = document.getElementById('bookmarkDateTo');
	const eventFromInput = document.getElementById('bookmarkEventDateFrom');
	const eventToInput = document.getElementById('bookmarkEventDateTo');
	const resetFiltersBtn = document.getElementById('bookmarkResetFilters');
	const summaryEl = document.getElementById('bookmarkFilterSummary');
	const noMatchEl = document.getElementById('bookmarkNoMatch');

	function parseDate(value){
		if(!value) return null;
		const d = new Date(value + 'T00:00:00');
		return Number.isNaN(d.getTime()) ? null : d;
	}

	function inRange(dateString, from, to){
		const d = parseDate(dateString);
		if(!d) return false;
		if(from && d < from) return false;
		if(to && d > to) return false;
		return true;
	}

	function getQuickWindow(mode){
		if(mode === 'any') return null;
		const now = new Date();
		const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		const end = new Date(today);

		if(mode === 'today'){
			return { from: today, to: end };
		}
		if(mode === 'last7'){
			const from = new Date(today);
			from.setDate(from.getDate() - 6);
			return { from: from, to: end };
		}
		if(mode === 'last30'){
			const from = new Date(today);
			from.setDate(from.getDate() - 29);
			return { from: from, to: end };
		}
		if(mode === 'thisMonth'){
			const from = new Date(now.getFullYear(), now.getMonth(), 1);
			return { from: from, to: end };
		}
		if(mode === 'thisYear'){
			const from = new Date(now.getFullYear(), 0, 1);
			return { from: from, to: end };
		}

		return null;
	}

	function applyFilters(){
		const searchTerm = (searchInput?.value || '').trim().toLowerCase();
		const selectedType = (typeFilter?.value || 'all').toLowerCase();
		const quickMode = (quickTimeFilter?.value || 'any').toLowerCase();

		let bookmarkedFrom = parseDate(bookmarkedFromInput?.value || '');
		let bookmarkedTo = parseDate(bookmarkedToInput?.value || '');
		const eventFrom = parseDate(eventFromInput?.value || '');
		const eventTo = parseDate(eventToInput?.value || '');

		const quickWindow = getQuickWindow(quickMode);
		if(quickWindow){
			if(!bookmarkedFrom || quickWindow.from > bookmarkedFrom) bookmarkedFrom = quickWindow.from;
			if(!bookmarkedTo || quickWindow.to < bookmarkedTo) bookmarkedTo = quickWindow.to;
		}

		const eventFilterActive = !!(eventFrom || eventTo);
		const items = Array.from(root.querySelectorAll('.bookmark-item'));
		let visibleCount = 0;

		items.forEach(function(item){
			const itemType = (item.getAttribute('data-type') || '').toLowerCase();
			const searchBlob = (item.getAttribute('data-search') || '').toLowerCase();
			const bookmarkedDate = item.getAttribute('data-bookmarked-date') || '';
			const eventDate = item.getAttribute('data-event-date') || '';

			let isVisible = true;
			if(selectedType !== 'all' && itemType !== selectedType) isVisible = false;
			if(isVisible && searchTerm && !searchBlob.includes(searchTerm)) isVisible = false;
			if(isVisible && (bookmarkedFrom || bookmarkedTo) && !inRange(bookmarkedDate, bookmarkedFrom, bookmarkedTo)) {
				isVisible = false;
			}
			if(isVisible && eventFilterActive){
				if(itemType !== 'events') {
					isVisible = false;
				} else if(!inRange(eventDate, eventFrom, eventTo)) {
					isVisible = false;
				}
			}

			item.style.display = isVisible ? '' : 'none';
			if(isVisible) visibleCount++;
		});

		const groups = Array.from(root.querySelectorAll('.bookmark-group'));
		groups.forEach(function(group){
			const groupType = (group.getAttribute('data-bookmark-group') || '').toLowerCase();
			const itemEls = Array.from(group.querySelectorAll('.bookmark-item'));
			const hasVisibleItems = itemEls.some(function(el){ return el.style.display !== 'none'; });
			const emptyState = group.querySelector('[data-empty-state="1"]');

			if(itemEls.length === 0){
				if(emptyState) emptyState.style.display = '';
				group.style.display = (selectedType === 'all' || selectedType === groupType) ? '' : 'none';
				return;
			}

			if(emptyState) emptyState.style.display = 'none';
			group.style.display = hasVisibleItems ? '' : 'none';
		});

		if(summaryEl){
			summaryEl.textContent = `Showing ${visibleCount} of ${items.length} bookmarks`;
		}
		if(noMatchEl){
			noMatchEl.style.display = visibleCount === 0 ? '' : 'none';
		}
	}

	[searchInput, typeFilter, quickTimeFilter, bookmarkedFromInput, bookmarkedToInput, eventFromInput, eventToInput]
		.forEach(function(control){
			if(!control) return;
			const eventName = control.tagName === 'INPUT' && control.type === 'text' ? 'input' : 'change';
			control.addEventListener(eventName, applyFilters);
			if(eventName !== 'input') control.addEventListener('input', applyFilters);
		});

	if(resetFiltersBtn){
		resetFiltersBtn.addEventListener('click', function(){
			if(searchInput) searchInput.value = '';
			if(typeFilter) typeFilter.value = 'all';
			if(quickTimeFilter) quickTimeFilter.value = 'any';
			if(bookmarkedFromInput) bookmarkedFromInput.value = '';
			if(bookmarkedToInput) bookmarkedToInput.value = '';
			if(eventFromInput) eventFromInput.value = '';
			if(eventToInput) eventToInput.value = '';
			applyFilters();
		});
	}

	root.addEventListener('click', async function(e){
		const btn = e.target.closest('.js-remove-bookmark');
		if(!btn) return;

		const type = btn.getAttribute('data-type');
		const referenceId = Number(btn.getAttribute('data-reference-id') || '0');
		if(!type) return;

		const previousText = btn.textContent;
		btn.disabled = true;
		btn.textContent = 'Removing...';

		try {
			const headers = { 'Content-Type': 'application/json' };
			if (typeof window !== 'undefined' && window.GL_CSRF_TOKEN) {
				headers['X-CSRF-Token'] = window.GL_CSRF_TOKEN;
			}

			const response = await fetch('<?= URLROOT ?>/bookmark/delete', {
				method: 'POST',
				headers,
				body: JSON.stringify({
					type: type,
					reference_id: referenceId
				})
			});
			const json = await response.json().catch(() => null);

			if (!response.ok || !json || !json.ok) {
				throw new Error((json && json.error) ? json.error : 'Could not remove bookmark');
			}

			const row = btn.closest('.bookmark-item');
			const group = btn.closest('[data-bookmark-group]');
			if (row) row.remove();

			if (group && !group.querySelector('.bookmark-item')) {
				const empty = group.querySelector('[data-empty-state="1"]');
				if (empty) {
					empty.style.display = '';
				} else {
					const p = document.createElement('p');
					p.className = 'settings-description';
					p.setAttribute('data-empty-state', '1');
					p.style.marginBottom = '0';
					const groupType = group.getAttribute('data-bookmark-group') || 'items';
					p.textContent = `No ${groupType} bookmarks yet.`;
					group.appendChild(p);
				}
			}

			applyFilters();
		} catch (err) {
			btn.disabled = false;
			btn.textContent = previousText;
			alert(err.message || 'Network error while removing bookmark');
		}
	});

	applyFilters();
});
</script>
