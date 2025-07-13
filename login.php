<?php

require 'config/db.php';
require 'config/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if(!verify_csrf_token($_POST['csrf_token'])) {
		$error = 'Invaild CSRF token';
	} else {
		$username = $_POST['username'] ?? '';
		$password = $_POST['password'] ?? '';

		$sql = "SELECT * FROM admins WHERE username=?";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([$username]);
		$admin = $stmt->fetch();
		if ($admin && password_verify($password, $admin['password'])) {
			$_SESSION['admin_logged_in'] = true;
			$_SESSION['admin_username'] = $admin['username'];
			header('Location: index.php');
			exit;
		} else {
			$error = 'Invalid username or password';
		}
	}
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>관리자 로그인</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h4 class="mb-3">관리자 로그인</h4>
			<?php if($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
			<?php endif; ?>		

            <form method="post" autocomplete="off">
				<input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <div class="mb-3">
                    <label class="form-label">아이디</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">비밀번호</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100">로그인</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
