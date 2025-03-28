<?php
require_once __DIR__ . "/../../../core/barcode.php";
$title = "My Profile";
include __DIR__ . '/../inc/header.php';
?>
<div class="profile-page">
  <div class="profile-card">
    <div class="profile-pic-container">
      <?php if (!empty($user['profile_pic'])): ?>
        <img class="profile-pic" src="public/uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture">
      <?php else: ?>
        <img class="profile-pic" src="public/assets/images/default-profile.jpg" alt="Default Profile Picture">
      <?php endif; ?>
    </div>
    <div class="profile-info">
      <h1 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h1>
      <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
    </div>
    <div class="barcode-container">
        <?php
            echo generate_barcode($user['id'])
        ?>
    </div>
    
    <a href="index.php?url=auth/editProfile" class="edit-profile-btn">Edit Profile</a>
  </div>
</div>
<?php include __DIR__ . '/../inc/footer.php'; ?>
