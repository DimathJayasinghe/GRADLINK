<?php ob_start(); ?>
<style>
 .event-detail{ max-width:900px; margin:0 auto; }
 .event-detail img{ width:100%; max-height:420px; object-fit:cover; border-radius:8px; }
</style>
<?php $styles = ob_get_clean(); ?>

<?php ob_start(); ?>
<div class="event-detail">
    <?php if(!$data['event']): ?>
        <h2>Event not found</h2>
    <?php else: $e = $data['event']; ?>
        <h1><?php echo htmlspecialchars($e->title); ?></h1>
        <p style="color:var(--muted)">By <?php echo htmlspecialchars($e->organizer_name ?? '');?> • <?php echo date('M d, Y H:i', strtotime($e->start_datetime)); ?></p>
        <?php if(!empty($e->attachment_image)): ?>
            <img src="<?php echo htmlspecialchars(URLROOT . '/media/post/' . $request->attachment_image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($e->title); ?>">
        <?php endif; ?>
        <div style="margin-top:12px;">
            <?php echo nl2br(htmlspecialchars($e->description)); ?>
        </div>
        <div style="margin-top:18px;">
            <a href="<?php echo URLROOT; ?>/calender" class="btn btn-back">Back to calendar</a>
        </div>
    <?php endif; ?>
</div>
<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/calender/v_layout_adapter.php'; ?>
