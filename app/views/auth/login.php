<?php
$title = "Login";
include_once __DIR__ . '/../inc/header.php';
?>
<div class="__content">
    <form id="authForm" action="/auth/doLogin" method="POST" class="authForm">
        <?php include __DIR__ . '/../partials/alert.php'; ?>
        <h1>Login:</h1>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <p>
            <input type="text" name="email" placeholder="Email"
            value="<?php echo  htmlspecialchars($data['email'] ?? ($_COOKIE['email'] ?? "")) ?>" required /><br>
        </p>
        <p>
            <input type="password" name="password" placeholder="Password" required /><br>
        </p>
        <p>
            <div class="g-recaptcha" data-sitekey="<?php echo $_ENV['GOOGLE_CAPTCHA_SITE_KEY']?>"></div>
        </p>
        <input type="submit" name="submit" value="Login">
        <p>
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Remember me</label>
        </p>
        <!-- <a style="float: right;" href="reactivateAccount.php">Reactivate Account?</a> -->
        <br>
        <a href="/auth/register">Don't Have an account? Register Now!</a><br>
    </form>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>
