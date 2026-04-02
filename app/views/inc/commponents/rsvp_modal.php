<?php
// Simple reusable RSVP modal markup. Styles kept inline to avoid touching global CSS.
?>
<div id="rsvp-modal" class="rsvp-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); align-items:center; justify-content:center; z-index:1100;">
  <div style="background:var(--bg); padding:20px; border-radius:8px; width:360px; max-width:90%;">
    <h3 style="margin-top:0;">RSVP</h3>
    <div style="margin-bottom:8px;"><strong id="rsvp-event-title">Event</strong></div>
    <div style="margin-bottom:12px;">
      <label style="display:block;margin-bottom:6px;">Your response</label>
      <label style="display:block;"><input type="radio" name="rsvp-status" value="attending" checked> Attending</label>
      <label style="display:block;"><input type="radio" name="rsvp-status" value="maybe"> Maybe</label>
      <label style="display:block;"><input type="radio" name="rsvp-status" value="not_attending"> Not attending</label>
    </div>
    <div style="display:flex; gap:8px; justify-content:flex-end;">
      <button id="rsvp-cancel" class="btn btn-back">Cancel</button>
      <button id="rsvp-confirm" class="btn btn-primary">Confirm</button>
    </div>
  </div>
</div>
