<?php
class Sale {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function createSale($customerId, $productId, $quantity, $totalAmount, $paymentMethod, $amountPaid, $outstandingAmount) {
        $stmt = $this->conn->prepare(
            "INSERT INTO sales (customer_id, product_id, quantity, total_amount, payment_method, amount_paid, outstanding_amount)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$customerId, $productId, $quantity, $totalAmount, $paymentMethod, $amountPaid, $outstandingAmount]);
    }

    public function all() {
        $stmt = $this->conn->query(
            "SELECT s.*, c.fullname AS customer_name, p.name AS product_name
             FROM sales s
             JOIN customers c ON s.customer_id = c.id
             JOIN products p ON s.product_id = p.id
             ORDER BY s.sale_date DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaymentsToday() {
        $stmt = $this->conn->query(
            "SELECT SUM(amount_paid) FROM sales WHERE DATE(sale_date) = CURRENT_DATE"
        );
        return $stmt->fetchColumn() ?: 0.00;
    }
}
