<?php
require 'config/db.php';
require 'config/functions.php';
require 'config/layout.php';

require_login();

$product_id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? 'in';
$error = '';

if(!$product_id) {
	exit('<script>
		alert("상품 코드번호가 누락되었습니다.");
		self.location.href="products.php";
	</script>');
}

$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$product_id]);
$product = $stmt->fetch();
if(!$product) {
	exit('<script>
		alert("상품을 찾을 수가 없습니다.");
		self.location.href="products.php";
	</script>');
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
	if(!verify_csrf_token($_POST['csrf_token'])) {
		$error = 'CSRF 토큰 오류';
	} else {
		$amount = (int) $_POST['amount'];
		if($action == 'in') {
			$sql = "UPDATE products SET quantity = quantity + ? WHERE id= ?";
			$stmt = $pdo->prepare($sql);
			$stmt->execute([$amount, $product_id]);
		} else {
			if($product['quantity'] < $amount) {
				$error = '출고 수량이 현재 재고보다 많습니다.';
			} else {
				$sql = "UPDATE products SET quantity = quantity - ? WHERE id= ?";
				$stmt = $pdo->prepare($sql);
				$stmt->execute([$amount, $product_id]);
			}
		}
		if(!$error) {
			$sql = "INSERT INTO stock_logs (product_id, change_type, change_amount) values(?, ?, ?)";
			$stmt = $pdo->prepare($sql);
			$stmt->execute([$product_id, $action, $amount]);
			header("Location: products.php");
			exit;
		}
	}
}

$csrf_token = generate_csrf_token();

$title = ($action == 'in') ? '입고' : '출고';
render_header($title);
?>

<h4><?= $title ?> 처리</h4>
<?php if($error) : ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<form method="post">
	<input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    <div class="mb-3">
        <label>수량</label>
        <input type="number" name="amount" class="form-control" min="1" required>
    </div>
    <button class="btn btn-primary">처리</button>
    <a href="products.php" class="btn btn-secondary">취소</a>
</form>


<?php
render_footer();



