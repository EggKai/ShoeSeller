<?php
class Review extends Model{
    public function createReview($productId, $userId, $rating, $title, $text) {
        $stmt = $this->pdo->prepare("
            INSERT INTO reviews (product_id, user_id, rating, title, review_text, created_at)
            VALUES (:product_id, :user_id, :rating, :title, :review_text, NOW())
        ");
        return $stmt->execute([
            'product_id' => $productId,
            'user_id'    => $userId,
            'rating'     => $rating,
            'title'      => $title,
            'review_text'=> $text
        ]);
    }
    /**
     * Retrieve all reviews for a given product.
     *
     * @param int $productId The product ID.
     * @return array An array of reviews.
     */
    public function getAllReviewsByProductId($productId) {
        $stmt = $this->pdo->prepare("
            SELECT r.*, u.name AS user_name
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.product_id = :product_id
            ORDER BY r.created_at DESC
        ");
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    
}
?>