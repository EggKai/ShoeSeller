<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../../core/Controller.php';

class HomeController extends Controller {
    private static $path = 'home';
    public function index() {
        $productModel = new Product();
        $products = $productModel->getAllProducts();
        $this->view(HomeController::$path.'/index', ['products' => $products, 'options' => ['carousel', 'landing']]);
    }
}
?>