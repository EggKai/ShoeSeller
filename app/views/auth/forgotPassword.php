<?php
$title = "Forgot Password";
include_once __DIR__ . '/../inc/header.php';
?>

<form id="authForm" action="index.php?url=auth/doLogin" method="POST" class="authForm">
    <?php
        include __DIR__ . '/../partials/alert.php'; 
    ?>
    <h1>Reset Password:</h1>
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
    <p>
        <input type="text" name="email" placeholder="Email"
            value="<?php echo  htmlspecialchars($data['email'] ?? ($_COOKIE['email'] ?? "")) ?>" required /><br>
    </p>
    <input type="submit" name="submit" value="Send Request">

    <a class="align-right" href="index.php?url=auth/login">Remember Your Password?</a><br>
</form>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>