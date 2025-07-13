<?php

function render_header($title){
echo <<<HTML
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>{$title}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="products.php">재고관리</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="products.php">상품</a></li>
        <li class="nav-item"><a class="nav-link" href="product_form.php">상품 추가</a></li>
        <li class="nav-item"><a class="nav-link" href="stock_log.php">입출고 이력</a></li>
        <li class="nav-item"><a class="nav-link" href="stats.php">통계</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">로그아웃</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4">
HTML;
}

function render_footer() {
	echo <<<HTML
</div>
</body>
</html>	
HTML;	
}