<?php
$title = "Reset Your Password";
include __DIR__ . '/../inc/header.php';
?>
<div class="reset-form-container __content">

    <?php
    include __DIR__ . '/../partials/alert.php';
    ?>

    <form action="index.php?url=auth/doReset" method="POST" id="authForm">
        <?php if (isset($token)): ?>
            <h1>Reset Your Password</h1>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <p>
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </p>
            <p>
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter Password"
                    required>
            </p>
            <input type="submit" name="submit" value="Submit">
        <?php else: ?>
            <h1>Invalid or expired token.</h1>
        <?php endif; ?>
    </form>
</div>
<?php include __DIR__ . '/../inc/footer.php'; ?>