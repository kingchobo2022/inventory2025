<?php
require 'config/db.php';
require 'config/functions.php';
require 'config/layout.php';
require_login();

$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalStock = $pdo->query("SELECT SUM(quantity) FROM products")->fetchColumn();

$sql = "SELECT a.*, b.name FROM stock_logs a JOIN products b ON a.product_id=b.id 
 ORDER BY a.changed_at DESC LIMIT 5";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$logs = $stmt->fetchAll();


$title = '재고관리 대시보드';
render_header($title);
?>
<div class="py-4">
    <h2 class="mb-4 text-center">📦 재고관리 대시보드</h2>
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-start border-4 border-primary">
                <div class="card-body">
                    <h5 class="card-title">총 상품 수</h5>
                    <p class="card-text display-5 fw-bold text-primary"><?= $totalProducts ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-start border-4 border-success">
                <div class="card-body">
                    <h5 class="card-title">전체 재고 수량</h5>
                    <p class="card-text display-5 fw-bold text-success"><?= $totalStock ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">🕓 최근 입출고 이력</h5>
            <a href="stock_log.php" class="btn btn-sm btn-outline-primary">전체 보기</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>상품명</th>
                        <th>유형</th>
                        <th>수량</th>
                        <th>날짜</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($logs as $log): ?>
					<tr>
						<td><?= htmlspecialchars($log['name']) ?></td>
						<td>
                            <?php if($log['change_type'] == 'in'): ?>
							<span class="badge bg-success">입고</span>
                            <?php else: ?>
							<span class="badge bg-danger">출고</span>
                            <?php endif; ?>
						</td>
						<td><?= number_format($log['change_amount']) ?></td>
						<td><?= date('y년 m월 d일 H:i', strtotime($log['changed_at'])); ?></td>
					</tr>
                    <?php endforeach; ?>
				</tbody>
            </table>
        </div>
    </div>
</div>

<?php
render_footer();
