<?php
include 'db.php';

// Müşteri ekleme
$error = '';
if(isset($_POST['add_customer'])){
    try {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';

        if(!$name) throw new Exception("Ad Soyad boş bırakılamaz!");

        $stmt = $pdo->prepare("INSERT INTO customers (name,email,phone,address) VALUES (?,?,?,?)");
        $stmt->execute([$name,$email,$phone,$address]);
        header("Location: customers.php");
        exit;
    } catch(Exception $e){
        $error = $e->getMessage();
    }
}

// Müşteri silme
if(isset($_GET['delete'])){
    $stmt = $pdo->prepare("DELETE FROM customers WHERE id=?");
    $stmt->ex2ecute([$_GET['delete']]);
    header("Location: customers.php");
    exit;
}

// Müşterileri çek
$customers = $pdo->query("SELECT * FROM customers ORDER BY id DESC")->fetchAll();
?>

<?php include'header.php'; ?>
<section class="content p-3">
    <div class="container-fluid">
        <h1 class="mb-4">Müşteriler</h1>

        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <!-- Ekleme Formu -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white"><h3 class="card-title">Yeni Müşteri Ekle</h3></div>
            <div class="card-body">
                <form method="post">
                    <div class="form-group mb-2">
                        <label>Ad Soyad</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="form-group mb-2">
                        <label>Telefon</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="form-group mb-2">
                        <label>Adres</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                    <button type="submit" name="add_customer" class="btn btn-success">
                        <i class="fas fa-plus"></i> Ekle
                    </button>
                </form>
            </div>
        </div>

        <!-- Müşteri Tablosu -->
        <div class="card">
            <div class="card-header bg-primary text-white"><h3 class="card-title">Mevcut Müşteriler</h3></div>
            <div class="card-body table-responsive">
                <table id="customerTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th><th>Ad Soyad</th><th>Email</th><th>Telefon</th><th>Adres</th><th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($customers as $c): ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td><?= htmlspecialchars($c['name']) ?></td>
                            <td><?= htmlspecialchars($c['email']) ?></td>
                            <td><?= htmlspecialchars($c['phone']) ?></td>
                            <td><?= htmlspecialchars($c['address']) ?></td>
                            <td>
                                <a href="edit_customers.php?id=<?= $c['id'] ?>" class="btn btn-lg btn-warning mb-1">
                                    <i class="fas fa-edit"></i> Düzenle
                                </a>
                                <a href="?delete=<?= $c['id'] ?>" class="btn btn-lg btn-danger mb-1" 
                                   onclick="return confirm('Bu müşteriyi silmek istediğinize emin misiniz?')">
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
    $('#customerTable').DataTable({
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
