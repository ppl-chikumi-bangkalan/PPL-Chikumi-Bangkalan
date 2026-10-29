<?php
// database/seed.php
require_once __DIR__ . '/../config/database.php';

try {
    // 1. Seed Users (Owner & Kasir)
    $ownerPass = password_hash('owner123', PASSWORD_DEFAULT);
    $cashierPass = password_hash('kasir123', PASSWORD_DEFAULT);
    $custPass = password_hash('customer123', PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone_number, password, role) VALUES 
        ('Owner Chikumi', 'owner@chikumi.com', '081234567890', ?, 'owner'),
        ('Kasir Chikumi', 'kasir@chikumi.com', '081234567891', ?, 'cashier'),
        ('Pelanggan Contoh', 'customer@gmail.com', '089876543210', ?, 'customer')
        ON DUPLICATE KEY UPDATE name=VALUES(name);");
    $stmt->execute([$ownerPass, $cashierPass, $custPass]);

    // 2. Seed Kategori
    $pdo->exec("INSERT INTO categories (name) VALUES ('Chikumi Main Menu');");
    $catId = $pdo->lastInsertId();

    // 3. Seed Produk
    $stmtProd = $pdo->prepare("INSERT INTO products (category_id, name, description, image_url, is_active) VALUES (?, 'Chikumi Original Box', 'Ayam crispy lezat khas Chikumi', 'chikumi.jpg', 1);");
    $stmtProd->execute([$catId]);
    $productId = $pdo->lastInsertId();

    // 4. Seed Product Sizes (Regular & Large, Large diset di bawah stok minimum untuk tes penanda merah)
    $stmtSize = $pdo->prepare("INSERT INTO product_sizes (product_id, size_name, base_price, stock, min_stock) VALUES 
        (?, 'Regular', 15000.00, 20, 5),
        (?, 'Large', 22000.00, 3, 5);");
    $stmtSize->execute([$productId, $productId]);

    // 5. Seed Variants
    $stmtVar = $pdo->prepare("INSERT INTO variants (product_id, type, name, additional_price, is_available) VALUES 
        (?, 'filling', 'Mozzarella Cheese', 3000.00, 1),
        (?, 'dipping_sauce', 'Barbeque Sauce', 2000.00, 1),
        (?, 'dipping_sauce', 'Cheese Sauce', 2500.00, 1),
        (?, 'seasoning', 'Balado Extra Pedas', 0.00, 1);");
    $stmtVar->execute([$productId, $productId, $productId, $productId]);

    echo "Seeding berhasil: Akun Owner, Kasir, dan sampel produk berhasil dimasukkan!";

} catch (PDOException $e) {
    echo "Seeding Gagal: " . $e->getMessage();
}