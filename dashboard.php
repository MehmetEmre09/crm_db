<?php
// Giriş kontrolü
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Dashboard verileri
$total_customers = $pdo->query("SELECT COUNT(*) as total_customers FROM customers")->fetch()['total_customers'];
$active_sales = $pdo->query("SELECT COUNT(*) as active_sales FROM sales WHERE status='aktif'")->fetch()['active_sales'];
$recent_communications = $pdo->query("
    SELECT com.id, c.name as customer_name, com.note, com.contact_date
    FROM communications com
    LEFT JOIN customers c ON com.customer_id=c.id
    ORDER BY com.contact_date DESC
    LIMIT 5
")->fetchAll();

// Grafik verileri
$sales_stats = $pdo->query("SELECT status, COUNT(*) as count FROM sales GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);

// İletişim tarih dağılımı
$comm_dates = $pdo->query("SELECT DATE(contact_date) as date, COUNT(*) as count FROM communications GROUP BY DATE(contact_date) ORDER BY date ASC")->fetchAll();
$comm_labels = array_column($comm_dates, 'date');
$comm_counts = array_column($comm_dates, 'count');
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Dashboard - CRM</title>
<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="dist/css/adminlte.min.css">
<script src="plugins/jquery/jquery-3.6.0.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    body { background-color: #f4f6f9; }
    .small-box { border-radius: 0.75rem; box-shadow: 0 4px 15px rgba(0,0,0,0.15); transition: transform 0.2s; }
    .small-box:hover { transform: translateY(-5px); }
    .card { border-radius: 0.75rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .table thead th { background-color: #007bff; color: #fff; }
    .navbar-nav .nav-item .nav-link { margin-right: 1rem; }
</style>
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark bg-primary">
<div class="container">
    <ul class="navbar-nav mr-auto">
        <li class="nav-item"><a href="dashboard.php" class="nav-link active"><i class="fas fa-tachometer-alt" style="color:white;"></i> Dashboard</a></li>
        <li class="nav-item"><a href="customers.php" class="nav-link"><i class="fas fa-users" style="color:white;"></i> Müşteriler</a></li>
        <li class="nav-item"><a href="sales.php" class="nav-link"><i class="fas fa-chart-line" style="color:white;"></i> Satışlar</a></li>
        <li class="nav-item"><a href="communications.php" class="nav-link"><i class="fas fa-envelope" style="color:white;"></i> İletişim</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#00000">
                <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['username']) ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="logout.php" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Admin</a>
            </div>
        </li>
    </ul>
</div>
</nav>

<!-- Content Wrapper -->
<div class="content-wrapper p-3">
    <div class="container-fluid">
        <h1 class="mb-4">CRM Dashboard</h1>

        <!-- Stat Kartları -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= $total_customers ?></h3>
                        <p>Toplam Müşteri</p>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <a href="customers.php" class="small-box-footer">Detaylar <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= $active_sales ?></h3>
                        <p>Aktif Satış</p>
                    </div>
                    <div class="icon"><i class="fas fa-chart-line"></i></div>
                    <a href="sales.php" class="small-box-footer">Detaylar <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= count($recent_communications) ?></h3>
                        <p>Son İletişim</p>
                    </div>
                    <div class="icon"><i class="fas fa-envelope"></i></div>
                    <a href="communications.php" class="small-box-footer">Detaylar <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>CRM</h3>
                        <p>Kontrol Paneli</p>
                    </div>
                    <div class="icon"><i class="fas fa-cogs"></i></div>
                    <a href="dashboard.php" class="small-box-footer">Yenile <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Grafikler -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white"><h3 class="card-title"><i class="fas fa-chart-pie"></i> Satış Durumu</h3></div>
                    <div class="card-body">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white"><h3 class="card-title"><i class="fas fa-chart-line"></i> İletişim Aktivitesi</h3></div>
                    <div class="card-body">
                        <canvas id="commChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Son 5 İletişim Tablosu -->
        <div class="card mt-4">
            <div class="card-header bg-primary text-white"><h3 class="card-title">Son 5 İletişim</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Müşteri</th>
                            <th>Not</th>
                            <th>Tarih</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_communications as $r): ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td><?= $r['customer_name'] ?></td>
                            <td><?= htmlspecialchars($r['note']) ?></td>
                            <td><?= $r['contact_date'] ?></td>
                            <td><a href="communications.php" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i> Detay</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
// Satış Durumu Grafiği
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(salesCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_keys($sales_stats)) ?>,
        datasets: [{
            data: <?= json_encode(array_values($sales_stats)) ?>,
            backgroundColor: ['#28a745','#007bff','#dc3545','#ffc107'],
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

// İletişim Aktivitesi Grafiği
const commCtx = document.getElementById('commChart').getContext('2d');
const commChart = new Chart(commCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($comm_labels) ?>,
        datasets: [{
            label: 'Günlük İletişim',
            data: <?= json_encode($comm_counts) ?>,
            fill: false,
            borderColor: '#17a2b8',
            tension: 0.3
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});
</script>

</div>
</body>
</html>
