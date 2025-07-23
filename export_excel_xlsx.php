<?php
require 'config/db.php';
require 'config/functions.php';
require 'config/layout.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\xlsx;

require_login();

$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();

// 엑셀 객체 생성
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('상품 목록');

// 헤더 행 작성
$sheet->fromArray(['ID', '상품명', 'SKU', '재고', '등록일'], NULL, 'A1');

$row = 2;
foreach($products as $product):
	$sheet->setCellValue("A".$row, $product['id']);
	$sheet->setCellValue("B".$row, $product['name']);
	$sheet->setCellValue("C".$row, $product['sku']);
	$sheet->setCellValue("D".$row, $product['quantity']);
	$sheet->setCellValue("E".$row, $product['created_at']);
	$row++;
endforeach;


// 다운로드
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=product_list.xlsx");
header("Cache-Control: max-age=0");

$writer = new xlsx($spreadsheet);
$writer->save("php://output");
exit;