<?php

include 'db.php';

// Dashboard verileri
$total_customers = $pdo->query("SELECT COUNT(*) as total_customers FROM customers")->fetch()['total_customers'];
$active_sales = $pdo->query("SELECT COUNT(*) as active_sales FROM sales WHERE status='aktif'")->fetch()['active_sales'];
$recent_communications = $pdo->query("
    SELECT com.id, c.name as customer_name, u.username, com.note, com.contact_date
    FROM communications com
    LEFT JOIN customers c ON com.customer_id=c.id
    LEFT JOIN users u ON com.user_id=u.id
    ORDER BY com.contact_date DESC
    LIMIT 5
")->fetchAll();

// Satış durumları
$sales_status = $pdo->query("
    SELECT status, COUNT(*) as total 
    FROM sales 
    GROUP BY status
")->fetchAll(PDO::FETCH_KEY_PAIR);

// Son 5 iletişim - müşteri bazlı
$comm_counts = $pdo->query("
    SELECT c.name, COUNT(*) as total
    FROM communications com
    LEFT JOIN customers c ON com.customer_id=c.id
    GROUP BY c.name
    ORDER BY total DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Basit CRM</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        nav { margin-bottom: 20px; }
        nav a { margin-right: 15px; text-decoration: none; font-weight: bold; color: #2c3e50; }
        nav a:hover { text-decoration: underline; }
        h1, h3 { color: #34495e; }
        table { border-collapse: collapse; width: 100%; background-color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-top:10px;}
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #3498db; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #e1f5fe; }
        .card {
            background-color: #fff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: inline-block;
            min-width: 150px;
            text-align: center;
        }
        canvas { background-color: #fff; padding: 10px; border-radius:6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom:20px;}
    </style>
</head>
<body>

<nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="customers.php">Müşteriler</a>
    <a href="sales.php">Satışlar</a>
    <a href="communications.php">İletişim</a>
</nav>

<h1>CRM Dashboard</h1>

<div style="display:flex; gap:20px; flex-wrap: wrap;">
    <div class="card">
        <h3>Toplam Müşteri</h3>
        <p><?= $total_customers ?></p>
    </div>
    <div class="card">
        <h3>Aktif Satış Fırsatları</h3>
        <p><?= $active_sales ?></p>
    </div>
</div>

<h3>Satış Durumu</h3>
<canvas id="salesChart" width="400" height="200"></canvas>

<h3>Son 5 İletişim Kayıdı</h3>
<table>
    <tr>
        <th>ID</th><th>Müşteri</th><th>Kullanıcı</th><th>Not</th><th>Tarih</th>
    </tr>
    <?php foreach($recent_communications as $r): ?>
    <tr>
        <td><?= $r['id'] ?></td>
        <td><?= $r['customer_name'] ?></td>
        <td><?= $r['username'] ?></td>
        <td><?= $r['note'] ?></td>
        <td><?= $r['contact_date'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<h3>Son 5 İletişim - Müşteri Bazlı</h3>
<canvas id="commChart" width="400" height="200"></canvas>

<script>
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(salesCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_keys($sales_status)) ?>,
        datasets: [{
            label: 'Satış Adedi',
            data: <?= json_encode(array_values($sales_status)) ?>,
            backgroundColor: ['#3498db','#2ecc71','#e74c3c'],
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});

const commCtx = document.getElementById('commChart').getContext('2d');
const commChart = new Chart(commCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_keys($comm_counts)) ?>,
        datasets: [{
            data: <?= json_encode(array_values($comm_counts)) ?>,
            backgroundColor: ['#3498db','#2ecc71','#e74c3c','#f1c40f','#9b59b6'],
        }]
    },
    options: { responsive: true }
});
</script>

</body>
</html>
