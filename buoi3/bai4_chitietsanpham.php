<?php
	session_start();
	define('M_RUNNING', 1);
	require_once __DIR__.'/defines/defines.php';
	require_once __DIR__.'/classes/exceptions.php';
	require_once __DIR__.'/classes/user.php';
	require_once __DIR__.'/classes/product.php';
	
	$CNF['TITLE'] = 'Bài 4 - Chi tiết sản phẩm';
	$CNF['STYLES'][] = 'styles/bai4_style.css';
	require_once __DIR__.'/includes/header.php';
	try{
		if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
			throw new Exception('Trang này không tồn tại!');
		}
		$mcon = @new MySQLi($DB['HOST'], $DB['USERNAME'], $DB['PASSWORD'], $DB['DB_NAME']);
		if($mcon->connect_errno){
			throw new DBException($mcon->connect_error);
		}
		
		$user = new \MApp\User($mcon);
		$user->login();
		
		$product = new \MApp\Product($mcon);
		
		$productinfo = $product->getProduct($_GET['id'], $user->getID());
		
		$currenttime = time();
		echo <<<PRODUCTINFO
		<div class="detail-product">
			<div class="detail-product-wrapper">
			<div class="detail-product-title">Thông tin sản phẩm {$productinfo->tensp}</div>
			<div class="detail-product-content">
				<div class="detail-product-avatar">
				<img width="100%" src="sanpham/{$productinfo->hinhanhsp}?v={$currenttime}"/>
				</div>
				<div class="detail-product-info">
					<div>Tên sản phẩm: {$productinfo->tensp}</div>
					<div>Giá sản phẩm: {$productinfo->giasp}</div>
					<div>Chi tiết sản phẩm: {$productinfo->chitietsp}</div>
				</div></div>
			</div>
			<div class="e-center"><a href="bai4_danhsachsanpham.php">Xem danh sách sản phẩm</a></div>
		</div>
PRODUCTINFO;
	}catch(\MApp\DBException $e){
		echo '<div class="error-box">'.$e->getMessage().'</div>';
	}catch(\MApp\ProductNotFoundException $e){
		echo '<div class="error-box">'.$e->getMessage().'</div>';
	}catch(\Exception $e){
		echo '<div class="error-box">'.$e->getMessage().'</div>';
	}
	require_once __DIR__.'/includes/footer.php';
?>