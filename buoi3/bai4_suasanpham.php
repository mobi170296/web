<?php
	session_start();
	define('M_RUNNING', 1);
	require_once __DIR__.'/defines/defines.php';
	require_once __DIR__.'/classes/exceptions.php';
	require_once __DIR__.'/classes/user.php';
	require_once __DIR__.'/classes/product.php';
	require_once __DIR__.'/classes/uploadedfile.php';
	require_once __DIR__.'/classes/datachecker.php';
	
	use MApp\DataChecker;
	
	$CNF['TITLE'] = 'Bài 4 - Sửa thông tin sản phẩm';
	$CNF['STYLES'][] = 'styles/bai4_style.css';
	require_once __DIR__.'/includes/header.php';
	
	try{
		$mcon = @new MySQLi($DB['HOST'], $DB['USERNAME'], $DB['PASSWORD'], $DB['DB_NAME']);
		if($mcon->connect_errno){
			throw new \MApp\DBException($mcon->connect_error);
		}
		
		$user = new MApp\User($mcon);
		#login nếu có vấn đề ném ra ngoại lệ bên ngoài M1
		$user->login();
			if(isset($_GET['id']) && is_numeric($_GET['id'])){
				$product = new MApp\Product($mcon);
				if(isset($_POST['editproduct'])){
					$product->getProduct($_GET['id'], $user->getID());
					#Không có ngoại lệ xảy ra thì sản phẩm đã tồn tại!
					#Đã nhấn submit sửa thông tin sản phẩm
					#form validation
					try{
						$s_productdata_error = [];
						
						$s_productdata_error['productname'] = '';
						if(isset($_POST['productname']) && is_string($_POST['productname']) && mb_strlen($_POST['productname'], 'UTF-8') >= 1){
							$productname = $_POST['productname'];
						}else{
							$s_productdata_error['productname'] = 'Tên sản phẩm không hợp lệ';
						}
						
						$s_productdata_error['productdetail'] = '';
						if(isset($_POST['productdetail']) && is_string($_POST['productdetail']) && mb_strlen($_POST['productdetail'], 'UTF-8') >= 1){
							$productdetail = $_POST['productdetail'];
						}else{
							$s_productdata_error['productdetail'] = 'Chi tiết sản phẩm phải được mô tả';
						}
						
						$s_productdata_error['productprice'] = '';
						if(isset($_POST['productprice']) && is_string($_POST['productprice']) && preg_match('/^\d+$/', $_POST['productprice'])){
							$productprice = intval($_POST['productprice']);
							if($productprice < 0 || $productprice > 0xffffffff){
								$s_productdata_error['productprice'] .= 'Không thể quản lý sản phẩm có giá cao như vậy';
							}
						}else{
							$s_productdata_error['productprice'] .= 'Giá sản phẩm không hợp lệ';
						}
						
						$b_productimage = false;
						$s_productdata_error['productimage'] = '';
						if(isset($_FILES['productimage']['error']) && $_FILES['productimage']['error'] == 0){
							$b_productimage = true;
							$mimetype = \MApp\UploadedFile::getMimeType($_FILES['productimage']['tmp_name']);
							if($mimetype!=''){
								$ext = \MApp\UploadedFile::getExtension($mimetype);
								if(!\MApp\UploadedFile::valueInArray($ext, ['bmp', 'png', 'gif', 'jpg', 'jpeg'])){
									$s_productdata_error['productimage'] .= 'Không hỗ trợ định dạng ảnh này';
								}else{
									#Khẳng định tên tập tin mới
									#Sản phẩm tồn tại => $_GET['id'] đúng =>
									$productimage = $_GET['id'].'.'.$ext;
								}
							}else{
								$s_productdata_error['productimage'] .= 'Đã xảy ra lỗi tập tin';
							}
						}
						
						if(!DataChecker::emptyValueInArray($s_productdata_error)){
							throw new \MApp\FormDataNotValidException($s_productdata_error);
						}
						
						if($b_productimage){
							$product->editProduct($_GET['id'], new Mapp\ProductInfo($_GET['id'], $productname, $productdetail, $productprice, $productimage, $user->getID()), $_FILES['productimage']['tmp_name'], __DIR__.'/sanpham/'.$productimage);
						}else{
							$product->editProduct($_GET['id'], new MApp\ProductInfo($_GET['id'], $productname, $productdetail, $productprice, null, $user->getID()), null, null);
						}
						echo '<div class="success-box">Đã cập nhật sản phẩm thành công</div>';
					}catch(\MApp\FormDataNotValidException $e){
						echo '<div class="error-box">';
						foreach($e->getErrors() as $v){
							echo '<div>' . $v . '</div>';
						}
						echo '</div>';
					}catch(\Exception $e){
						echo "<div class=\"error-box\">{$e->getMessage()}</div>";
					}
				}
				
				$productinfo = $product->getProduct($_GET['id'], $user->getID());
				
				?>
				<div class="edit-product-frm">
				<div class="edit-product-frm-title">Sửa thông tin sản phẩm</div>
				<div class="edit-product-wrapper">
				<div class="edit-product-content">
				<form action="" method="post" enctype="multipart/form-data">
				<table>
				<tr>
				<td>Tên sản phẩm</td><td><input type="text" name="productname" value="<?php echo $productinfo->tensp; ?>"/></td>
				</tr>
				<tr>
				<td>Chi tiết sản phẩm</td><td><textarea name="productdetail" cols="30" rows="6" spellcheck="false"><?php echo $productinfo->chitietsp; ?></textarea></td>
				</tr>
				<tr>
				<td>Giá sản phẩm</td><td><input type="text" name="productprice" value="<?php echo $productinfo->giasp; ?>"/> (VND)</td>
				</tr>
				<tr>
				<td>Hình ảnh sản phẩm</td><td><img class="product-image" src="sanpham/<?php echo $productinfo->hinhanhsp; ?>?v=<?php echo time(); ?>"/><br/><input type="file" name="productimage" accept="image/*"/></td>
				</tr>
				<tr>
				<td colspan="2"><input type="submit" name="editproduct" value="Lưu thông tin"/> <input type="reset" value="Trở lại thông tin cũ"/></td>
				</tr>
				</table>
				</form>
				</div>
				</div>
				<div class="e-center"><a href="bai4_danhsachsanpham.php">Quay trở lại danh sách sản phẩm</a></div>
				</div>
				<?php
			}else{
				throw new \MApp\ProductNotFoundException('ID sản phẩm không đúng không thể sửa thông tin cho sản phẩm');
			}
		
	}catch(MApp\DBException $e){
		echo '<div class="error-box">Lỗi Database: ' . $e->getMessage() . '</div>';
	}catch(MApp\LoginFailedException $e){
		echo '<div class="error-box">'.$e->getMessage().'</div>';
	}catch(MApp\ProductNotFoundException $e){
		echo '<div class="error-box">'.$e->getMessage().'</div>';
	}catch(MApp\Exception $e){
		echo '<div class="error-box">'.$e->getMessage().'</div>';
	}
	
	require_once __DIR__.'/includes/footer.php';
?>