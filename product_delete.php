<?php
require 'config/db.php';
require 'config/functions.php';

require_login();

$id = $_GET['id'] ?? null;

if($id) {
	$sql = "SELECT image FROM products WHERE id = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$id]);
	$image = $stmt->fetchColumn();
	if($image) {
		if(file_exists($image)) {
			unlink($image);
		}
	}

	$sql = "DELETE FROM products WHERE id = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$id]);
}

header('Location: products.php');
exit;