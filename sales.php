<?php

include 'db.php';

// Satış ekleme
if(isset($_POST['add_sale'])){
    $customer_id = $_POST['customer_id'];
    $title = $_POST['title'];
    $amount = $_POST['amount'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("INSERT INTO sales (customer_id,title,amount,status) VALUES (?,?,?,?)");
    $stmt->execute([$customer_id,$title,$amount,$status]);
    header("Location: sales.php");
    exit;
}

// Müşteriler dropdown
$customers = $pdo->query("SELECT id,name FROM customers")->fetchAll();

// Satışları çek
$sales = $pdo->query("SELECT s.*, c.name as customer_name FROM sales s JOIN customers c ON s.customer_id=c.id ORDER BY s.id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Satışlar - Basit CRM</title>
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
        form input, form select, form button { display: block; margin: 5px 0; padding: 8px; width: 300px; max-width: 100%; border: 1px solid #ccc; border-radius: 4px; }
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

<h1>Satış Fırsatları</h1>

<form method="post">
    <select name="customer_id" required>
        <option value="">Müşteri seç</option>
        <?php foreach($customers as $c) echo "<option value='{$c['id']}'>{$c['name']}</option>"; ?>
    </select>
    <input type="text" name="title" placeholder="Teklif Başlığı" required>
    <input type="number" step="0.01" name="amount" placeholder="Tutar">
    <select name="status">
        <option value="aktif">Aktif</option>
        <option value="kapandı">Kapandı</option>
        <option value="kaybedildi">Kaybedildi</option>
    </select>
    <button type="submit" name="add_sale">Ekle</button>
</form>

<table>
    <tr>
        <th>ID</th><th>Müşteri</th><th>Başlık</th><th>Tutar</th><th>Durum</th>
    </tr>
    <?php foreach($sales as $s): ?>
    <tr>
        <td><?= $s['id'] ?></td>
        <td><?= $s['customer_name'] ?></td>
        <td><?= $s['title'] ?></td>
        <td><?= $s['amount'] ?></td>
        <td><?= $s['status'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
