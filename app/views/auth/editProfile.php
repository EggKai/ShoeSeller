<?php
$title = "Edit Profile";
include __DIR__ . '/../inc/header.php';
?>
<div class="edit-profile-container">
    <h1>Edit Profile</h1>
    <?php
    include __DIR__ . '/../partials/alert.php';
    ?>
    <form action="index.php?url=auth/doEditProfile" method="POST" enctype="multipart/form-data" id="authForm">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">

        <!-- Name -->
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>

        <!-- Email -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
            required>

        <!-- Address -->
        <label for="address">Address:</label>
        <textarea id="address" name="address" rows="4"
            placeholder="Enter your address..."><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>

        <!-- Profile Picture -->
        <label for="profile_pic">Profile Picture:</label>
        <?php if (!empty($user['profile_pic'])): ?>
            <div class="current-profile-pic">
                <img src="public/uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>"
                    alt="Current Profile Picture">
            </div>
        <?php endif; ?>
        <input type="file" id="profile_pic" name="profile_pic" accept="image/png, image/jpeg, image/avif">

        <!-- Submit -->
        <button type="submit" name="submit" class="submit-btn">Save Changes</button>
    </form>
</div>
<?php include __DIR__ . '/../inc/footer.php'; ?>