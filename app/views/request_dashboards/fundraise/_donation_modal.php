<?php
// Reusable donation modal partial
// Expects $campaign (object) to be defined by the including view
// Uses a global include guard to avoid duplicating styles/scripts

if (!isset($GLOBALS['donation_modal_included'])): $GLOBALS['donation_modal_included'] = true; ?>
    <style>
        .gl-donate-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.74);
            backdrop-filter: blur(10px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .gl-donate-overlay.show {
            display: flex;
        }

        .gl-donate-modal {
            width: min(860px, 96vw);
            max-height: 92vh;
            overflow: auto;
            background: var(--bg-alt);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
        }

        .gl-donate-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
        }

        .gl-donate-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text);
        }

        .gl-donate-body {
            padding: 1rem 1.25rem;
        }

        .gl-close {
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--muted);
            cursor: pointer;
            font-size: 1rem;
            padding: .25rem .5rem;
        }

        .gl-donate-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 1rem;
        }

        .gl-card {
            background: rgba(0, 0, 0, 0.29);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 1rem;
        }

        .gl-card h3 {
            margin: 0 0 .75rem;
            font-weight: 700;
            color: var(--text);
        }

        .gl-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .9rem;
        }

        .gl-form-grid .full {
            grid-column: 1 / -1;
        }

        .gl-label {
            font-size: .9rem;
            color: var(--muted);
            margin-bottom: .4rem;
            display: block;
        }

        .gl-input,
        .gl-select {
            width: 100%;
            background: var(--bg);
            color: var(--text);
            border: 1px solid var(--border);
            border-radius: .55rem;
            padding: .7rem .85rem;
        }

        .gl-chips {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            margin: .35rem 0 .6rem;
        }

        .gl-chip {
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.03);
            color: var(--text);
            padding: .45rem .7rem;
            border-radius: 999px;
            cursor: pointer;
        }

        .gl-chip.active {
            border-color: var(--link);
            color: var(--link);
            background: rgba(9, 34, 97, 0.81);
            box-shadow: rgba(130, 42, 254, 0.67);
        }

        .gl-helper {
            font-size: .85rem;
            color: var(--muted);
        }

        .gl-actions {
            display: flex;
            gap: .5rem;
            align-items: center;
            margin-top: .85rem;
        }

        .gl-btn-primary {
            background: var(--link);
            color: #fff;
            border: none;
            border-radius: .5rem;
            padding: .6rem .9rem;
            cursor: pointer;
        }

        .gl-btn-ghost {
            background: transparent;
            color: var(--text);
            border: 1px solid var(--border);
            border-radius: .5rem;
            padding: .6rem .9rem;
            cursor: pointer;
        }

        .gl-summary-row {
            display: flex;
            justify-content: space-between;
            margin: .45rem 0;
            color: var(--text);
        }

        .gl-summary-row .muted {
            color: var(--muted);
        }

        .gl-progress {
            position: relative;
            width: 100%;
            background: var(--border);
            border-radius: 10px;
            height: 12px;
            overflow: hidden;
            margin: .5rem 0 .8rem;
        }

        .gl-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4caf50, #2e7d32);
            border-radius: 10px 0 0 10px;
            transition: width .3s ease;
        }

        .gl-badge {
            display: inline-block;
            padding: .2rem .55rem;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
        }

        .gl-badge.pending {
            background: rgba(255, 193, 7, .2);
            color: #ffc107;
        }

        .gl-badge.approved {
            background: rgba(40, 167, 69, .2);
            color: #28a745;
        }

        .gl-badge.rejected {
            background: rgba(220, 53, 69, .2);
            color: #dc3545;
        }

        @media (max-width: 920px) {
            .gl-donate-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div id="glDonationOverlay" class="gl-donate-overlay" role="dialog" aria-modal="true" aria-labelledby="glDonationTitle">
        <div class="gl-donate-modal" onclick="event.stopPropagation()">
            <div class="gl-donate-header">
                <h3 id="glDonationTitle" class="gl-donate-title">Make a Donation</h3>
                <button class="gl-close" type="button" aria-label="Close" onclick="GL_closeDonationModal()">✕</button>
            </div>
            <div class="gl-donate-body">
                <div class="gl-donate-grid">
                    <div class="gl-card">
                        <h3>Donation details</h3>
                        <form id="glDonationForm" method="post" action="#" onsubmit="return GL_handleDonate(event)">
                            <div class="gl-form-grid">
                                <div class="full">
                                    <label class="gl-label">Quick amounts</label>
                                    <div class="gl-chips" id="glTiers">
                                        <button type="button" class="gl-chip" data-amount="1000">LKR 1,000</button>
                                        <button type="button" class="gl-chip" data-amount="2500">LKR 2,500</button>
                                        <button type="button" class="gl-chip" data-amount="5000">LKR 5,000</button>
                                        <button type="button" class="gl-chip" data-amount="10000">LKR 10,000</button>
                                    </div>
                                </div>

                                <div class="full">
                                    <label for="glAmount" class="gl-label">Donation amount (LKR)</label>
                                    <input type="number" min="100" step="100" id="glAmount" name="amount" class="gl-input" required placeholder="e.g., 2500">
                                    <div class="gl-helper">Minimum donation LKR 100</div>
                                </div>

                                <div>
                                    <label for="glFirstName" class="gl-label">First name</label>
                                    <input type="text" id="glFirstName" name="first_name" class="gl-input" required>
                                </div>
                                <div>
                                    <label for="glLastName" class="gl-label">Last name</label>
                                    <input type="text" id="glLastName" name="last_name" class="gl-input" required>
                                </div>

                                <div class="full">
                                    <label for="glEmail" class="gl-label">Email</label>
                                    <input type="email" id="glEmail" name="email" class="gl-input" required>
                                </div>

                                <div>
                                    <label for="glVisibility" class="gl-label">Name visibility</label>
                                    <select id="glVisibility" name="visibility" class="gl-select">
                                        <option value="public">Show my name</option>
                                        <option value="anonymous">Donate anonymously</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="glMessage" class="gl-label">Message (optional)</label>
                                    <input type="text" id="glMessage" name="message" class="gl-input" placeholder="A note to the organizers">
                                </div>

                                <div class="full">
                                    <label class="gl-label">Payment method</label>
                                    <div class="gl-chips" id="glPayMethods">
                                        <button type="button" class="gl-chip active" data-method="card">Card</button>
                                        <button type="button" class="gl-chip" data-method="bank">Bank Transfer</button>
                                    </div>
                                    <div class="gl-helper">Payment is simulated for now — no real charges.</div>
                                </div>
                            </div>

                            <div class="gl-actions">
                                <button class="gl-btn-primary" type="submit">Donate now</button>
                                <button class="gl-btn-ghost" type="button" onclick="GL_closeDonationModal()">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <aside class="gl-card">
                        <h3>Campaign summary</h3>
                        <?php if (isset($campaign) && $campaign): ?>
                            <?php $gl_pct = min(100, ($campaign->target_amount > 0) ? ($campaign->raised_amount / $campaign->target_amount * 100) : 0); ?>
                            <div class="gl-summary-row"><span class="muted">Title</span><span><?php echo htmlspecialchars($campaign->title); ?></span></div>
                            <div class="gl-summary-row"><span class="muted">Club</span><span><?php echo htmlspecialchars($campaign->club_name); ?></span></div>
                            <div class="gl-summary-row"><span class="muted">Status</span>
                                <span class="gl-badge <?php echo strtolower($campaign->status); ?>"><?php echo htmlspecialchars($campaign->status); ?></span>
                            </div>
                            <div class="gl-summary-row"><span class="muted">Target</span><span>Rs. <?php echo number_format($campaign->target_amount, 2); ?></span></div>
                            <div class="gl-summary-row"><span class="muted">Raised</span><span>Rs. <?php echo number_format($campaign->raised_amount, 2); ?></span></div>
                            <div class="gl-progress">
                                <div class="gl-progress-fill" style="width: <?php echo number_format($gl_pct, 2); ?>%"></div>
                            </div>
                            <div class="gl-summary-row"><span class="muted">Progress</span><span><?php echo number_format($gl_pct, 2); ?>%</span></div>
                        <?php else: ?>
                            <p class="gl-helper">No campaign data available.</p>
                        <?php endif; ?>
                        <hr style="border-color: var(--border); border-style: solid; border-width: 1px 0 0; margin:.8rem 0;">
                        <div class="gl-summary-row"><span class="muted">Processing fee</span><span>Rs. 0.00</span></div>
                        <div class="gl-summary-row"><span class="muted">You pay</span><span id="glYouPay">Rs. 0.00</span></div>
                    </aside>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Donation modal controller (scoped globals)
        const GL_overlay = document.getElementById('glDonationOverlay');
        const GL_amountEl = document.getElementById('glAmount');
        const GL_youPayEl = document.getElementById('glYouPay');
        const GL_tiers = document.querySelectorAll('#glTiers .gl-chip');
        const GL_methods = document.querySelectorAll('#glPayMethods .gl-chip');

        function GL_formatLKR(v) {
            const n = Number(v || 0);
            return 'Rs. ' + n.toLocaleString('en-LK', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function GL_updateYouPay() {
            if (GL_youPayEl && GL_amountEl) GL_youPayEl.textContent = GL_formatLKR(GL_amountEl.value || 0);
        }
        GL_tiers.forEach(btn => {
            btn.addEventListener('click', () => {
                GL_tiers.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                GL_amountEl.value = btn.dataset.amount;
                GL_updateYouPay();
            });
        });
        GL_methods.forEach(btn => {
            btn.addEventListener('click', () => {
                GL_methods.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });
        if (GL_amountEl) {
            GL_amountEl.addEventListener('input', GL_updateYouPay);
            GL_updateYouPay();
        }

        function GL_openDonationModal() {
            if (!GL_overlay) return;
            GL_overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                GL_amountEl && GL_amountEl.focus();
            }, 0);
        }

        function GL_closeDonationModal() {
            if (!GL_overlay) return;
            GL_overlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        function GL_handleDonate(e) {
            e.preventDefault();
            const amount = Number(GL_amountEl && GL_amountEl.value);
            if (!amount || amount < 100) {
                GL_amountEl && (GL_amountEl.style.borderColor = '#dc3545', GL_amountEl.focus());
                return false;
            }
            alert('Thank you for your donation of ' + GL_formatLKR(amount) + '! (Simulation)');
            <?php if (isset($campaign) && $campaign): ?>
                window.location.href = '<?php echo URLROOT; ?>/fundraiser/show/<?php echo (int)$campaign->req_id; ?>';
            <?php else: ?>
                window.location.href = '<?php echo URLROOT; ?>/fundraiser';
            <?php endif; ?>
            return false;
        }

        // Expose open/close globally for triggers
        window.GL_openDonationModal = GL_openDonationModal;
        window.GL_closeDonationModal = GL_closeDonationModal;

        // Close on overlay click
        if (GL_overlay) {
            GL_overlay.addEventListener('click', (e) => {
                if (e.target === GL_overlay) {
                    GL_closeDonationModal();
                }
            });
        }
        // Close on ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && GL_overlay && GL_overlay.classList.contains('show')) GL_closeDonationModal();
        });
    </script>
<?php endif; // end include guard 
?>