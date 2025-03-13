<?php
class Controller {
    protected function view($view, $data = []) {
        extract($data);
        require_once __DIR__ . '/../app/views/' . $view . '.php';
    }
}
?>