<?php
if (!isset($_SESSION['user'])) {
    header('Location: index.php?action=login');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Inventory - UtangListo</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-logo">
                <img src="assets/images/logo.png" alt="UtangListo Logo" />
            </div>
            <div class="brand-text">UtangListo</div>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php?action=home">Dashboard</a>
            <a href="index.php?action=customers">Customers</a>
            <a class="active" href="index.php?action=inventory">Products</a>
            <a href="index.php?action=sales">Transaction</a>
            <a href="index.php?action=logout">Log Out</a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div>
                <h1 class="page-title">Inventory</h1>
                <p class="text-muted">Manage product stock and restock when needed.</p>
            </div>
            <div class="topbar-actions">
                <a href="index.php?action=home">Dashboard</a>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="row gy-4">
            <div class="col-lg-6">
                <div class="card card-custom form-card">
                    <div class="card-body">
                        <h5 class="card-title">Restock Product</h5>
                        <form method="POST" action="index.php?action=inventory">
                            <div class="mb-3">
                                <label class="form-label">Product</label>
                                <select name="product_id" class="form-select" required>
                                    <option value="">Choose product</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="restock_quantity" class="form-control" min="1" value="1" required>
                            </div>
                            <button class="btn btn-primary w-100" type="submit">Restock</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="card-title">Current Inventory</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                                            <td>₱<?php echo number_format($product['price'], 2); ?></td>
                                            <td><?php echo $product['stock']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
