<?php
include 'db.php';

// Gelen id kontrolü
if(!isset($_GET['id'])){
    header("Location: communications.php");
    exit;
}

$id = $_GET['id'];

// Kayıt var mı kontrol et
$stmt = $pdo->prepare("SELECT * FROM communications WHERE id=?");
$stmt->execute([$id]);
$record = $stmt->fetch();

if(!$record){
    header("Location: communications.php");
    exit;
}

// Dropdown verileri
$customers = $pdo->query("SELECT id,name FROM customers")->fetchAll();
$users = $pdo->query("SELECT id,username FROM users")->fetchAll();

// Güncelleme işlemi
if(isset($_POST['update_communication'])){
    $stmt = $pdo->prepare("UPDATE communications SET customer_id=?, user_id=?, note=?, contact_date=? WHERE id=?");
    $stmt->execute([$_POST['customer_id'], $_POST['user_id'], $_POST['note'], $_POST['contact_date'], $id]);
    header("Location: communications.php");
    exit;
}
?>

<?php include'header.php'; ?>
<section class="content p-3">
<div class="container-fluid">

    <h1 class="mb-4">İletişim Düzenle</h1>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title ">İletişim Bilgileri</h3>
        </div>
        <div class="card-body">
            <form method="post">

                <div class="form-group mb-3">
                    <label>Müşteri</label>
                    <select name="customer_id" class="form-control" required>
                        <?php foreach($customers as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $record['customer_id']==$c['id']?'selected':'' ?>><?= $c['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label>Kullanıcı</label>
                    <select name="user_id" class="form-control" required>
                        <?php foreach($users as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= $record['user_id']==$u['id']?'selected':'' ?>><?= $u['username'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label>Not</label>
                    <textarea name="note" class="form-control" required><?= htmlspecialchars($record['note']) ?></textarea>
                </div>

                <div class="form-group mb-3">
                    <label>Tarih</label>
                    <input type="datetime-local" name="contact_date" class="form-control" value="<?= str_replace(' ', 'T', $record['contact_date']) ?>" required>
                </div>

                <button type="submit" name="update_communication" class="btn btn-primary">Güncelle</button>

            </form>
        </div>
    </div>

</div>
</section>

</div>

<script src="plugins/jquery/jquery-3.6.0.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
