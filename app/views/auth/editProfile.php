<?php
$title = "Edit Profile";
include __DIR__ . '/../inc/header.php';
?>
<div class="edit-profile-container">
    <?php
    include __DIR__ . '/../partials/alert.php';
    ?>
    <form action="/auth/doEditProfile" method="POST" enctype="multipart/form-data" id="authForm">
        <h1>Edit Profile</h1>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">

        <!-- Name -->
        <p>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>"
                required>
        </p>
        <!-- Email -->
        <p>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                required>
        </p>
        <!-- Address -->
        <p>
            <label for="address">Address:</label>
            <textarea id="address" name="address" rows="4"
                placeholder="Enter your address..."><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
        </p>

        <!-- Profile Picture -->
        <p>
            <label for="profile_pic">Profile Picture:</label>
        <div class="profile-pic-container">
            <?php if (!empty($user['profile_pic'])): ?>
                <img class="profile-pic" src="data:image/jpeg;base64,<?php echo base64_encode($user['profile_pic']); ?>"
                    alt="Profile Picture">
            <?php else: ?>
                <img class="profile-pic" src="/public/assets/images/default-profile.jpg" alt="Default Profile Picture">
            <?php endif; ?>
        </div>
        <input type="file" id="profile_pic" name="profile_pic" accept="image/png, image/jpeg, image/avif">
        </p>

        <!-- Submit -->
        <input type="submit" name="submit" class="submit-btn" value="Save Changes"></input>
    </form>
</div>
<?php include __DIR__ . '/../inc/footer.php'; ?>