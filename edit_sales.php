<?php
include 'db.php';

// id kontrolü
if(!isset($_GET['id'])){
    header("Location: sales.php");
    exit;
}
$id = $_GET['id'];

// Satışı çek
$stmt = $pdo->prepare("SELECT * FROM sales WHERE id=?");
$stmt->execute([$id]);
$sale = $stmt->fetch();

if(!$sale){
    header("Location: sales.php");
    exit;
}

// Müşteriler dropdown
$customers = $pdo->query("SELECT id,name FROM customers")->fetchAll();

// Güncelleme
if(isset($_POST['update_sale'])){
    $stmt = $pdo->prepare("UPDATE sales SET customer_id=?, title=?, amount=?, status=? WHERE id=?");
    $stmt->execute([$_POST['customer_id'], $_POST['title'], $_POST['amount'], $_POST['status'], $id]);
    header("Location: sales.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Düzenle Satış</title>
    <link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
<section class="content p-3">
<div class="container-fluid">
<h1 class="mb-4">Satış Düzenle</h1>

<div class="card">
    <div class="card-body">
        <form method="post">
            <div class="form-group">
                <label>Müşteri</label>
                <select name="customer_id" class="form-control" required>
                    <?php foreach($customers as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $sale['customer_id']==$c['id']?'selected':'' ?>><?= $c['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Başlık</label>
                <input type="text" name="title" class="form-control" value="<?= $sale['title'] ?>" required>
            </div>
            <div class="form-group">
                <label>Tutar</label>
                <input type="number" step="0.01" name="amount" class="form-control" value="<?= $sale['amount'] ?>">
            </div>
            <div class="form-group">
                <label>Durum</label>
                <select name="status" class="form-control">
                    <option value="aktif" <?= $sale['status']=='aktif'?'selected':'' ?>>Aktif</option>
                    <option value="kapandı" <?= $sale['status']=='kapandı'?'selected':'' ?>>Kapandı</option>
                    <option value="kaybedildi" <?= $sale['status']=='kaybedildi'?'selected':'' ?>>Kaybedildi</option>
                </select>
            </div>
            <button type="submit" name="update_sale" class="btn btn-primary">Güncelle</button>
            <a href="sales.php" class="btn btn-secondary">İptal</a>
        </form>
    </div>
</div>

</div>
</section>
</div>
</body>
</html>
