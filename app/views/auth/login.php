<?php
$title = "Login";
include_once __DIR__ . '/../inc/header.php';
?>

<form id="authForm" action="index.php?url=auth/doLogin" method="POST" class="authForm">
    <?php
        include __DIR__ . '/../partials/alert.php'; 
    ?>
    <h1>Login:</h1>
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
    <p>
        <input type="text" name="email" placeholder="Email"
            value="<?php echo  htmlspecialchars($data['email'] ?? ($_COOKIE['email'] ?? "")) ?>" required /><br>
    </p>
    <p>
        <input type="password" name="password" placeholder="Password" required /><br>
    </p>
    <input type="submit" name="submit" value="Login">

    <a href="index.php?url=auth/register">Don't Have an account? Register Now!</a><br>
    <a class="align-right" href="index.php?url=auth/forgotPassword">Forgot Password?</a><br>
    Remember me:<input type="checkbox" name="remember">
    <!-- <a style="float: right;" href="reactivateAccount.php">Reactivate Account?</a> -->
</form>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>