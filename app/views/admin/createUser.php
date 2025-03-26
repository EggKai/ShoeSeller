<?php
$title = "Create New User";
include __DIR__ . '/../inc/header.php';
?>
<div id="authForm">
    <h1>Create New User</h1>
    <?php if (isset($alert)): ?>
        <div class="alert"><?php echo htmlspecialchars($alert[0]); ?></div>
    <?php endif; ?>
    <form action="index.php?url=admin/doCreateUser" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
        
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" placeholder="Enter full name" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter email address" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter password" required>
        
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
        
        <label for="admin_password">Your Admin Password:</label>
        <input type="password" id="admin_password" name="admin_password" placeholder="Enter your admin password" required>
        
        <label for="user_type">User Type:</label>
        <select id="user_type" name="user_type" required>
            <option value="">--Select User Type--</option>
            <option value="employee">Employee</option>
            <option value="admin">Admin</option>
        </select>
        <hr>
        <button type="submit" name="submit">Create User</button>
    </form>
</div>
<?php include __DIR__ . '/../inc/footer.php'; ?>
