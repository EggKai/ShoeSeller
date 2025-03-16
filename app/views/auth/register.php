<?php
$title = "Register";
include_once __DIR__ . '/../inc/header.php';
?>
<div class="__content">
<form id="authForm" action="index.php?url=auth/doRegister" method="POST">
    <?php
    include __DIR__ . '/../partials/alert.php';
    ?>
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
    <h1>Register:</h1>
    <div class="tab">Email:
        <p><input placeholder="E-mail" oninput="this.className = ''" name="email"
                value="<?php echo htmlspecialchars($data['email'] ?? "") ?>"></p>
    </div>
    <div class="tab">Login Info:
        <p><input placeholder="Password" oninput="this.className = ''" name="password" type="password"></p>
        <p><input placeholder="Confirm Your Password" oninput="this.className = ''" name="cnfmpassword" type="password">
        </p>
    </div>
    <div class="tab">Contact Info:
        <p><input placeholder="Name" oninput="this.className = ''" name="name"
                value="<?php echo htmlspecialchars($data['name'] ?? "") ?>"></p>
        <p><input placeholder="Address" oninput="this.className = ''" name="address"
                value="<?php echo htmlspecialchars($data['address'] ?? "") ?>"></p>
    </div>
    <div style="overflow:auto;">
        <div style="float:right;">
            <button class="stepButton" type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
            <button class="stepButton" type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
        </div>
    </div>
    <a style="float:left;" href="index.php?url=auth/login">Already Have an Account? Login Now!</a>
    <div class="steps">
        <span class="step"></span>
        <span class="step"></span>
        <span class="step"></span>
    </div>
</form>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>