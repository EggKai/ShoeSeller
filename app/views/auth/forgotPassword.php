<?php
$title = "Forgot Password";
include_once __DIR__ . '/../inc/header.php';
?>
<div class="__content">
<form id="authForm" action="/auth/resetPassword" method="POST" class="authForm">
    <?php
        include __DIR__ . '/../partials/alert.php'; 
    ?>
    <h1>Reset Password:</h1>
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
    <p>
        <input type="text" name="email" placeholder="Email" value="<?php echo  htmlspecialchars($data['email'] ?? ($_COOKIE['email'] ?? "")) ?>" required /><br>
    </p>
    <input type="submit" name="submit" value="Send Request">

    <a class="align-right" href="/auth/login">Remember Your Password?</a><br>
</form>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>