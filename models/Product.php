<?php
class Product {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->seedDefaultProducts();
    }

    public function all() {
        $stmt = $this->conn->query("SELECT * FROM products ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function restock($id, $quantity) {
        $stmt = $this->conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        $stmt->execute([$quantity, $id]);
        return true;
    }

    public function decrementStock($id, $quantity) {
        $stmt = $this->conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
        $stmt->execute([$quantity, $id, $quantity]);
        return $stmt->rowCount() > 0;
    }

    private function seedDefaultProducts() {
        $count = $this->conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
        if ($count > 0) {
            return;
        }

        $items = [
            ['Tuna Flakes', 45.00, 20],
            ['Instant Noodles', 15.00, 30],
            ['Cooking Oil 500ml', 120.00, 15],
            ['Cigarettes Pack', 75.00, 12],
            ['Toothpaste', 55.00, 18],
        ];

        $stmt = $this->conn->prepare("INSERT INTO products (name, price, stock) VALUES (?, ?, ?)");
        foreach ($items as $item) {
            $stmt->execute($item);
        }
    }
}
