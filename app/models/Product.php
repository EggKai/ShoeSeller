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
        $stmt = $this->pdo->prepare("SELECT size, stock FROM product_sizes WHERE product_id = :product_id ORDER BY size ASC");
        $stmt->execute(['product_id' => $shoeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function searchProductByQuery($query) {
        $stmt = $this->pdo->prepare("
            SELECT *,
                   (p.name LIKE :queryPrefix OR p.name LIKE CONCAT('% ', :queryPrefix)) AS starts_with
            FROM products p
            WHERE p.name LIKE :query
            ORDER BY starts_with DESC, p.name
        "); //prio items that start with query (users are more likely to search those)
        $stmt->execute([
            'query' => '%' . $query . '%',
            'queryPrefix' => $query . '%'
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllCategories() {
        return $this->findAll('categories');
    }
    /**
     * Insert a new product into the products table.
     *
     * @param string $name        Product name.
     * @param string $brand       Product brand.
     * @param float  $price       Base price for the product.
     * @param string $description Product description.
     * @param string $thumbnail   Image filename for the product.
     * @param string $category_id Category ID.
     * @return int|false          The new product's ID on success or false on failure.
     */
    public function createProduct($name, $brand, $price, $description, $thumbnail, $category_id) {
        $stmt = $this->pdo->prepare("
            INSERT INTO products (name, brand, base_price, description, image_url, category_id)
            VALUES (:name, :brand, :price, :description, :thumbnail, :category_id)
        ");
        
        $params = [
            'name'        => $name,
            'brand'       => $brand,
            'price'       => $price,
            'description' => $description,
            'thumbnail'   => $thumbnail,
            'category_id' => $category_id
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
     * @return bool             True on success, false on failure.
     */
    public function addProductSize($productId, $size, $stock) {
        $stmt = $this->pdo->prepare("
            INSERT INTO product_sizes (product_id, size, stock)
            VALUES (:product_id, :size, :stock)
        ");
        
        $params = [
            'product_id' => $productId,
            'size'       => $size,
            'stock'      => $stock,
        ];
        
        return $stmt->execute($params);
    }
    /**
     * Update the stock for an existing product size.
     *
     * @param int    $productId The product ID.
     * @param string $size      The size.
     * @param int    $stock     The new stock value.
     * @return bool             True on success, false on failure.
     */
    public function updateProductSize($productId, $size, $stock) {
        $stmt = $this->pdo->prepare("UPDATE product_sizes SET stock = :stock WHERE product_id = :product_id AND size = :size");
        return $stmt->execute([
        'stock'       => $stock,
        'product_id'  => $productId,
        'size'        => $size
        ]);
    }

    /**
     * Delete a specific product size.
     *
     * @param int    $productId The product ID.
     * @param string $size      The size to delete.
     * @return bool             True on success, false on failure.
     */
    public function deleteProductSize($productId, $size) {
        $stmt = $this->pdo->prepare("DELETE FROM product_sizes WHERE product_id = :product_id AND size = :size");
        return $stmt->execute([
        'product_id'  => $productId,
        'size'        => $size
        ]);
    }

    public function updateProduct($id, $name, $brand, $price, $description, $thumbnail = null) {
        $sql = "UPDATE products SET name = :name, brand = :brand, base_price = :price, description = :description";
        if ($thumbnail !== null) {
            $sql .= ", image_url = :thumbnail";
        } //builds sql statement dyanimically based on whether there was a thumbnail
        $sql .= " WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $params = [
            'name'        => $name,
            'brand'       => $brand,
            'price'       => $price,
            'description' => $description,
            'id'          => $id
        ];
        if ($thumbnail !== null) {
            $params['thumbnail'] = $thumbnail;
        } 
        
        return $stmt->execute($params);
    }
    
}
?>