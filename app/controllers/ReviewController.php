<?php
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/HomeController.php';
require_once __DIR__ . '/ProductController.php';


class ReviewController extends Controller {
    public const PATH = "partials";

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            (new HomeController())->index();
            exit;
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            die("Unauthorized");
        }
        
        // Validate CSRF token if needed
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) {
            die("Invalid request");
        }
        
        $productId = filter_input(INPUT_GET, 'product_id', FILTER_SANITIZE_NUMBER_INT);
        $rating    = filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_INT);
        $title     = filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS);
        $text      = filter_var($_POST['review_text'], FILTER_SANITIZE_SPECIAL_CHARS);
        $userId    = $_SESSION['user']['id']; // get userid from current session
        
        // Insert the review into the database
        $reviewModel = new Review();
        $success = $reviewModel->createReview($productId, $userId, $rating, $title, $text);
        
        if ($success) {
            // Redirect back to product page
            header("Location: product/detail&id=$productId");
            exit;
        } else {
            // Show error or redirect
            die("Failed to create review");
        }
    }
    public function reviews($id) {
        $this->view(self::PATH . '/reviews', ['reviews' => (new Review())->getAllReviewsByProductId($id), 'options' => ['reviews'], 'csrf_token' => Csrf::generateToken()]);
    }
}