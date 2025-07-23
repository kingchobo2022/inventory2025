<?php
require 'config/db.php';
require 'config/functions.php';
require 'config/layout.php';
require_login();

$sql = "SELECT COUNT(*) FROM products";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$totalProducts = $stmt->fetchColumn();

$sql = "SELECT * FROM products WHERE quantity < 10 ORDER BY quantity ASC";
$lowStocks = $pdo->query($sql)->fetchAll();

$title = '재고 통계';
render_header($title);
?>

	<div class="py-4">
		<h2 class="mb-4 text-center">📊 <?= $title ?></h2>

		<div class="row mb-4">
			<div class="col-md-6 mb-3">
				<div class="card shadow-sm border-start border-4 border-primary">
					<div class="card-body">
						<h5 class="card-title">총 상품 수</h5>
						<p class="display-5 fw-bold text-primary mb-0"><?= $totalProducts ?></p>
					</div>
				</div>
			</div>
		</div>

		<div class="card shadow-sm">
			<div class="card-header bg-white">
				<h5 class="mb-0">⚠️ 재고 부족 상품 (10개 미만)</h5>
			</div>
			<div class="table-responsive">
				<table class="table table-hover align-middle mb-0">
					<thead class="table-light">
						<tr>
							<th>상품명</th>
							<th>SKU</th>
							<th>수량</th>
						</tr>
					</thead>
					<tbody>
<?php foreach($lowStocks as $lowStock): ?>
						<tr>
							<td><?= htmlspecialchars($lowStock['name']) ?></td>
							<td><?= htmlspecialchars($lowStock['sku']) ?></td>
							<td><span class="badge bg-warning text-dark"><?= $lowStock['quantity'] ?></span></td>
						</tr>
<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>



<?php
render_footer();
