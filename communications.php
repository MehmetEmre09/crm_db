<?php
include 'db.php';
$error = '';

// Form gönderildiğinde ekleme
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    try {
        $customer_id = $_POST['customer_id'] ?? null;
        $user_id = $_POST['user_id'] ?? null;
        $note = $_POST['note'] ?? '';
        $contact_date = $_POST['contact_date'] ?? null;

        if(!$customer_id || !$user_id || !$contact_date){
            throw new Exception("Gerekli alanlar boş bırakılamaz!");
        }

        $stmt = $pdo->prepare("INSERT INTO communications (customer_id, user_id, note, contact_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$customer_id, $user_id, $note, $contact_date]);
        header("Location: communications.php");
        exit;
    } catch(Exception $e){
        $error = $e->getMessage();
    }
}

// Silme işlemi
if(isset($_GET['delete'])){
    $stmt = $pdo->prepare("DELETE FROM communications WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    header("Location: communications.php");
    exit;
}

// Dropdown verileri
$customers = $pdo->query("SELECT id,name FROM customers")->fetchAll();
$users = $pdo->query("SELECT id,username FROM users")->fetchAll();

// Kayıtlar
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
<title>İletişim - CRM</title>
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
.error-msg { color:red; margin-bottom:10px; }
.btn-lg { font-size:0.9rem; padding:0.5rem 1rem; }
.table thead th { background-color: #007bff; color: #fff; }
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
            <li class="nav-item"><a href="sales.php" class="nav-link">Satışlar</a></li>
            <li class="nav-item"><a href="communications.php" class="nav-link active">İletişim</a></li>
        </ul>
    </div>
</nav>

<section class="content p-3">
<div class="container-fluid">
<h1 class="mb-4">İletişim Kayıtları</h1>

<?php if($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<!-- Ekleme Formu -->
<div class="card mb-4">
    <div class="card-header bg-info text-white"><h3 class="card-title">Yeni İletişim Ekle</h3></div>
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
                <label>Kullanıcı</label>
                <select name="user_id" class="form-control" required>
                    <option value="">Seç</option>
                    <?php foreach($users as $u) echo "<option value='{$u['id']}'>{$u['username']}</option>"; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Not</label>
                <textarea name="note" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label>Tarih</label>
                <input type="datetime-local" name="contact_date" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-plus"></i> Ekle
            </button>
        </form>
    </div>
</div>

<!-- Liste -->
<div class="card">
    <div class="card-header bg-primary text-white"><h3 class="card-title">Mevcut Kayıtlar</h3></div>
    <div class="card-body table-responsive">
        <table id="recordsTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th><th>Müşteri</th><th>Kullanıcı</th><th>Not</th><th>Tarih</th><th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($records as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= htmlspecialchars($r['customer_name']) ?></td>
                    <td><?= htmlspecialchars($r['username']) ?></td>
                    <td><?= htmlspecialchars($r['note']) ?></td>
                    <td><?= $r['contact_date'] ?></td>
                    <td>
                        <a href="edit_communications.php?id=<?= $r['id'] ?>" class="btn btn-lg btn-warning mb-1">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <a href="?delete=<?= $r['id'] ?>" class="btn btn-lg btn-danger mb-1" 
                           onclick="return confirm('Bu kaydı silmek istediğinize emin misiniz?')">
                           <i class="fas fa-trash"></i> Sil
                        </a>
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
    $('#recordsTable').DataTable({
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
