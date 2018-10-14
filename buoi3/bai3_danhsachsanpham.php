<?php
	session_start();
	define ('M_RUNNING', 1);
	require_once __DIR__.'/defines/defines.php';
	require_once __DIR__.'/classes/exceptions.php';
	require_once __DIR__.'/classes/user.php';
	require_once __DIR__.'/classes/product.php';
	
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
								echo "<td>{$p->giasp}</td>";
								echo '<td><a href="#" title="Bài 3 không có khả năng này">Xem chi tiết</a></td>';
								echo '<td><a href="#" title="Bài 3 không có khả năng này">Sửa</a></td>';
								echo '<td><a href="#" title="Bài 3 không có khả năng này">Xóa</a></td>';
								echo '</tr>';
								$index++;
							}
							?>
							</table>
							<div class="e-center"><a href="bai2_dangxuat.php">Đăng xuất</a></div>
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