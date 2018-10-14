<?php
	session_start();
	define('M_RUNNING', 1);
	require_once __DIR__.'/defines/defines.php';
	require_once __DIR__.'/classes/exceptions.php';
	require_once __DIR__.'/classes/user.php';
	require_once __DIR__.'/classes/product.php';
	require_once __DIR__.'/classes/uploadedfile.php';
	require_once __DIR__.'/classes/datachecker.php';
	
	$CNF['TITLE'] = 'Bài 3 - Thêm sản phẩm';
	$CNF['STYLES'][] = 'styles/bai3_style.css';
	
	$showform = true;
	
	require_once __DIR__.'/includes/header.php';
	try{
		$mcon = @new MySQLi($DB['HOST'], $DB['USERNAME'], $DB['PASSWORD'], $DB['DB_NAME']);
		if($mcon->connect_errno){
			throw new \MApp\DBException($mcon->connect_error);
		}
		
		$user = new \MApp\User($mcon);
		$user->login();
		
		if(isset($_POST['addproduct'])){
			#Đã nhấn nút thêm sản phẩm
			$s_product_error = [];
			$s_product_error['productname'] = '';
			if(isset($_POST['productname']) && is_string($_POST['productname']) && mb_strlen($_POST['productname'], 'UTF-8') >= 1){
				$productname = $_POST['productname'];
			}else{
				$s_product_error['productname'] = 'Tên sản phẩm không hợp lệ';
			}
			
			$s_product_error['productdetail'] = '';
			if(isset($_POST['productdetail']) && is_string($_POST['productdetail']) && mb_strlen($_POST['productdetail'], 'UTF-8') >= 1){
				$productdetail = $_POST['productdetail'];
			}else{
				$s_product_error['productdetail'] = 'Chi tiết sản phẩm phải được mô tả';
			}
			
			$s_product_error['productprice'] = '';
			if(isset($_POST['productprice']) && is_string($_POST['productprice']) && preg_match('/^\d+$/', $_POST['productprice'])){
				$productprice = intval($_POST['productprice']);
				if($productprice < 0 || $productprice > 0xffffffff){
					$s_product_error['productprice'] .= 'Không thể quản lý sản phẩm có giá cao như vậy';
				}
			}else{
				$s_product_error['productprice'] .= 'Giá sản phẩm không hợp lệ';
			}
			
			$s_product_error['productimage'] = '';
			if(isset($_FILES['productimage']['error']) && $_FILES['productimage']['error'] == 0){
				$mimetype = \MApp\UploadedFile::getMimeType($_FILES['productimage']['tmp_name']);
				if($mimetype!=''){
					$ext = \MApp\UploadedFile::getExtension($mimetype);
					if(!\MApp\UploadedFile::valueInArray($ext, ['png', 'gif', 'jpeg'])){
						$s_product_error['productimage'] = 'Không hỗ trợ định dạng này';
					}
				}else{
					$s_product_error['productimage'] .= 'Không thể xác định được loại ảnh này';
				}
			}else{
				$s_product_error['productimage'] .= 'Vui lòng chọn ảnh cho sản phẩm';
			}
			
			if(\MApp\DataChecker::emptyValueInArray($s_product_error)){
				$product = new \MApp\Product($mcon);
				$product->addProduct(new \MApp\ProductInfo('', $_POST['productname'], $_POST['productdetail'], $_POST['productprice'], '', $user->getID()), $_FILES['productimage']['tmp_name'], __DIR__.'/sanpham/tensanpham.non');
				echo '<div class="success-box">Bạn đã thêm sản phẩm '.$_POST['productname'].' thành công! <a href="bai4_danhsachsanpham.php">Xem danh sách sản phẩm</a></div>';
				$showform = false;
			}else{
				throw new \MApp\FormDataNotValidException($s_product_error);
			}
		}
	}catch(\MApp\DBException $e){
		echo '<div class="error-box">'.$e->getMessage().'</div>';
		$showform = false;
	}catch(\MApp\LoginFailedException $e){
		echo '<div class="error-box">'.$e->getMessage().' <a href="bai2_dangnhap.php">đăng nhập</a></div>';
		$showform = false;
	}catch(\MApp\FormDataNotValidException $e){
		echo '<div class="error-box">';
		foreach($e->getErrors() as $error){
			if($error != ''){
				echo "<div>$error</div>";
			}
		}
		echo '</div>';
	}catch(Exception $e){
		echo '<div class="error-box">'.$e->getMessage().'</div>';
	}
	
	if($showform){
?>
		<div id="add-product-frm">
			<div class="add-product-frm-center">
				<div class="add-product-frm-title">Thêm sản phẩm mới</div>
				<div class="add-product-frm-notification">Vui lòng điền đầy đủ thông tin bên dưới để thêm sản phẩm mới</div>
				<div class="add-product-frm-border">
					<div class="add-product-frm-content">
						<form action="" method="post" enctype="multipart/form-data">
						<table>
						<tr><td>Tên sản phẩm</td><td><input type="text" name="productname" value="<?php if(isset($_POST['addproduct'])) echo $_POST['productname']; ?>"/></td></tr>
						<tr><td>Chi tiết sản phẩm</td><td><textarea name="productdetail" rows="6" cols="30" spellcheck="false"><?php if(isset($_POST['addproduct'])) echo $_POST['productdetail']; ?></textarea></td></tr>
						<tr><td>Giá sản phẩm</td><td><input type="text" name="productprice" value="<?php if(isset($_POST['addproduct'])) echo $_POST['productprice']; ?>"/> (VND)</td></tr>
						<tr><td>Hình đại diện</td><td><input type="file" name="productimage"/></td></tr>
						<tr><td colspan="2" style="text-align: center;"><input type="submit" name="addproduct" value="Lưu sản phẩm"/> <input type="reset" name="reset" value="Làm lại"/></td></tr>
						</table>
						</form>
					</div>
				</div>
				<div class="e-center"><a class="btn" href="bai4_danhsachsanpham.php">Xem danh sách sản phẩm</a></div>
			</div>
		</div>
<?php
	}
	require_once __DIR__.'/includes/footer.php';
?>