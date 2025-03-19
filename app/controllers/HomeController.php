<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../../core/Controller.php';

class HomeController extends Controller {
    private static $path = 'home';
    public function index() {
        $productModel = new Product();
        $recents = $productModel->getProductsByCreatedDate();
        $bestSellers = $productModel->getBestSellers();
        $this->view(HomeController::$path.'/index', ['recents' => $recents, 'bestsellers' => $bestSellers, 'options' => ['carousel', 'landing']]);
    }
}
?>