<?php ob_start(); ?>
<style>
 .events-grid{ display:grid; grid-template-columns: repeat(auto-fill,minmax(260px,1fr)); gap:16px; }
 .event-card{ background:var(--card); border:1px solid var(--border); padding:12px; border-radius:8px; }
 .event-card img{ width:100%; height:140px; object-fit:cover; border-radius:6px; }
</style>
<?php $styles = ob_get_clean(); ?>

<?php ob_start(); ?>
<div>
    <h2>Events</h2>
    <?php if(!empty($data['events'])): ?>
        <div class="events-grid">
            <?php foreach($data['events'] as $e): ?>
                <div class="event-card">
                    <?php if(!empty($e->attachment_image)): ?>
                        <img src="<?php echo M_event_image::getUrl($e->attachment_image); ?>" alt="<?php echo htmlspecialchars($e->title); ?>">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($e->title); ?></h3>
                    <p style="color:var(--muted)"><?php echo htmlspecialchars(substr($e->description,0,140)); ?><?php echo strlen($e->description)>140? '...':''; ?></p>
                    <p style="font-size:13px; color:var(--muted)"><?php echo date('M d, Y H:i', strtotime($e->start_datetime)); ?></p>
                    <a href="<?php echo URLROOT; ?>/event/show/<?php echo urlencode($e->id); ?>" class="btn btn-primary">View</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No events found.</p>
    <?php endif; ?>
</div>
<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/calender/v_layout_adapter.php'; ?>
