<?php
	session_start();
	define ('M_RUNNING', 1);
	require_once __DIR__.'/defines/defines.php';
	require_once __DIR__.'/classes/exceptions.php';
	require_once __DIR__.'/classes/user.php';
	require_once __DIR__.'/classes/product.php';
	
	$CNF['TITLE'] = 'Bài 4 - Danh sách sản phẩm';
	$CNF['STYLES'][] = 'styles/bai4_style.css';
	require_once __DIR__.'/includes/header.php';
	try{
		$mcon = @new MySQLi($DB['HOST'], $DB['USERNAME'], $DB['PASSWORD'], $DB['DB_NAME']);
		if($mcon->connect_errno){
			throw new \MApp\DBException($mcon->connect_error);
		}
		
		$user = new \MApp\User($mcon);
		$user->login();
		
		$product = new \MApp\Product($mcon);
		
		$products = $product->getProducts($user->getID());
?>
					<div class="info">
						<div class="info-wrapper">
						<div class="info-header">Xin chào <?php echo $user->getTenDangNhap(); ?></div>
						<div class="info-content">
							<div>Danh sách sản phẩm của bạn là:</div>
							<table>
							<tr><th>STT</th><th>Tên sản phẩm</th><th>Giá sản phẩm</th><th colspan="3">Lựa chọn</th></tr>
							<?php
							$index = 1;
							foreach($products as $p){
								echo '<tr>';
								echo "<td>$index</td>";
								echo "<td>{$p->tensp}</td>";
								echo "<td>{$p->giasp} (VND)</td>";
								echo '<td><a href="bai4_chitietsanpham.php?id='.$p->idsp.'">Xem chi tiết</a></td>';
								echo '<td><a href="bai4_suasanpham.php?id='.$p->idsp.'">Sửa</a></td>';
								echo '<td><a href="bai4_xoasanpham.php?id='.$p->idsp.'">Xóa</a></td>';
								echo '</tr>';
								$index++;
							}
							?>
							</table>
							<div class="e-center"><a class="btn" href="bai3_themsanpham.php">Thêm sản phẩm</a> <a class="btn" href="bai2_dangxuat.php">Đăng xuất</a></div>
						</div>
						</div>
					</div>
<?php
		
	}catch(\MApp\DBException $e){
		echo '<div class="error-box">'.$e->getMessage().'</div>';
	}catch(\MApp\LoginFailedException $e){
		echo '<div class="error-box">Bạn chưa đăng nhập không thể xem danh sách sản phẩm! <a href="bai2_dangnhap.php">đăng nhập</a></div>';
	}catch(\MApp\ProductNotFoundException $e){
		echo '<div class="error-box">'.$e->getMessage().'</div>';
	}
	require_once __DIR__.'/includes/footer.php';
?>