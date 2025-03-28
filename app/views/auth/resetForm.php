<?php
$title = "Reset Your Password";
include __DIR__ . '/../inc/header.php';
?>
<div class="reset-form-container">
    <h1>Reset Your Password</h1>
    <?php
    include __DIR__ . '/../partials/alert.php';
    ?>

    <?php if (isset($token)): ?>
        <form action="index.php?url=auth/doReset" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            
            <button type="submit">Reset Password</button>
        </form>
    <?php else: ?>
        <p>Invalid or expired token.</p>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../inc/footer.php'; ?>
