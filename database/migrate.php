<?php
// database/migrate.php
require_once __DIR__ . '/db.php';

try {
    // Nonaktifkan foreign key checks sementara saat drop tabel
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

    // Hapus tabel jika sudah ada (urutan dari anak ke induk)
    $tables = [
        'pos_item_variants', 'pos_transaction_items', 'pos_transactions',
        'order_item_variants', 'order_items', 'orders',
        'cart_item_variants', 'carts',
        'expenses', 'stock_histories',
        'product_sizes', 'variants', 'products', 'categories', 'users'
    ];
    
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS `$table`;");
    }

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // 1. Users & Roles
    $pdo->exec("CREATE TABLE users (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NULL,
        phone_number VARCHAR(20) UNIQUE NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('owner', 'cashier', 'customer') NOT NULL DEFAULT 'customer',
        remember_token VARCHAR(100) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");

    // 2. Categories
    $pdo->exec("CREATE TABLE categories (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");

    // 3. Products
    $pdo->exec("CREATE TABLE products (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        category_id BIGINT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT NULL,
        image_url VARCHAR(255) NULL,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_product_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB;");

    // 4. Variants (Filling, Dipping Sauce, Seasoning)
    $pdo->exec("CREATE TABLE variants (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        product_id BIGINT NOT NULL,
        type ENUM('size', 'filling', 'dipping_sauce', 'seasoning') NOT NULL,
        name VARCHAR(100) NOT NULL,
        additional_price DECIMAL(12, 2) DEFAULT 0.00,
        is_available BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_variant_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    // 5. Product Sizes & Stock (Rules stok minimum & harga per ukuran)
    $pdo->exec("CREATE TABLE product_sizes (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        product_id BIGINT NOT NULL,
        size_name ENUM('Regular', 'Large') NOT NULL,
        base_price DECIMAL(12, 2) NOT NULL,
        stock INT DEFAULT 0,
        min_stock INT DEFAULT 5,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_product_size_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    // 6. Stock Histories
    $pdo->exec("CREATE TABLE stock_histories (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        product_size_id BIGINT NOT NULL,
        user_id BIGINT NULL,
        type ENUM('in', 'out') NOT NULL,
        quantity INT NOT NULL,
        source ENUM('manual_restock', 'pos_transaction', 'online_order') NOT NULL,
        reference_id BIGINT NULL,
        notes VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_stock_history_product_size FOREIGN KEY (product_size_id) REFERENCES product_sizes(id) ON DELETE CASCADE,
        CONSTRAINT fk_stock_history_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB;");

    // 7. Expenses (Pengeluaran Usaha)
    $pdo->exec("CREATE TABLE expenses (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        category ENUM('raw_material', 'operational', 'salary', 'bonus', 'overtime', 'other') NOT NULL,
        title VARCHAR(255) NOT NULL,
        amount DECIMAL(12, 2) NOT NULL,
        expense_date DATE NOT NULL,
        notes TEXT NULL,
        created_by BIGINT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_expense_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB;");

    // 8. Carts & Cart Items
    $pdo->exec("CREATE TABLE carts (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        customer_id BIGINT NOT NULL,
        product_size_id BIGINT NOT NULL,
        quantity INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_cart_customer FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT fk_cart_product_size FOREIGN KEY (product_size_id) REFERENCES product_sizes(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    $pdo->exec("CREATE TABLE cart_item_variants (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        cart_id BIGINT NOT NULL,
        variant_id BIGINT NOT NULL,
        CONSTRAINT fk_cart_item_variant_cart FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
        CONSTRAINT fk_cart_item_variant_variant FOREIGN KEY (variant_id) REFERENCES variants(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    // 9. Orders & Order Items (Pesanan Online)
    $pdo->exec("CREATE TABLE orders (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        order_number VARCHAR(50) UNIQUE NOT NULL,
        customer_id BIGINT NOT NULL,
        delivery_method ENUM('courier', 'pickup') NOT NULL,
        shipping_address TEXT NULL,
        subtotal DECIMAL(12, 2) NOT NULL,
        shipping_cost DECIMAL(12, 2) DEFAULT 0.00,
        grand_total DECIMAL(12, 2) NOT NULL,
        status ENUM('unpaid', 'processing', 'ready', 'completed', 'cancelled') DEFAULT 'unpaid',
        snap_token VARCHAR(255) NULL,
        payment_type VARCHAR(50) NULL,
        paid_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_order_customer FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    $pdo->exec("CREATE TABLE order_items (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        order_id BIGINT NOT NULL,
        product_size_id BIGINT NOT NULL,
        unit_price DECIMAL(12, 2) NOT NULL,
        quantity INT NOT NULL,
        subtotal DECIMAL(12, 2) NOT NULL,
        CONSTRAINT fk_order_item_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        CONSTRAINT fk_order_item_product_size FOREIGN KEY (product_size_id) REFERENCES product_sizes(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    $pdo->exec("CREATE TABLE order_item_variants (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        order_item_id BIGINT NOT NULL,
        variant_id BIGINT NOT NULL,
        variant_price DECIMAL(12, 2) NOT NULL,
        CONSTRAINT fk_order_item_variant_item FOREIGN KEY (order_item_id) REFERENCES order_items(id) ON DELETE CASCADE,
        CONSTRAINT fk_order_item_variant_variant FOREIGN KEY (variant_id) REFERENCES variants(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    // 10. POS Transactions (Transaksi Kasir)
    $pdo->exec("CREATE TABLE pos_transactions (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        transaction_number VARCHAR(50) UNIQUE NOT NULL,
        cashier_id BIGINT NOT NULL,
        total_amount DECIMAL(12, 2) NOT NULL,
        payment_method ENUM('cash', 'non_cash') NOT NULL,
        amount_paid DECIMAL(12, 2) NOT NULL,
        change_amount DECIMAL(12, 2) NOT NULL,
        status ENUM('paid', 'cancelled') DEFAULT 'paid',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_pos_cashier FOREIGN KEY (cashier_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    $pdo->exec("CREATE TABLE pos_transaction_items (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        pos_transaction_id BIGINT NOT NULL,
        product_size_id BIGINT NOT NULL,
        unit_price DECIMAL(12, 2) NOT NULL,
        quantity INT NOT NULL,
        subtotal DECIMAL(12, 2) NOT NULL,
        CONSTRAINT fk_pos_item_transaction FOREIGN KEY (pos_transaction_id) REFERENCES pos_transactions(id) ON DELETE CASCADE,
        CONSTRAINT fk_pos_item_product_size FOREIGN KEY (product_size_id) REFERENCES product_sizes(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    $pdo->exec("CREATE TABLE pos_item_variants (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        pos_transaction_item_id BIGINT NOT NULL,
        variant_id BIGINT NOT NULL,
        variant_price DECIMAL(12, 2) NOT NULL,
        CONSTRAINT fk_pos_item_variant_item FOREIGN KEY (pos_transaction_item_id) REFERENCES pos_transaction_items(id) ON DELETE CASCADE,
        CONSTRAINT fk_pos_item_variant_variant FOREIGN KEY (variant_id) REFERENCES variants(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    echo "Migration berhasil: Semua tabel dan relasi MySQL berhasil dibuat!<br>";

} catch (PDOException $e) {
    echo "Migration Gagal: " . $e->getMessage();
}