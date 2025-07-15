<?php
require 'config/db.php';
require 'config/functions.php';
require 'config/layout.php';

require_login();

$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = "CSRF 토큰 오류";
    } else {
        $name = $_POST['name'] ?? '';
        $sku = $_POST['sku'] ?? '';
        $quantity = (int) $_POST['quantity'] ?? 0;
        $price = (int) $_POST['price'] ?? 0;
        
        if(!empty($_FILES['image']['name'])) {
            $uploadDir = 'uploads/';
            $fileName = time() .'_'. basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName; // uploads/2437439473_이미지명.png
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $image = $targetPath;
            } else {
                $error = '이미지 업로드 실패';
            }
        }

        if(!$error) {
            $sql = "INSERT INTO products (name, sku, quantity, price, image) values(?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $sku, $quantity, $price, $image]);

            header("Location: products.php");
            exit;
        }

    }  
}  




$csrf_token = generate_csrf_token();

$title = '상품 추가';
render_header($title);
?>
<div class="py-4">
    <h2 class="mb-4 text-center">📝 상품 추가</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>        

    <form method="post" enctype="multipart/form-data" class="mx-auto" style="max-width: 600px;">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <div class="mb-3">
            <label class="form-label">상품명</label>
            <input type="text" name="name" class="form-control" value="" required>
        </div>
        <div class="mb-3">
            <label class="form-label">SKU</label>
            <input type="text" name="sku" class="form-control" value="" required>
        </div>
        <div class="mb-3">
            <label class="form-label">수량</label>
            <input type="number" name="quantity" class="form-control" value="" required>
        </div>
        <div class="mb-3">
            <label class="form-label">가격 (원)</label>
            <input type="number" name="price" class="form-control" value="" required>
        </div>
        <div class="mb-3">
            <label class="form-label">상품 이미지</label>
            <input type="file" name="image" class="form-control">
        </div>

        <div class="d-grid gap-2">
            <button class="btn btn-success">저장</button>
            <a href="products.php" class="btn btn-outline-secondary">취소</a>
        </div>
    </form>
</div>

<?php
render_footer();
?>