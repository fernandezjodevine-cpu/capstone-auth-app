<?php
require_once 'models/Product.php';
require_once 'models/Customer.php';
require_once 'models/Sale.php';

class StoreController {
    private $products;
    private $customers;
    private $sales;

    public function __construct() {
        global $conn;
        $this->products = new Product($conn);
        $this->customers = new Customer($conn);
        $this->sales = new Sale($conn);
    }

    public function getDashboardData() {
        return [
            'totalOutstanding' => $this->customers->getTotalOutstanding(),
            'totalCustomers' => count($this->customers->all()),
            'paymentsToday' => $this->sales->getPaymentsToday(),
        ];
    }

    public function getProducts() {
        return $this->products->all();
    }

    public function getCustomers() {
        return $this->customers->all();
    }

    public function getSales() {
        return $this->sales->all();
    }

    public function processPurchase($data) {
        $customerName = trim($data['customer_name'] ?? '');
        $productId = intval($data['product_id'] ?? 0);
        $quantity = intval($data['quantity'] ?? 0);
        $paymentMethod = $data['payment_method'] ?? 'cash';

        if ($customerName === '') {
            return 'Customer name is required.';
        }

        if ($productId <= 0 || $quantity <= 0) {
            return 'Please select a product and quantity.';
        }

        $product = $this->products->find($productId);
        if (!$product) {
            return 'Selected product was not found.';
        }

        if ($product['stock'] < $quantity) {
            return 'Product is not currently available in the requested quantity.';
        }

        $customer = $this->customers->findOrCreate($customerName);
        if (!$customer) {
            return 'Unable to create or find the customer record.';
        }

        $totalAmount = $product['price'] * $quantity;

        if ($paymentMethod === 'credit') {
            if ($customer['outstanding_balance'] + $totalAmount > $customer['credit_limit']) {
                return 'This customer is not capable of additional credit at this time.';
            }
            $amountPaid = 0.00;
            $outstanding = $totalAmount;
            $this->customers->addOutstanding($customer['id'], $totalAmount);
        } else {
            $amountPaid = $totalAmount;
            $outstanding = 0.00;
        }

        $this->products->decrementStock($productId, $quantity);
        $this->sales->createSale(
            $customer['id'],
            $productId,
            $quantity,
            $totalAmount,
            $paymentMethod,
            $amountPaid,
            $outstanding
        );

        return true;
    }

    public function restockProduct($data) {
        $productId = intval($data['product_id'] ?? 0);
        $quantity = intval($data['restock_quantity'] ?? 0);

        if ($productId <= 0 || $quantity <= 0) {
            return 'Select a product and enter a restock quantity.';
        }

        $product = $this->products->find($productId);
        if (!$product) {
            return 'Product not found.';
        }

        $this->products->restock($productId, $quantity);
        return true;
    }

    public function payOutstanding($data) {
        $customerId = intval($data['customer_id'] ?? 0);
        $amount = floatval($data['payment_amount'] ?? 0);

        if ($customerId <= 0 || $amount <= 0) {
            return 'Select a customer and enter a valid payment amount.';
        }

        $customer = $this->customers->findById($customerId);
        if (!$customer) {
            return 'Customer not found.';
        }

        if ($amount > $customer['outstanding_balance']) {
            return 'Payment exceeds the outstanding balance.';
        }

        $this->customers->payOutstanding($customerId, $amount);
        return true;
    }
}
