<?php if (session_has("Error", true)):?>
    <div class="alert alert-danger"><?=session("Error", "Failed Operation", true)?></div>
<?php endif; ?>

<?php if (session_has("Success", true)):?>
    <div class="alert alert-success"><?=session("Success", "Success Operation", true)?></div>
<?php endif; ?>
