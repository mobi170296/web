<?php
	namespace MApp;
	if(defined('M_RUNNING')){
		require_once __DIR__.'/exceptions.php';
		class UserInfo{
			public $id, $tendangnhap, $matkhau, $hinhanh, $gioitinh, $nghenghiep, $sothich;
			public function __construct($id, $tendangnhap, $matkhau, $hinhanh, $gioitinh, $nghenghiep, $sothich){
				$this->id = $id;
				$this->tendangnhap = $tendangnhap;
				$this->matkhau = $matkhau;
				$this->hinhanh = $hinhanh;
				$this->gioitinh = $gioitinh;
				$this->nghenghiep = $nghenghiep;
				$this->sothich = $sothich;
			}
		}
		class User{
			private $dbcon;
			public function __construct($dbcon){
				$this->dbcon = $dbcon;
			}
			public function register($userinfo, $srcfile, $destfile){
				$result = $this->dbcon->query('START TRANSACTION READ WRITE');
				if(!$result){
					throw new DBException($this->dbcon->error);
				}
				$result = $this->dbcon->query('SELECT * FROM thanhvien WHERE tendangnhap=\''.$this->dbcon->real_escape_string($userinfo->tendangnhap).'\' FOR UPDATE');
				if(!$result){
					$error = $this->dbcon->error;
					$this->dbcon->query('rollback');
					throw new DBException($error);
				}
				if($result->num_rows){
					$this->dbcon->query('rollback');
					throw new ExistedUserException('Người dùng có tên đăng nhập ' . $userinfo->tendangnhap . ' đã tồn tại');
				}
				$result = $this->dbcon->query("INSERT INTO thanhvien(id, tendangnhap, matkhau, hinhanh, gioitinh, nghenghiep, sothich) VALUES(null, '{$this->dbcon->real_escape_string($userinfo->tendangnhap)}', md5('{$this->dbcon->real_escape_string($userinfo->matkhau)}'), '{$this->dbcon->real_escape_string($userinfo->hinhanh)}', '{$userinfo->gioitinh}', '{$userinfo->nghenghiep}', '{$userinfo->sothich}')");
				if(!$result){
					$error = $this->dbcon->error;
					$this->dbcon->query('rollback');
					throw new DBException($error);
				}
				
				if(UploadedFile::scaleImageToPng($srcfile, $destfile, 300, 300)){
					$this->dbcon->query('commit');
				}else{
					$this->dbcon->query('rollback');
					throw new \Exception('Có lỗi trong quá trình đăng ký thành viên');
				}
			}
			public function login($username='', $password=''){
				if($username==''){
					#Login from SESSION
					if(isset($_SESSION['username']) && isset($_SESSION['password'])){
						$username = $_SESSION['username'];
						$password = $_SESSION['password'];
						$result = $this->dbcon->query('SELECT * FROM thanhvien WHERE tendangnhap=\''.$this->dbcon->real_escape_string($username).'\' AND matkhau=md5(\''.$this->dbcon->real_escape_string($password).'\')');
						if(!$result){
							throw new DBException($this->dbcon->error);
						}
						if($result){
							if($result->num_rows){
								$user = $result->fetch_assoc();
								foreach($user as $k => $v){
									$this->$k = $v;
								}
							}else{
								throw new LoginFailedException('Bạn chưa đăng nhập');
							}
						}else{
							throw new DBException($this->dbcon->error);
						}
					}else{
						throw new LoginFailedException('Bạn chưa đăng nhập');
					}
				}else{
					$result = $this->dbcon->query('SELECT * FROM thanhvien WHERE tendangnhap=\''.$this->dbcon->real_escape_string($username).'\' AND matkhau=md5(\''.$this->dbcon->real_escape_string($password).'\')');
					if($result){
						if($result->num_rows){
							$user = $result->fetch_assoc();
							foreach($user as $k => $v){
								$this->$k = $v;
							}
							$_SESSION['username'] = $username;
							$_SESSION['password'] = $password;
						}else{
							throw new LoginFailedException('Đăng nhập không thành công');
						}
					}else{
						throw new DBException($this->dbcon->error);
					}
				}
			}
			public function logout(){
				unset($_SESSION['username']);
				unset($_SESSION['password']);
			}
			public function isLogin(){
				return isset($this->id);
			}
			public function getDBConnection(){
				return $this->dbcon;
			}
			public function getID(){
				return $this->id;
			}
			public function getTenDangNhap(){
				return $this->tendangnhap;
			}
			public function getMatKhau(){
				return $this->matkhau;
			}
			public function getHinhAnh(){
				return $this->hinhanh;
			}
			public function getGioiTinh(){
				return $this->gioitinh;
			}
			public function getNgheNghiep(){
				return $this->nghenghiep;
			}
			public function getSoThich(){
				return $this->sothich;
			}
		}
	}
?>