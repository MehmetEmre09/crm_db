<?php

include 'db.php';

// İletişim ekleme
if(isset($_POST['add_communication'])){
    $customer_id = $_POST['customer_id'];
    $user_id = $_POST['user_id'];
    $note = $_POST['note'];
    $contact_date = $_POST['contact_date'];

    $stmt = $pdo->prepare("INSERT INTO communications (customer_id,user_id,note,contact_date) VALUES (?,?,?,?)");
    $stmt->execute([$customer_id,$user_id,$note,$contact_date]);
    header("Location: communications.php");
    exit;
}

// Müşteriler ve kullanıcılar dropdown
$customers = $pdo->query("SELECT id,name FROM customers")->fetchAll();
$users = $pdo->query("SELECT id,username FROM users")->fetchAll();

// Kayıtları çek
$records = $pdo->query("
    SELECT com.*, c.name as customer_name, u.username 
    FROM communications com 
    LEFT JOIN customers c ON com.customer_id=c.id
    LEFT JOIN users u ON com.user_id=u.id
    ORDER BY com.contact_date DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>İletişim - Basit CRM</title>
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
        form input, form textarea, form select, form button { display: block; margin: 5px 0; padding: 8px; width: 300px; max-width: 100%; border: 1px solid #ccc; border-radius: 4px; }
        form button { width: auto; background-color: #3498db; color: white; border: none; cursor: pointer; border-radius: 4px; }
        form button:hover { background-color: #2980b9; }
        textarea { height: 60px; }
    </style>
</head>
<body>

<nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="customers.php">Müşteriler</a>
    <a href="sales.php">Satışlar</a>
    <a href="communications.php">İletişim</a>
</nav>

<h1>İletişim Kayıtları</h1>

<form method="post">
    <select name="customer_id" required>
        <option value="">Müşteri seç</option>
        <?php foreach($customers as $c) echo "<option value='{$c['id']}'>{$c['name']}</option>"; ?>
    </select>
    <select name="user_id" required>
        <option value="">Kullanıcı seç</option>
        <?php foreach($users as $u) echo "<option value='{$u['id']}'>{$u['username']}</option>"; ?>
    </select>
    <textarea name="note" placeholder="Görüşme notu" required></textarea>
    <input type="datetime-local" name="contact_date" required>
    <button type="submit" name="add_communication">Ekle</button>
</form>

<table>
    <tr>
        <th>ID</th><th>Müşteri</th><th>Kullanıcı</th><th>Not</th><th>Tarih</th>
    </tr>
    <?php foreach($records as $r): ?>
    <tr>
        <td><?= $r['id'] ?></td>
        <td><?= $r['customer_name'] ?></td>
        <td><?= $r['username'] ?></td>
        <td><?= $r['note'] ?></td>
        <td><?= $r['contact_date'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
