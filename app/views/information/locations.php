<?php
$title = 'Locations';
include __DIR__ . '/../inc/header.php';
?>
<div id="loader" class="lds-default"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
<div class="main-container">
	<div class="locations-container">
		<div class="headings">
			<h1 hidden>Find a ShoeSeller Store</h1>
			<h2>Find a ShoeSeller Store</h2>
			<div class="search-bar">
				<input type="text" id="searchInput" onkeyup="filterLocations()" placeholder="Search locations...">
			</div>
		</div>

		<!-- Location List !-->
		<div class="location-list-container" tabindex="0">
		<?php if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] === 'admin') { ?>

			<!-- Add Button ADMIN ONLY -->
			<div class="add-button-container">
				<button onclick="toggleAddLocationForm()" class="add-button-button">+</button>
			</div>
			
			<!-- Add Form ADMIN ONLY -->
			<div class="add-form-container">
				<form class="new-location-item" onsubmit="addLocation(event)">
					<input type="text" name="name" placeholder="Name" required>
					<input type="text" name="address" placeholder="Address" required>
					<input type="text" name="country" placeholder="Country" required>
					<input type="text" name="zip_code" placeholder="ZIP Code" required>
					<input type="text" name="phone" placeholder="Phone" required>
					<input type="text" name="latitude" placeholder="Latitude" required>
					<input type="text" name="longitude" placeholder="Longitude" required>
					<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
					<input type="hidden" name="submit" value="1">
					<div class="buttons">
						<button type="submit" name="submit">Add</button>
						<button type="button" onclick="discardAddLocationForm()">Discard</button>
					</div>
				</form>
			</div>
		<?php } ?>

			<?php if (!empty($locations)): ?>
				<input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf_token); ?>">
				<div class="location-list">
					<?php foreach ($locations as $item): ?>

						<div class="location-item" id="location-<?php echo htmlspecialchars($item['id']); ?>" 
							onclick="updateMap(<?php echo htmlspecialchars($item['latitude']); ?>, <?php echo htmlspecialchars($item['longitude']); ?>)">
							<div class="item">
								<div hidden class="latitude"><?php echo htmlspecialchars($item['latitude']); ?></div>
								<div hidden class="longitude"><?php echo htmlspecialchars($item['longitude']); ?></div>

								<div class="name"><strong><?php echo htmlspecialchars($item['name']); ?></strong></div>
								<div class="details">
									<div class="address"><?php echo htmlspecialchars($item['address']); ?></div>
									<div class="country"><?php echo htmlspecialchars($item['country']); ?>,
										<span class="zip_code"><?php echo htmlspecialchars($item['zip_code']); ?></span>
									</div>
									<div class="phone"><?php echo htmlspecialchars($item['phone']); ?></div>
								</div>
								<?php if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] === 'admin') { ?>
									<div class="actions-container">
										<button onclick="updateLocation(<?php echo htmlspecialchars($item['id']); ?>)" class="update-location-button">Edit</button>
										<button onclick="removeLocation(<?php echo htmlspecialchars($item['id']); ?>)" class="delete-location-button">Delete</button>
									</div>
								<?php } ?>
							</div>

							<?php if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] === 'admin') { ?>

							<!-- Edit Form ADMIN ONLY -->
							<div class="edit-form-container" id="edit-<?php echo htmlspecialchars($item['id']); ?>">
								<form onsubmit="saveLocation(event, <?php echo htmlspecialchars($item['id']); ?>)">
									<input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id']); ?>" required>
									<input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required>
									<input type="text" name="address" value="<?php echo htmlspecialchars($item['address']); ?>" required>
									<input type="text" name="country" value="<?php echo htmlspecialchars($item['country']); ?>" required>
									<input type="text" name="zip_code" value="<?php echo htmlspecialchars($item['zip_code']); ?>" required>
									<input type="text" name="phone" value="<?php echo htmlspecialchars($item['phone']); ?>" required>
									<input type="text" name="latitude" value="<?php echo htmlspecialchars($item['latitude']); ?>" required>
									<input type="text" name="longitude" value="<?php echo htmlspecialchars($item['longitude']); ?>" required>
									<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
									<input type="hidden" name="submit" value="1">
									<div class="buttons">
										<button type="submit">Save</button>
										<button type="button" onclick="discardChanges(event, <?php echo htmlspecialchars($item['id']); ?>)">Discard</button>
									</div>
								</form>
							</div>
							<?php } ?>

						</div>
					<?php endforeach; ?>
				</div>
			<?php else: ?>
				<p>There are no locations available.</p>
			<?php endif; ?>
		</div>

	</div>

	<!-- Google Map !-->
	<div class="iframe-container">
		<iframe id="mapFrame"
			title="interactive-map"
			src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7175.060941233249!2d103.90894078720245!3d1.412267617281438!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31da1515bfb2d263%3A0xc71c56458ac08497!2sSingapore%20Institute%20of%20Technology%20(Campus%20Court)!5e0!3m2!1sen!2ssg!4v1742530812666!5m2!1sen!2ssg"
			allowfullscreen=""
			loading="lazy"
			referrerpolicy="no-referrer-when-downgrade">
		</iframe>
	</div>
</div>

<?php
include __DIR__ . '/../inc/footer.php';
?>