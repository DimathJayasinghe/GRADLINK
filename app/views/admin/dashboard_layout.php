<?php require APPROOT . '/views/inc/header.php';?>
<?php $topnavbar_content = [
    // ['url' => URLROOT . '/messages', 'label' => 'Messages', 'icon' => 'envelope', 'active' => false],
    // ['url' => URLROOT . '/settings', 'label' => 'Settings', 'icon' => 'cog', 'active' => false],
]?>
<?php require APPROOT . '/views/inc/commponents/topnavbar.php';?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/req_dashboard/dashboard_layout.css">
<?php /* $styles moved to the document <head> by header.php */ ?>

<div class="container">
    <div class="leftsidebar">
        <ul class="menu">
            <?php
                if(!isset($sidebar_left) || !is_array($sidebar_left)){
                    $sidebar_left = [
                        ['label'=>'Overview', 'url'=>'/admin','active'=>false, 'icon'=>'tachometer-alt'],
                        ['label'=>'User Management', 'url'=>'/admin/users','active'=>false, 'icon' => 'users'],
                        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>false, 'icon' => 'chart-bar'],
                        ['label'=>'Event Moderation', 'url'=>'/admin/eventrequests','active'=>false, 'icon' => 'clipboard-list'],
                        ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>false, 'icon' => 'pencil-alt'],
                        ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>false, 'icon' => 'donate'],
                        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle'],
                        ['label'=>'Suspended Users', 'url'=>'/admin/suspendedUsers','active'=>false, 'icon' => 'user-slash'],
                        ['label'=>'Help & Support', 'url'=>'/admin/support','active'=>false, 'icon' => 'circle-question']
                    ];
                }
            ?>
            <?php foreach($sidebar_left as $link): ?>
            <li class="menu-item <?php if($link['active']){echo "active";}?>">
                <a href="<?php echo URLROOT.$link['url'] ?>">
                    <i class="fas fa-<?php echo isset($link['icon']) ? $link['icon'] : 'layer-group'; ?>"></i>
                    <span><?php echo $link['label'] ?></span>
                </a>
            </li>
            <?php endforeach; ?>
            <div>
                <li class="menu-item">
                    <a href="<?php echo URLROOT; ?>/mainfeed">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Main Feed</span>
                    </a>
                </li>
            </div>
        </ul>
    </div>

    <div class="maincontent">
        <?php echo $content; ?>
    </div>
</div>

<style>
    .admin-popup-overlay {
        position: fixed;
        inset: 0;
        background: rgba(3, 8, 14, 0.72);
        backdrop-filter: blur(2px);
        z-index: 5000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .admin-popup-overlay.is-open {
        display: flex;
    }

    .admin-popup-card {
        width: min(520px, 92vw);
        background: #121a24;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.45);
        color: #e7edf6;
        overflow: hidden;
    }

    .admin-popup-header {
        padding: 0.95rem 1.1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.7rem;
    }

    .admin-popup-title {
        margin: 0;
        font-size: 1.02rem;
        font-weight: 700;
        color: #f7fbff;
    }

    .admin-popup-close {
        border: none;
        background: transparent;
        color: #a8b3c4;
        font-size: 1.2rem;
        cursor: pointer;
        line-height: 1;
    }

    .admin-popup-close:hover {
        color: #ffffff;
    }

    .admin-popup-body {
        padding: 1rem 1.1rem;
    }

    .admin-popup-message {
        margin: 0;
        color: #d3dbe8;
        line-height: 1.5;
        white-space: pre-line;
    }

    .admin-popup-input {
        width: 100%;
        margin-top: 0.85rem;
        min-height: 94px;
        resize: vertical;
        border-radius: 8px;
        border: 1px solid #3f4c61;
        background: #0f151d;
        color: #eaf0fa;
        padding: 0.7rem 0.75rem;
        font: inherit;
    }

    .admin-popup-input:focus {
        outline: none;
        border-color: #4a90e2;
        box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.25);
    }

    .admin-popup-footer {
        padding: 0.9rem 1.1rem 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        justify-content: flex-end;
        gap: 0.55rem;
        flex-wrap: wrap;
    }

    .admin-popup-btn {
        border: none;
        border-radius: 8px;
        padding: 0.5rem 0.9rem;
        cursor: pointer;
        font-weight: 700;
        font-size: 0.86rem;
    }

    .admin-popup-btn-cancel {
        background: #3b4656;
        color: #f3f6fb;
    }

    .admin-popup-btn-confirm {
        background: #2f7ef7;
        color: #ffffff;
    }

    .admin-popup-btn-confirm.is-danger {
        background: #c92a2a;
    }
</style>

<div id="adminPopupOverlay" class="admin-popup-overlay" aria-hidden="true">
    <div class="admin-popup-card" role="dialog" aria-modal="true" aria-labelledby="adminPopupTitle">
        <div class="admin-popup-header">
            <h3 id="adminPopupTitle" class="admin-popup-title">Notice</h3>
            <button id="adminPopupClose" type="button" class="admin-popup-close" aria-label="Close">&times;</button>
        </div>
        <div class="admin-popup-body">
            <p id="adminPopupMessage" class="admin-popup-message"></p>
            <textarea id="adminPopupInput" class="admin-popup-input" style="display:none;"></textarea>
        </div>
        <div class="admin-popup-footer">
            <button id="adminPopupCancel" type="button" class="admin-popup-btn admin-popup-btn-cancel">Cancel</button>
            <button id="adminPopupConfirm" type="button" class="admin-popup-btn admin-popup-btn-confirm">OK</button>
        </div>
    </div>
</div>

<script>
    (function () {
        if (window.AdminPopup) return;

        const overlay = document.getElementById('adminPopupOverlay');
        const titleEl = document.getElementById('adminPopupTitle');
        const messageEl = document.getElementById('adminPopupMessage');
        const inputEl = document.getElementById('adminPopupInput');
        const closeBtn = document.getElementById('adminPopupClose');
        const cancelBtn = document.getElementById('adminPopupCancel');
        const confirmBtn = document.getElementById('adminPopupConfirm');
        const cardEl = overlay ? overlay.querySelector('.admin-popup-card') : null;

        let resolver = null;
        let lastFocusedEl = null;
        let currentCfg = null;

        function cleanup() {
            if (!overlay) return;
            overlay.classList.remove('is-open');
            overlay.setAttribute('aria-hidden', 'true');
            inputEl.style.display = 'none';
            inputEl.value = '';
            confirmBtn.classList.remove('is-danger');
            currentCfg = null;

            if (lastFocusedEl && typeof lastFocusedEl.focus === 'function') {
                try {
                    lastFocusedEl.focus();
                } catch (e) {}
            }
            lastFocusedEl = null;
        }

        function resolveAndClose(payload) {
            const fn = resolver;
            resolver = null;
            cleanup();
            if (typeof fn === 'function') fn(payload);
        }

        function open(config) {
            if (!overlay) {
                return Promise.resolve({ confirmed: false, value: null });
            }

            if (resolver) {
                resolveAndClose({ confirmed: false, value: null });
            }

            currentCfg = config;
            titleEl.textContent = config.title || 'Notice';
            messageEl.textContent = config.message || '';

            cancelBtn.style.display = config.showCancel ? 'inline-block' : 'none';
            closeBtn.style.display = config.showCancel ? 'inline-block' : 'none';
            cancelBtn.textContent = config.cancelText || 'Cancel';
            confirmBtn.textContent = config.confirmText || 'OK';

            if (config.danger) {
                confirmBtn.classList.add('is-danger');
            } else {
                confirmBtn.classList.remove('is-danger');
            }

            if (config.prompt) {
                inputEl.style.display = 'block';
                inputEl.placeholder = config.placeholder || '';
                inputEl.value = config.defaultValue || '';
            } else {
                inputEl.style.display = 'none';
                inputEl.value = '';
            }

            lastFocusedEl = document.activeElement;
            overlay.classList.add('is-open');
            overlay.setAttribute('aria-hidden', 'false');

            setTimeout(() => {
                if (config.prompt) inputEl.focus();
                else confirmBtn.focus();
            }, 0);

            return new Promise((resolve) => {
                resolver = resolve;
            });
        }

        confirmBtn.addEventListener('click', () => {
            if (!currentCfg) return;
            if (currentCfg.prompt) {
                const value = inputEl.value;
                if (currentCfg.required && !value.trim()) {
                    inputEl.focus();
                    return;
                }
                resolveAndClose({ confirmed: true, value });
                return;
            }
            resolveAndClose({ confirmed: true, value: null });
        });

        function cancelCurrent() {
            resolveAndClose({ confirmed: false, value: null });
        }

        cancelBtn.addEventListener('click', cancelCurrent);
        closeBtn.addEventListener('click', cancelCurrent);

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay && currentCfg && currentCfg.showCancel) {
                cancelCurrent();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (!overlay.classList.contains('is-open')) return;
            if (e.key === 'Escape' && currentCfg && currentCfg.showCancel) {
                e.preventDefault();
                cancelCurrent();
                return;
            }
            if (e.key === 'Enter' && currentCfg && !currentCfg.prompt && e.target !== cancelBtn) {
                e.preventDefault();
                confirmBtn.click();
            }
        });

        window.AdminPopup = {
            alert(message, options = {}) {
                return open({
                    title: options.title || 'Notice',
                    message,
                    showCancel: false,
                    confirmText: options.confirmText || 'OK',
                    danger: !!options.danger
                }).then(() => undefined);
            },
            confirm(message, options = {}) {
                return open({
                    title: options.title || 'Please Confirm',
                    message,
                    showCancel: true,
                    confirmText: options.confirmText || 'Confirm',
                    cancelText: options.cancelText || 'Cancel',
                    danger: !!options.danger
                }).then((result) => !!result.confirmed);
            },
            prompt(message, defaultValue = '', options = {}) {
                return open({
                    title: options.title || 'Input Required',
                    message,
                    showCancel: true,
                    confirmText: options.confirmText || 'Submit',
                    cancelText: options.cancelText || 'Cancel',
                    danger: !!options.danger,
                    prompt: true,
                    defaultValue,
                    placeholder: options.placeholder || '',
                    required: !!options.required
                }).then((result) => (result.confirmed ? result.value : null));
            }
        };
    })();
</script>