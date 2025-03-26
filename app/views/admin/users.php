<?php
$title = "All Users";
include __DIR__ . '/../inc/header.php';
?>
<div class="view-users-container __content">
  <h1>All Users</h1>
  <?php if (!empty($users)): ?>
    <div class="table-responsive">
      <table class="users-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>User Type</th>
            <th>Points</th>
            <th>Created At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr id="user-row-<?php echo htmlspecialchars($user['id']); ?>">
              <td><?php echo htmlspecialchars($user['id']); ?></td>
              <td><?php echo htmlspecialchars($user['name']); ?></td>
              <td><?php echo htmlspecialchars($user['email']); ?></td>
              <td><?php echo htmlspecialchars($user['user_type']); ?></td>
              <td><?php echo htmlspecialchars($user['points']); ?></td>
              <td><?php echo htmlspecialchars($user['created_at']); ?></td>
              <td>
                <button class="delete-user-btn" data-user-id="<?php echo htmlspecialchars($user['id']); ?>">Delete</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p>No users found.</p>
  <?php endif; ?>
</div>
<button class="floating-plus-button" name="add-user" type="button">
    <a href="admin/createUser">
      +
    </a>
  </button>
<!-- Hidden CSRF Token -->
<input type="hidden" id="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

<!-- Delete Modal -->
<div id="delete-modal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Confirm Deletion</h3>
    <p>Please enter your admin password to confirm deletion:</p>
    <input type="password" id="admin-password" placeholder="Enter password">
    <button id="confirm-delete-btn">Confirm</button>
  </div>
</div>

<?php include __DIR__ . '/../inc/footer.php'; ?>
