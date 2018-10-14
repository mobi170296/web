<?php
	session_start();
	define('M_RUNNING', 1);
	require_once __DIR__.'/defines/defines.php';
	require_once __DIR__.'/classes/exceptions.php';
	require_once __DIR__.'/classes/user.php';
	require_once __DIR__.'/classes/product.php';
	
	$CNF['TITLE'] = 'Bài 4 - Xóa sản phẩm';
	$CNF['STYLES'][] = 'styles/bai4_style.css';
	
	require_once __DIR__.'/includes/header.php';
	try{
		$mcon = @new MySQLi($DB['HOST'], $DB['USERNAME'], $DB['PASSWORD'], $DB['DB_NAME']);
		if($mcon->connect_errno){
			throw new DBException($mcon->connect_error);
		}
		
		$user = new MApp\User($mcon);
		$user->login();
		if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
			throw new ProductNotFoundException('ID sản phẩm không đúng không thể sửa thông tin cho sản phẩm');
		}
		$product = new MApp\Product($mcon);
		if(isset($_POST['deleteproduct'])){
			$productinfo = $product->getProduct($_GET['id'], $user->getID());
			$product->deleteProduct($_GET['id'], __DIR__.'/sanpham/');
			echo '<div class="success-error">Bạn đã xóa sản phẩm <span style="font-weight: bold; color: red;">'.$productinfo->tensp.'</span> thành công <a href="bai4_danhsachsanpham.php">Quay trở lại xem danh sách sản phẩm</a></div>';
		}else{
			$productinfo = $product->getProduct($_GET['id'], $user->getID());
			?>
			<div class="delete-product-frm">
			<div class="delete-product-wrapper">
			<div class="delete-product-frm-title">Xóa sản phẩm</div>
			<div class="delete-product-content">
			<div>Bạn có muốn xóa sản phẩm <span style="font-weight: bold; color: red;"><?php echo $productinfo->tensp; ?></span> không?</div>
			<div class="e-center"><img src="sanpham/<?php echo $productinfo->hinhanhsp; ?>?v=<?php echo time();?>"/></div>
			<div>
			<form action="" method="post" enctype="application/x-www-form-urlencoded">
				<input type="submit" name="deleteproduct" value="Xóa sản phẩm" /> <a href="bai4_danhsachsanpham.php">Quay lại</a>
			</form>
			</div>
			</div>
			</div>
			</div>
			<?php
		}
	}catch(LoginFailedException $e){
		echo '<div class="error-box">'.$e->getMessage().'</div>';
	}catch(DBException $e){
		echo '<div class="error-box">'.$e->getMessage().'</div>';
	}catch(ProductNotFoundException $e){
		echo '<div class="error-box">'.$e->getMessage().'</div>';
	}catch(Exception $e){
		echo '<div class="error-box">'.$e->getMessage().'</div>';
	}
	
	require_once __DIR__.'/includes/footer.php';
?>