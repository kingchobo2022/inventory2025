<?php
require 'config/db.php';
require 'config/functions.php';
require 'config/layout.php';
require_login();

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'name';
$order = $_GET['order'] ?? 'asc';
$page = max(1, (int) ($_GET['page'] ?? 1) );
$perPage = 10;
$offset = ($page - 1) * $perPage;
$whereCaluse = '';
$params = [];
if(!empty($search)) {
    $whereCaluse = "WHERE name LIKE :search OR sku LIKE :search";
    $params['search'] = "%$search%";
}

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM products $whereCaluse");
$totalStmt->execute($params);
$total = $totalStmt->fetchColumn();

$stmt = $pdo->prepare("SELECT * FROM products $whereCaluse ORDER BY $sort $order LIMIT :offset, :perPage");
foreach($params as $key => &$val) {
    $stmt->bindParam(':$key', $val); // ':search' = "%$search%";
}
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

$title = '상품 목록';
render_header($title);
?>

<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">📋 상품 목록</h2>
        <a href="product_form.php" class="btn btn-primary">+ 상품 추가</a>
    </div>

    <form class="row g-3 mb-3" method="get">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="상품명 또는 SKU 검색" value="">
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="name">상품명</option>
                <option value="quantity">수량</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="order" class="form-select">
                <option value="asc">오름차순</option>
                <option value="desc">내림차순</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-secondary w-100">검색</button>
        </div>

    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>상품명</th>
                    <th>SKU</th>
                    <th>수량</th>
                    <th>가격</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
<?php foreach($products AS $product): ?>                
				<tr>
					<td><?= $product['id'] ?></td>
					<td><?= htmlspecialchars($product['name']) ?></td>
					<td><?= htmlspecialchars($product['sku']) ?></td>
					<td><?= $product['quantity'] ?></td>
					<td><?= number_format($product['price']) ?>원</td>
					<td>
						<div class="btn-group btn-group-sm" role="group">
							<a href="product_form.php?id=<?= $product['id'] ?>" class="btn btn-outline-primary">수정</a>
							<a href="product_delete.php?id=<?= $product['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('정말 삭제하시겠습니까?')">삭제</a>
							<a href="stock.php?id=<?= $product['id'] ?>&action=in" class="btn btn-outline-success">입고</a>
							<a href="stock.php?id=<?= $product['id'] ?>&action=out" class="btn btn-outline-warning">출고</a>
						</div>
					</td>
				</tr>
<?php endforeach; ?>                
            </tbody>
        </table>
    </div>

    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            
                <li class="page-item">
                    <a class="page-link" href="">1</a>
                </li>
                <li class="page-item active">
                    <a class="page-link" href="">2</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="">3</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="">4</a>
                </li>
            
        </ul>
    </nav>
</div>	


<?php
render_footer();