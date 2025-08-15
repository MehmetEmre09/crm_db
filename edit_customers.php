<?php
include 'db.php';

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $customer = $pdo->prepare("SELECT * FROM customers WHERE id=?");
    $customer->execute([$id]);
    $c = $customer->fetch();
}

// Form gönderildiğinde güncelle
if(isset($_POST['update_customer'])){
    $stmt = $pdo->prepare("UPDATE customers SET name=?, email=?, phone=?, address=? WHERE id=?");
    $stmt->execute([$_POST['name'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['id']]);
    header("Location: customers.php");
    exit;
}
?>
<?php include'header.php'; ?>


<section class="content p-3">
    <div class="container-fluid">
        <h1 class="mb-4">Müşteri Düzenle</h1>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Müşteri Bilgileri</h3>
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="id" value="<?= $c['id'] ?>">

                    <div class="form-group mb-3">
                        <label>Ad Soyad</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($c['name']) ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($c['email']) ?>">
                    </div>

                    <div class="form-group mb-3">
                        <label>Telefon</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($c['phone']) ?>">
                    </div>

                    <div class="form-group mb-3">
                        <label>Adres</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($c['address']) ?>">
                    </div>

                    <button type="submit" name="update_customer" class="btn btn-primary">Güncelle</button>
                    <a href="customers.php" class="btn btn-secondary">İptal</a>
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
