<?php

include 'db.php';

// Müşteri ekleme
if(isset($_POST['add_customer'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $stmt = $pdo->prepare("INSERT INTO customers (name,email,phone,address) VALUES (?,?,?,?)");
    $stmt->execute([$name,$email,$phone,$address]);
    header("Location: customers.php");
    exit;
}

// Müşterileri çek
$customers = $pdo->query("SELECT * FROM customers ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Müşteriler - Basit CRM</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        nav { margin-bottom: 20px; }
        nav a { margin-right: 15px; text-decoration: none; font-weight: bold; color: #2c3e50; }
        nav a:hover { text-decoration: underline; }
        h1, h3 { color: #34495e; }
        table { border-collapse: collapse; width: 100%; background-color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #3498db; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #e1f5fe; }
        form input, form textarea, form button { display: block; margin: 5px 0; padding: 8px; width: 300px; max-width: 100%; border: 1px solid #ccc; border-radius: 4px; }
        form button { width: auto; background-color: #3498db; color: white; border: none; cursor: pointer; border-radius: 4px; }
        form button:hover { background-color: #2980b9; }
    </style>
</head>
<body>

<nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="customers.php">Müşteriler</a>
    <a href="sales.php">Satışlar</a>
    <a href="communications.php">İletişim</a>
</nav>

<h1>Müşteriler</h1>

<form method="post">
    <input type="text" name="name" placeholder="Ad Soyad" required>
    <input type="email" name="email" placeholder="E-posta">
    <input type="text" name="phone" placeholder="Telefon">
    <input type="text" name="address" placeholder="Adres">
    <button type="submit" name="add_customer">Ekle</button>
</form>

<table>
    <tr>
        <th>ID</th><th>Ad Soyad</th><th>Email</th><th>Telefon</th><th>Adres</th>
    </tr>
    <?php foreach($customers as $c): ?>
    <tr>
        <td><?= $c['id'] ?></td>
        <td><?= $c['name'] ?></td>
        <td><?= $c['email'] ?></td>
        <td><?= $c['phone'] ?></td>
        <td><?= $c['address'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
