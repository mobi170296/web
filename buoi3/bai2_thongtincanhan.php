<?php
	session_start();
	define ('M_RUNNING', 1);
	require_once __DIR__.'/defines/defines.php';
	require_once __DIR__.'/classes/exceptions.php';
	require_once __DIR__.'/classes/user.php';
	
	$CNF['TITLE'] = 'Bài 2 - Thông tin cá nhân';
	$CNF['STYLES'][] = 'styles/bai2_style.css';
	require_once __DIR__.'/includes/header.php';
	try{
		$mcon = @new MySQLi($DB['HOST'], $DB['USERNAME'], $DB['PASSWORD'], $DB['DB_NAME']);
		if($mcon->connect_errno){
			throw new \MApp\DBException($mcon->connect_error);
		}
		$user = new MApp\User($mcon);
		$user->login();
?>
		<div class="info">
			<div class="info-header">Xin chào <?php echo $user->getTenDangNhap(); ?></div>
			<div class="info-content">
				<div class="info-content-avatar">
					<img width="100%" src="avatar/<?php echo $user->getHinhAnh(); ?>"/>
				</div>
				<div class="info-content-content">
					<div>Nickname: <?php echo $user->getTenDangNhap(); ?></div>
					<div>Giới tính: <?php echo $user->getGioiTinh(); ?></div>
					<div>Nghề nghiệp: <?php echo $user->getNgheNghiep(); ?></div>
					<div>Sở thích: <?php echo $user->getSoThich(); ?></div>
					<div class="e-center" style="margin: 20px 0px;"><a href="bai3_themsanpham.php">Thêm sản phẩm</a><a href="bai4_danhsachsanpham.php">Danh sách sản phẩm</a><a href="bai2_dangxuat.php">Đăng xuất</a></div>
				</div>
			</div>
		</div>
<?php
		$mcon->close();
	}catch(\MApp\DBException $e){
		echo '<div class="error-box">'.$e->getMessage().'</div>';
	}catch(\MApp\LoginFailedException $e){
		echo '<div class="error-box">Bạn chưa đăng nhập <a href="bai2_dangnhap.php">đăng nhập</a></div>';
	}
	require_once __DIR__.'/includes/footer.php';
?>