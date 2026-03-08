<?php
require_once __DIR__ . '/../classes/Category.php';

class CategoryController {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new Category();
    }

    public function getAll() {
        $categories = $this->categoryModel->getAll();
        return ['categories' => $categories];
    }

    public function getById() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if (!$id) {
            return ['error' => 'Category not found'];
        }

        $category = $this->categoryModel->getById($id);
        $businesses = $this->categoryModel->getBusinessByCategory($id);

        return [
            'category' => $category,
            'businesses' => $businesses
        ];
    }
}
?>
