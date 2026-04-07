<?php
class Customer {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function all() {
        $stmt = $this->conn->query("SELECT * FROM customers ORDER BY fullname");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByName($fullname) {
        $stmt = $this->conn->prepare("SELECT * FROM customers WHERE fullname = ?");
        $stmt->execute([$fullname]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findOrCreate($fullname, $phone = null) {
        $customer = $this->findByName($fullname);
        if ($customer) {
            return $customer;
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO customers (fullname, phone, credit_limit, outstanding_balance) VALUES (?, ?, 1000.00, 0.00)"
        );
        $stmt->execute([$fullname, $phone]);

        return $this->findById($this->conn->lastInsertId());
    }

    public function addOutstanding($customerId, $amount) {
        $stmt = $this->conn->prepare(
            "UPDATE customers SET outstanding_balance = outstanding_balance + ? WHERE id = ?"
        );
        $stmt->execute([$amount, $customerId]);
    }

    public function payOutstanding($customerId, $amount) {
        $stmt = $this->conn->prepare(
            "UPDATE customers SET outstanding_balance = GREATEST(outstanding_balance - ?, 0) WHERE id = ?"
        );
        $stmt->execute([$amount, $customerId]);
    }

    public function getTotalOutstanding() {
        $stmt = $this->conn->query("SELECT SUM(outstanding_balance) FROM customers");
        return $stmt->fetchColumn() ?: 0.00;
    }
}
