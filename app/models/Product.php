<?php
require_once __DIR__ . '/../../core/Model.php';

class Product extends Model {
    public function getAllProducts() {
        return $this->findAll('products');
    }

    public function getProductById($id) {
        return $this->findById('products', $id);
    }
    public function getSizesByShoeId($shoeId) {
        $stmt = $this->pdo->prepare("SELECT size, stock FROM product_sizes WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $shoeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>