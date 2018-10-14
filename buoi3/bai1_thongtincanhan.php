<?php
	define ('M_RUNNING', 1);
	require_once __DIR__.'/defines/defines.php';
	require_once __DIR__.'/classes/exceptions.php';
	
	$CNF['TITLE'] = 'Bài 1 - Thông tin cá nhân';
	$CNF['STYLES'][] = 'styles/bai1_style.css';
	
	require_once __DIR__. '/includes/header.php';
	try{
		if(isset($_GET['username']) && $_GET['username']!=''){
			$mcon = @new MySQLi($DB['HOST'], $DB['USERNAME'], $DB['PASSWORD'], $DB['DB_NAME']);
			if($mcon->connect_errno){
				throw new \MApp\DBException($mcon->connect_error);
			}
			$username = $mcon->real_escape_string($_GET['username']);
			$result = $mcon->query('SELECT * FROM thanhvien WHERE tendangnhap=\''.$username.'\'');
			if(!$result){
				throw new \MApp\DBException($mcon->error);
			}
			if($result->num_rows==0){
				throw new \MApp\NotExistedUserException('Người dùng không tồn tại!');
			}
			$user = $result->fetch_assoc();
			#Load thông tin của người dùng
			foreach($user as $key => $value){
				$$key = $value;
			}
			#Bắt đầu của trang thông tin cá nhân
?>
			<div class="info">
				<div class="info-header">Xin chào <?php echo $tendangnhap; ?></div>
				<div class="info-content">
					<div class="info-content-avatar">
						<img width="100%" src="avatar/<?php echo $hinhanh; ?>"/>
					</div>
					<div class="info-content-content">
						<div>Nickname: <?php echo $tendangnhap; ?></div>
						<div>Giới tính: <?php echo $gioitinh; ?></div>
						<div>Nghề nghiệp: <?php echo $nghenghiep; ?></div>
						<div>Sở thích: <?php echo $sothich; ?></div>
						<div class="e-center"><a href="#" title="Không thể sử dụng tính năng này">Đăng xuất</a></div>
					</div>
				</div>
			</div>
<?php
		#Kết thúc trang thông tin cá nhân
		$mcon->close();
		}else{
			throw new Exception('Bạn không thể truy cập trang này!');
		}
	}catch(Exception $e){
		echo '<div class="error-box">'.$e->getMessage().'</div>';
	}
	
	require_once __DIR__.'/includes/footer.php';
?>