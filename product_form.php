<?php
require 'config/db.php';
require 'config/functions.php';
require 'config/layout.php';

require_login();

$id = $_GET['id'] ?? '';

if($id) {
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if(!$product) {
        exit("
        <script>
            alert('상품이 존재하지 않거나 이미 삭제되었습니다.');
            self.location.href='./products.php';
        </script>
        ");
    }
}


$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = "CSRF 토큰 오류";
    } else {
        $name = $_POST['name'] ?? '';
        $sku = $_POST['sku'] ?? '';
        $quantity = (int) $_POST['quantity'] ?? 0;
        $price = (int) $_POST['price'] ?? 0;
        $id = (int) $_POST['id'] ?? 0;
        $image = '';

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

            if($id) {

                if($image) {

                    $old_image = $_POST['old_image'] ?? '';
                    if($old_image) {
                        if(file_exists($old_image)) {
                            unlink($old_image);
                        }
                    }

                    $sql = "UPDATE products SET name=? , sku=?, quantity=?, price=?, image=? where id=?";
                    $arr = [$name, $sku, $quantity, $price, $image, $id];
                } else {
                    $sql = "UPDATE products SET name=? , sku=?, quantity=?, price=? where id=?";
                    $arr = [$name, $sku, $quantity, $price, $id];
                }

                $stmt = $pdo->prepare($sql);
                $stmt->execute($arr);    
            } else {
                $sql = "INSERT INTO products (name, sku, quantity, price, image) values(?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $sku, $quantity, $price, $image]);
            }

            header("Location: products.php");
            exit;
        }

    }  
}  




$csrf_token = generate_csrf_token();

$title = ($id == '') ? '상품 추가' : '상품 수정';
render_header($title);
?>
<div class="py-4">
    <h2 class="mb-4 text-center">📝 <?= $title ?></h2>

    <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>        

    <form method="post" enctype="multipart/form-data" class="mx-auto" style="max-width: 600px;">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <?php if($id): ?>
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="old_image" value="<?= $product['image'] ?>">
        <?php endif; ?>
        <div class="mb-3">
            <label class="form-label">상품명</label>
            <input type="text" name="name" class="form-control" value="<?= $product['name'] ?? '' ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">SKU</label>
            <input type="text" name="sku" class="form-control" value="<?= $product['sku'] ?? '' ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">수량</label>
            <input type="number" name="quantity" class="form-control" value="<?= $product['quantity'] ?? '' ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">가격 (원)</label>
            <input type="number" name="price" class="form-control" value="<?= $product['price'] ?? '' ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">상품 이미지</label>
            <input type="file" name="image" class="form-control">
            <?php if(!empty($product['name'])): ?>
                <img src="<?= $product['image'] ?>" alt="상품이미지" class="mt-2" style="max-width:150px;">
            <?php endif; ?>
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