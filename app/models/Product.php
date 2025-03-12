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
    public function searchProductByQuery($query) {
        $stmt = $this->pdo->prepare("SELECT * FROM products p WHERE p.name LIKE :query");
        $stmt->execute(['query' => '%' . $query . '%']);
        return $stmt->fetchAll(mode: PDO::FETCH_ASSOC);
    }
    /**
     * Insert a new product into the products table.
     *
     * @param string $name        Product name.
     * @param string $brand       Product brand.
     * @param float  $price       Base price for the product.
     * @param string $description Product description.
     * @param string $thumbnail   Image filename for the product.
     * @return int|false          The new product's ID on success or false on failure.
     */
    public function createProduct($name, $brand, $price, $description, $thumbnail) {
        $stmt = $this->pdo->prepare("
            INSERT INTO products (name, brand, base_price, description, image_url)
            VALUES (:name, :brand, :price, :description, :thumbnail)
        ");
        
        $params = [
            'name'        => $name,
            'brand'       => $brand,
            'price'       => $price,
            'description' => $description,
            'thumbnail'   => $thumbnail
        ];
        
        if ($stmt->execute($params)) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Insert a new product size entry into the product_sizes table.
     *
     * @param int    $productId The ID of the product.
     * @param string $size      The size value.
     * @param int    $stock     The available stock for this size.
     * @param float  $price     Price for this size (usually same as product's base_price).
     * @return bool             True on success, false on failure.
     */
    public function addProductSize($productId, $size, $stock, $price) {
        $stmt = $this->pdo->prepare("
            INSERT INTO product_sizes (product_id, size, stock, price)
            VALUES (:product_id, :size, :stock, :price)
        ");
        
        $params = [
            'product_id' => $productId,
            'size'       => $size,
            'stock'      => $stock,
            'price'      => $price
        ];
        
        return $stmt->execute($params);
    }
}
?>