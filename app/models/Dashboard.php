<?php
require_once __DIR__ . '/../../core/Model.php';

class Dashboard extends Model {

    /**
     * Get total revenue from orders that have been paid/shipped/completed.
     */
    public function getTotalRevenue() {
        $stmt = $this->pdo->query("SELECT COALESCE(SUM(total_price), 0) AS total_revenue FROM orders WHERE status IN ('paid', 'shipped', 'completed')");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)$result['total_revenue'];
    }

    /**
     * Get total shoes sold by summing up quantities from order_items
     * for orders with a successful status.
     */
    public function getTotalShoesSold() {
        $stmt = $this->pdo->query("
            SELECT COALESCE(SUM(oi.quantity), 0) AS total_shoes_sold 
            FROM order_items oi 
            JOIN orders o ON oi.order_id = o.id 
            WHERE o.status IN ('paid', 'shipped', 'completed')
        ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total_shoes_sold'];
    }

    /**
     * Get total sales (i.e., count of orders that are successful).
     */
    public function getTotalSales() {
        $stmt = $this->pdo->query("SELECT COUNT(*) AS total_sales FROM orders WHERE status IN ('paid', 'shipped', 'completed')");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total_sales'];
    }

    /**
     * Get total users.
     */
    public function getTotalUsers() {
        $stmt = $this->pdo->query("SELECT COUNT(*) AS total_users FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total_users'];
    }

    /**
     * Get revenue per day.
     * Returns an array of ['day' => 'YYYY-MM-DD', 'revenue' => amount].
     */
    public function getRevenuePerDay() {
        $stmt = $this->pdo->query("
            SELECT DATE(created_at) AS day, COALESCE(SUM(total_price), 0) AS revenue 
            FROM orders 
            WHERE status IN ('paid', 'shipped', 'completed')
            GROUP BY DATE(created_at)
            ORDER BY day
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get shoes by category (number of products per category).
     */
    public function getShoesByCategory() {
        $stmt = $this->pdo->query("
            SELECT c.name AS category, COUNT(p.id) AS count 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            GROUP BY c.id
            ORDER BY count DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get shoes sold by category from order_items.
     */
    public function getShoesSoldByCategory() {
        $stmt = $this->pdo->query("
            SELECT c.name AS category, COALESCE(SUM(oi.quantity), 0) AS shoes_sold 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            JOIN categories c ON p.category_id = c.id 
            JOIN orders o ON oi.order_id = o.id 
            WHERE o.status IN ('paid', 'shipped', 'completed')
            GROUP BY c.id
            ORDER BY shoes_sold DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get reviews written per day.
     */
    public function getReviewsPerDay() {
        $stmt = $this->pdo->query("
            SELECT DATE(created_at) AS day, COUNT(*) AS reviews 
            FROM reviews 
            GROUP BY DATE(created_at)
            ORDER BY day
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Aggregate all dashboard data.
     *
     * @return array
     */
    public function getDashboardData() {
        return [
            'totalRevenue'         => $this->getTotalRevenue(),
            'totalShoesSold'       => $this->getTotalShoesSold(),
            'totalSales'           => $this->getTotalSales(),
            'totalUsers'           => $this->getTotalUsers(),
            'revenuePerDay'        => $this->getRevenuePerDay(),
            'shoesByCategory'      => $this->getShoesByCategory(),
            'shoesSoldByCategory'  => $this->getShoesSoldByCategory(),
            'reviewsPerDay'        => $this->getReviewsPerDay(),
        ];
    }
}
