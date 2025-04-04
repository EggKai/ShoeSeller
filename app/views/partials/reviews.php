<div class="reviews">
<?php if (isset($_SESSION['user'])): ?>
  <div class="new-review-form">
      <h3>Write a Review</h3>
      <form action="index.php?url=products/createReview&id=<?php echo htmlspecialchars($product['id']); ?>" method="POST">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
          
          <!-- Star Rating -->
            <div class="rating">
                <?php for ($star=5; $star > 0; $star--) { ?>
                    <input type="radio" id="star<?php echo $star?>" name="rating" value="<?php echo $star?>"
                        <?php echo (($reviewAssoc['rating'] ?? 0) == $star) ? "checked" : ""; ?> />
                    <label class="star" for="star<?php echo $star?>" title="Awesome" aria-hidden="true"></label>
                <?php } ?>
            </div>
          <!-- <label for="title">Review Title:</label> -->
          <input type="text" id="title" name="title" placeholder="Brief summary" required>
          
          <!-- <label for="review_text">Your Review:</label> -->
          <textarea id="review_text" name="review_text" rows="4" placeholder="Share your experience..." required></textarea>
          
          <button type="submit" name="submit" class="submit-review-btn">Submit Review</button>
      </form>
  </div>
<?php else: ?>
  <p>You must <a href="/auth/login">log in</a> to write a review.</p>
<?php endif; ?>
    

    <?php if (!empty($reviews)): ?>
        <h3>Reviews</h3>
        <?php foreach ($reviews as $review): ?>
            <div class="review-item">
                <div class="review-top">
                    <div class="review-rating">
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $review['rating'] ? '<span class="star">&#9733;</span>' : '<span class="star">&#9734;</span>';
                        }
                        ?>
                    </div>
                    <div class="review-user-date">
                        <span class="review-user"><?php echo htmlspecialchars($review['user_name']); ?></span><br>
                        <span class="review-date"><?php echo htmlspecialchars($review['created_at']); ?></span>
                    </div>
                </div>
                <h3 class="review-title"><?php echo htmlspecialchars($review['title']); ?></h3>
                <p class="review-text"><?php echo htmlspecialchars($review['review_text']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No reviews exist for this product yet.</p>
    <?php endif; ?>

</div>