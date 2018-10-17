<?php
	session_start();
	define('M_RUNNING', 1);
	require_once __DIR__.'/defines/defines.php';
	require_once __DIR__.'/classes/exceptions.php';
	require_once __DIR__.'/classes/user.php';
	require_once __DIR__.'/classes/uploadedfile.php';
	
	$CNF['TITLE'] = 'Bài 1 - Đăng ký thành viên';
	$CNF['STYLES'][] = 'styles/bai1_style.css';
	
	try{
		if(isset($_POST['register'])){
			$mcon = @new MySQLi($DB['HOST'], $DB['USERNAME'], $DB['PASSWORD'], $DB['DB_NAME']);
			if($mcon->connect_errno){
				throw new \MApp\DBException($mcon->connect_error);
			}
			$user = new \MApp\User($mcon);
			$s_userdata_error = [];
			
			$s_userdata_error['username'] = '';
			if(isset($_POST['username']) && is_string($_POST['username'])){
				$username = $_POST['username'];
				if(mb_strlen($username, 'UTF-8') > 30 || !preg_match('/^[a-z0-9A-Z]+$/', $username)){
					$s_userdata_error['username'] = 'Tên đăng nhập không hợp lệ chỉ chứa chữ cái hoặc số';
				}
			}else{
				$s_userdata_error['username'] = 'Tên đăng nhập không được để trống';
			}
			
			$s_userdata_error['password'] = '';
			if(isset($_POST['password'][0]) && is_string($_POST['password'][0]) && strlen($_POST['password'][0]) >= 6 && isset($_POST['password'][1]) && is_string($_POST['password'][1])){
				$password = $_POST['password'][0];
				if($password != $_POST['password'][1]){
					$s_userdata_error['password'] = 'Mật khẩu nhập lại không trùng';
				}
			}else{
				$s_userdata_error['password'] = 'Mật khẩu không được ít hơn 6 ký tự';
			}
			
			$s_userdata_error['avatar'] = '';
			if(isset($_FILES['avatar']['error']) && $_FILES['avatar']['error'] == 0){
				$mimetype = \MApp\UploadedFile::getMimeType($_FILES['avatar']['tmp_name']);
				$ext = \MApp\UploadedFile::getExtension($mimetype);
				if(!MApp\UploadedFile::valueInArray($ext, ['png', 'gif', 'jpeg'])){
					$s_userdata_error['avatar'] = 'Định dạng ảnh không được hỗ trợ';
				}
			}else{
				$s_userdata_error['avatar'] = 'Bạn phải chọn ảnh đại diện';
			}
			
			$s_userdata_error['gender'] = '';
			$gender = '';
			if(isset($_POST['gender']) && is_numeric($_POST['gender']) && intval($_POST['gender']) >= 0 && intval($_POST['gender']) < count($GENDER)){
				$gender = $GENDER[intval($_POST['gender'])];
			}else{
				$s_userdata_error['gender'] = 'Bạn phải có giới tính chứ';
			}
			
			$s_userdata_error['major'] = '';
			$major = '';
			if(isset($_POST['major']) && is_numeric($_POST['major']) && intval($_POST['major']) >= 0 && intval($_POST['major']) < count($MAJOR)){
				$major = $MAJOR[intval($_POST['major'])];
			}else{
				$s_userdata_error['major'] = 'Bạn chưa chọn nghề nghiệp';
			}
			
			$s_userdata_error['hobby'] = '';
			$ok_hobby = true;
			$hobby = '';
			if(isset($_POST['hobby']) && is_array($_POST['hobby'])){
				foreach($_POST['hobby'] as $v){
					if(is_numeric($v) && intval($v) >= 0 && intval($v) < count($HOBBY)){
						$hobby .= ' ' . $HOBBY[intval($v)] . ',';
					}else{
						$ok_hobby = false;
						break;
					}
				}
			}
			if($ok_hobby){
				if($hobby != '') $hobby = mb_substr($hobby, 1, mb_strlen($hobby, 'UTF-8') - 2);
			}else{
				$s_userdata_error['hobby'] = 'Sở thích không hợp lệ';
			}
			
			if($s_userdata_error['username'] != '' || $s_userdata_error['password'] != '' || $s_userdata_error['avatar'] != '' || $s_userdata_error['gender'] != '' || $s_userdata_error['major'] != '' || !$ok_hobby){
				#Tồn tại 1+ lỗi data
				throw new \MApp\FormDataNotValidException($s_userdata_error);
			}else{
				#Không có lỗi data 
				$user->register(new \MApp\UserInfo(null, $username, $password, $username.'.png', $gender, $major, $hobby), $_FILES['avatar']['tmp_name'], __DIR__.'/avatar/'.$username.'.png');
				header('location: bai1_thongtincanhan.php?username='.$username);
			}
			$mcon->close();
		}
	}catch(\MApp\FormDataNotValidException $e){
		$error = $e->getErrors();
	}catch(Exception $e){
		$error = $e->getMessage();
	}
	
	require_once __DIR__.'/includes/header.php';
	if(isset($error)){
		echo '<div class="error-box">';
		if(is_array($error)){
			foreach($error as $v){
				if($v != '') echo '<div>' . $v . '</div>';
			}
		}else{
			echo '<div>' . $error . '</div>';
		}
		echo '</div>';
	}
	?>
	<div class="wrapper">
	<div id="register-form">
			<div class="register-form-title">Đăng ký tài khoản mới</div>
			<div class="register-form-notification">Vui lòng điền đầy đủ thông tin bên dưới để đăng ký tài khoản mới</div>
			<div class="register-form-info">
				<form action="" method="post" enctype="multipart/form-data">
					<table>
						<tr>
							<td>Tên đăng nhập</td>
							<td><input type="text" name="username"/></td>
						</tr>
						<tr>
							<td>Mật khẩu</td>
							<td><input type="password" name="password[]"/></td>
						</tr>
						<tr>
							<td>Gõ lại mật khẩu</td>
							<td><input type="password" name="password[]"/></td>
						</tr>
						<tr>
							<td>Hình đại diện</td>
							<td><input type="file" name="avatar" accept="image/png,image/jpeg,image/gif"/></td>
						</tr>
						<tr>
							<td>Giới tính</td>
							<td>
								<input type="radio" name="gender" id="register-gender-male" value="1" checked/>
								<label for="register-gender-male">Nam</label>
								<input type="radio" name="gender" id="register-gender-female" value="0"/>
								<label for="register-gender-female">Nữ</label>
								<input type="radio" name="gender" id="register-gender-other" value="2"/>
								<label for="register-gender-other">Khác</label>
							</td>
						</tr>
						<tr>
							<td>Nghề nghiệp</td>
							<td>
								<select name="major">
									<option value="0">Học sinh</option>
									<option value="1">Sinh viên</option>
									<option value="2">Giáo viên</option>
									<option value="3">Khác</option>
								</select>
							</td>
						</tr>
						
						<tr>
							<td>Sở thích</td>
							<td>
								<input type="checkbox" name="hobby[]" id="register-hobby-sport" value="0"/>
								<label type="checkbox" for="register-hobby-sport">Thể thao</label>
								<input type="checkbox" name="hobby[]" id="register-hobby-travel" value="1"/>
								<label type="checkbox" for="register-hobby-travel">Du lịch</label>
								<input type="checkbox" name="hobby[]" id="register-hobby-music" value="2"/>
								<label type="checkbox" for="register-hobby-music">Âm nhạc</label><br/>
								<input type="checkbox" name="hobby[]" id="register-hobby-fashion" value="3"/>
								<label type="checkbox" for="register-hobby-fashion">Thời trang</label>
							</td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" name="register" value="Đăng ký"/> <input type="reset" value="Làm lại"/></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
		</div>
	<?php
	require_once __DIR__.'/includes/footer.php';
?>