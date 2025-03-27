<?php
require_once __DIR__ . '/../../core/Model.php';

class Product extends Model
{
    public function getAllProducts()
    {
        return $this->findAll('products');
    }
    public function getAllListedProducts()
    {
        $stmt = $this->pdo->query("SELECT * FROM products WHERE unlisted = 0");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id)
    {
        return $this->findById('products', $id);
    }
    public function getSizesByShoeId($shoeId)
    {
        $stmt = $this->pdo->prepare("SELECT size, stock FROM product_sizes WHERE product_id = :product_id ORDER BY size ASC");
        $stmt->execute(['product_id' => $shoeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function searchProductByQuery($query)
    {
        $stmt = $this->pdo->prepare("
            SELECT *,
                   (p.name LIKE :queryPrefix OR p.name LIKE CONCAT('% ', :queryPrefix)) AS starts_with
            FROM products p
            WHERE p.name LIKE :query AND unlisted = 0
            ORDER BY starts_with DESC, p.name
        "); //prio items that start with query (users are more likely to search those)
        $stmt->execute([
            'query' => '%' . $query . '%',
            'queryPrefix' => $query . '%'
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getBestSellers($limit = 9)
    {
        // Ensure $limit is an integer
        $limit = (int) $limit;

        // Prepare a query that joins products with order_items,
        // sums the quantity sold, groups by product id, and orders by total sold.
        $stmt = $this->pdo->prepare("
            SELECT p.*, SUM(oi.quantity) AS total_sold
            FROM products p
            INNER JOIN order_items oi ON p.id = oi.product_id
            WHERE p.unlisted = 0
            GROUP BY p.id
            ORDER BY total_sold DESC
            LIMIT :limit
        ");

        // Bind the limit parameter as an integer.
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getProductsByCreatedDate($limit = 9)
    {
        // Ensure $limit is an integer.
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM products
            WHERE unlisted = 0
            ORDER BY created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllCategories()
    {
        return $this->findAll('categories');
    }
    
    /**
     * Check if a product name already exists.
     *
     * @param string $name The product name to check.
     * @return bool        True if the name exists, false otherwise.
     */
    public function productNameExists($name)
    {
        // Optionally, use a case-insensitive check by converting to lowercase:
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS count FROM products WHERE LOWER(name) = LOWER(:name)");
        $stmt->execute(['name' => $name]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
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
    public function createProduct($name, $brand, $price, $description, $thumbnail, $category_id)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO products (name, brand, base_price, description, image_url, category_id)
            VALUES (:name, :brand, :price, :description, :thumbnail, :category_id)
        ");

        $params = [
            'name' => $name,
            'brand' => $brand,
            'price' => $price,
            'description' => $description,
            'thumbnail' => $thumbnail,
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
    public function addProductSize($productId, $size, $stock)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO product_sizes (product_id, size, stock)
            VALUES (:product_id, :size, :stock)
        ");

        $params = [
            'product_id' => $productId,
            'size' => $size,
            'stock' => $stock,
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
    public function updateProductSize($productId, $size, $stock)
    {
        $stmt = $this->pdo->prepare("UPDATE product_sizes SET stock = :stock WHERE product_id = :product_id AND size = :size");
        return $stmt->execute([
            'stock' => $stock,
            'product_id' => $productId,
            'size' => $size
        ]);
    }

    /**
     * Delete a specific product size.
     *
     * @param int    $productId The product ID.
     * @param string $size      The size to delete.
     * @return bool             True on success, false on failure.
     */
    public function deleteProductSize($productId, $size)
    {
        $stmt = $this->pdo->prepare("DELETE FROM product_sizes WHERE product_id = :product_id AND size = :size");
        return $stmt->execute([
            'product_id' => $productId,
            'size' => $size
        ]);
    }

    public function updateProduct($id, $name, $brand, $price, $description, $thumbnail = null)
    {
        $sql = "UPDATE products SET name = :name, brand = :brand, base_price = :price, description = :description";
        if ($thumbnail !== null) {
            $sql .= ", image_url = :thumbnail";
        } //builds sql statement dyanimically based on whether there was a thumbnail
        $sql .= " WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $params = [
            'name' => $name,
            'brand' => $brand,
            'price' => $price,
            'description' => $description,
            'id' => $id
        ];
        if ($thumbnail !== null) {
            $params['thumbnail'] = $thumbnail;
        }

        return $stmt->execute($params);
    }

    public function handleListing($id)
    {
        $sql = "UPDATE products SET unlisted = NOT unlisted WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $params = [
            'id' => $id
        ];

        return $stmt->execute($params);
    }
    /**
     * Get available stock for a specific product and size.
     *
     * @param int $productId The product ID.
     * @param mixed $size The product size (as stored in your database).
     * @return int The available stock, or 0 if not found.
     */
    public function getStockForItem($productId, $size)
    {
        $stmt = $this->pdo->prepare("SELECT stock FROM product_sizes WHERE product_id = :product_id AND size = :size");
        $stmt->execute([
            'product_id' => $productId,
            'size' => $size
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int) $result['stock'] : 0;
    }

    /**
     * Reduce the available stock for a product size by a given quantity.
     *
     * @param int    $productId The product ID.
     * @param mixed  $size      The size.
     * @param int    $quantity  The quantity to subtract.
     * @return bool             True if successful, false otherwise.
     */
    public function reduceStock($productId, $size, $quantity)
    {
        $stmt = $this->pdo->prepare("
        UPDATE product_sizes 
        SET stock = stock - :quantity 
        WHERE product_id = :product_id 
          AND size = :size 
          AND stock >= :quantity
    ");
        return $stmt->execute([
            'quantity' => $quantity,
            'product_id' => $productId,
            'size' => $size
        ]);
    }

}
?>