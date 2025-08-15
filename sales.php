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

<?php include'header.php'; ?>
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
                    <option value="aktif" style="color:black;" >Aktif</option>
                    <option value="kapandı" style="color:black;" >Kapandı</option>
                    <option value="kaybedildi" style="color:black;">Kaybedildi</option>
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
                        <span class="badge badge-<?= $badge ?>" style="color:black;"><?= $s['status'] ?></span>
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
