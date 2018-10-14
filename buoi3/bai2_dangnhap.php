<?php
	session_start();
	define('M_RUNNING', 1);
	
	require_once __DIR__.'/defines/defines.php';
	require_once __DIR__.'/classes/exceptions.php';
	require_once __DIR__.'/classes/user.php';
	
	$CNF['TITLE'] = 'Bài 2 - Đăng nhập';
	$CNF['STYLES'][] = 'styles/bai2_style.css';
	try{
		$mcon = @new MySQLi($DB['HOST'], $DB['USERNAME'], $DB['PASSWORD'], $DB['DB_NAME']);
		if($mcon->connect_errno){
			throw new \MApp\DBException($mcon->connect_error);
		}
		$user = new \MApp\User($mcon);
		$user->login();
		#Trường hợp người dùng đã đăng nhập
		require_once __DIR__.'/includes/header.php';
		echo '<div class="error-box">Bạn đã đăng nhập rồi không thể truy cập trang này!</div>';
		require_once __DIR__.'/includes/footer.php';
		$mcon->close();
	}catch(\MApp\DBException $e){
		require_once __DIR__.'/includes/header.php';
		echo '<div class="error-box">'.$e->getMessage().'</div>';
		require_once __DIR__.'/includes/footer.php';
	}catch(\MApp\LoginFailedException $e){
		#Trường hợp người dùng chưa đăng nhập
		try{
			if(isset($_POST['login'])){
				#Xử lý nếu người dùng nhấn Đăng nhập
				if(isset($_POST['username']) && is_string($_POST['username']) && isset($_POST['password']) && is_string($_POST['password'])){
					$user->login($_POST['username'], $_POST['password']);
				}else{
					throw new Exception('Bạn không đăng nhập được với thông tin đã đưa đâu!');
				}
				$mcon->close();
				#đã đăng nhập thành công đã thiết lập session!
				#bắt đầu redirect tới Bài 2 Thông tin cá nhân
				header('location: bai2_thongtincanhan.php');
			}
			
		}catch(Exception $e){
			$error = $e->getMessage();
		}
		#hiện form đăng nhập
		#hiện ở đây + include header sau khi logic control pre execute header function
		require_once __DIR__.'/includes/header.php';
		if(isset($error)){
			echo '<div class="error-box">'.$error.'</div>';
		}
?>
		<div class="wrapper">
			<div id="login-form">
				<div class="login-form-title">Đăng nhập tài khoản</div>
				<div class="login-form-notification">Nhập thông tin đăng nhập</div>
				<div class="login-form-info">
					<form action="" method="post" enctype="application/x-www-form-urlencoded">
						<div>Tên đăng nhập</div>
						<div><input type="text" size="30" name="username" placeholder="Tên đăng nhập"/></div>
						<div>Mật khẩu</div>
						<div><input type="password" size="30" name="password" placeholder="Mật khẩu"/></div>
						<div style="text-align: center;"><input type="submit" name="login" value="Đăng nhập"/></div>
					</form>
				</div>
			</div>
		</div>
<?php
		require_once __DIR__.'/includes/footer.php';
	}
?>