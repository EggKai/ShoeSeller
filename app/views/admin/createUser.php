<?php
$title = "Create New User";
include __DIR__ . '/../inc/header.php';
?>
<div id="authForm">
    <h1>Create New User</h1>
    <?php
    include __DIR__ . '/../partials/alert.php';
    ?>
    <form action="/admin/doCreateUser" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
        <p>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter full name" value="<?php echo $data['name']??'';?>" required>
        </p>
        <p>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter email address" value="<?php echo $data['email']??'';?>" required>
        </p>
        <p>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter password" value="<?php echo $data['password']??'';?>" required>
        </p>
        <p>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" value="<?php echo $data['confirm_password']??'';?>" required>
        </p>        
        <p>
            <label for="admin_password">Your Admin Password:</label>
            <input type="password" value="<?php echo $data['admin_password']??'';?>" id="admin_password" name="admin_password" placeholder="Enter your admin password" required>
        </p>
        <p>
        <label for="user_type">User Type:</label>
        <select id="user_type" name="user_type" required>
            <option value="">--Select User Type--</option>
            <option value="employee">Employee</option>
            <option value="admin">Admin</option>
        </select>
        </p>
        
        <button type="submit" name="submit">Create User</button>
    </form>
</div>
<?php include __DIR__ . '/../inc/footer.php'; ?>
