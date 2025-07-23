<?php
require 'config/db.php';
require 'config/functions.php';
require 'config/layout.php';

require_login();

$sql = "SELECT a.*, b.name FROM stock_logs a JOIN products b ON a.product_id=b.id 
 ORDER BY a.changed_at DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$logs = $stmt->fetchAll();

render_header('입출고 입력');
?>
<div class="py-4">
	<h2 class="mb-4 text-center">📋 입출고 이력</h2>
	<div class="card shadow-sm">
		<div class="card-body p-0">
			<div class="table-responsive">
				<table class="table table-hover align-middle mb-0">
					<thead class="table-light">
						<tr>
							<th>상품명</th>
							<th>유형</th>	
							<th>수량</th>
							<th>처리일시</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($logs as $log): ?>
						<tr>
							<td><?= htmlspecialchars($log['name']) ?></td>
							<td><?= $log['change_type'] == 'in' ? '입고': '출고' ?></td>
							<td><?= number_format($log['change_amount']) ?></td>
							<td><?= date('y년 m월 d일 H:i', strtotime($log['changed_at'])); ?></td>
						</tr>								
						<?php endforeach; ?>	
					</tbody>
				</table>
			</div>
		</div>
	</div>


</div>
<?php
// substr($log['changed_at'],0, 16);
render_footer();
