<?php
include 'db.php';
$error = '';

// Satış ekleme
if(isset($_POST['add_sale'])){
    try {
        $customer_id = $_POST['customer_id'] ?? null;
        $title = $_POST['title'] ?? '';
        $amount = $_POST['amount'] ?? 0;
        $status = $_POST['status'] ?? 'aktif';

        if(!$customer_id || !$title) throw new Exception("Müşteri ve Başlık alanları boş olamaz!");

        $stmt = $pdo->prepare("INSERT INTO sales (customer_id,title,amount,status) VALUES (?,?,?,?)");
        $stmt->execute([$customer_id,$title,$amount,$status]);
        header("Location: sales.php");
        exit;
    } catch(Exception $e){
        $error = $e->getMessage();
    }
}

// Satış silme
if(isset($_GET['delete'])){
    $stmt = $pdo->prepare("DELETE FROM sales WHERE id=?");
    $stmt->execute([$_GET['delete']]);
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
<title>Satışlar - CRM</title>
<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="dist/css/adminlte.min.css">
<script src="plugins/jquery/jquery-3.6.0.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/datatables-bs4/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<style>
    .navbar-nav .nav-link.active { background-color: #0056b3; color: #fff; border-radius: 0.25rem; }
    .card { border-radius: 0.5rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .error-msg, .alert { margin-bottom: 15px; }
    .btn { margin-right: 5px; padding: 0.4rem 0.8rem; font-size: 0.875rem; }
    .badge { color: #000 !important; } /* Durum yazısını siyah yaptık */
</style>
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark bg-primary">
    <div class="container">
        <ul class="navbar-nav">
            <li class="nav-item"><a href="dashboard.php" class="nav-link">Dashboard</a></li>
            <li class="nav-item"><a href="customers.php" class="nav-link">Müşteriler</a></li>
            <li class="nav-item"><a href="sales.php" class="nav-link active">Satışlar</a></li>
            <li class="nav-item"><a href="communications.php" class="nav-link">İletişim</a></li>
        </ul>
    </div>
</nav>

<section class="content p-3">
<div class="container-fluid">

<h1 class="mb-4">Satış Fırsatları</h1>

<?php if($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<!-- Satış Ekleme Formu -->
<div class="card mb-4">
    <div class="card-header bg-info"><h3 class="card-title">Yeni Satış Ekle</h3></div>
    <div class="card-body">
        <form method="post">
            <div class="form-group">
                <label>Müşteri</label>
                <select name="customer_id" class="form-control" required>
                    <option value="">Seç</option>
                    <?php foreach($customers as $c) echo "<option value='{$c['id']}'>{$c['name']}</option>"; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Başlık</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Tutar</label>
                <input type="number" step="0.01" name="amount" class="form-control">
            </div>
            <div class="form-group">
                <label>Durum</label>
                <select name="status" class="form-control">
                    <option value="aktif">Aktif</option>
                    <option value="kapandı">Kapandı</option>
                    <option value="kaybedildi">Kaybedildi</option>
                </select>
            </div>
            <button type="submit" name="add_sale" class="btn btn-success">Ekle</button>
        </form>
    </div>
</div>

<!-- Satış Tablosu -->
<div class="card">
    <div class="card-header bg-primary"><h3 class="card-title">Mevcut Satışlar</h3></div>
    <div class="card-body">
        <table id="salesTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th><th>Müşteri</th><th>Başlık</th><th>Tutar</th><th>Durum</th><th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($sales as $s): ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><?= htmlspecialchars($s['customer_name']) ?></td>
                    <td><?= htmlspecialchars($s['title']) ?></td>
                    <td><?= $s['amount'] ?></td>
                    <td>
                        <?php 
                            $badge = 'secondary';
                            if($s['status']=='aktif') $badge='success';
                            elseif($s['status']=='kapandı') $badge='primary';
                            elseif($s['status']=='kaybedildi') $badge='danger';
                        ?>
                        <span class="badge badge-<?= $badge ?>"><?= $s['status'] ?></span>
                    </td>
                    <td>
                        <a href="edit_sales.php?id=<?= $s['id'] ?>" class="btn btn-warning px-3 py-1"><i class="fas fa-edit"></i> Düzenle</a>
                        <a href="?delete=<?= $s['id'] ?>" class="btn btn-danger px-3 py-1" onclick="return confirm('Bu satışı silmek istediğinize emin misiniz?')"><i class="fas fa-trash"></i> Sil</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</div>
</section>

<script>
$(function(){
    $('#salesTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false
    });
});
</script>

</div>
</body>
</html>
