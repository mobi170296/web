<?php
	namespace MApp;
	if(defined('M_RUNNING')){
		require_once __DIR__.'/exceptions.php';
		class ProductInfo{
			public $idsp, $tensp, $chitietsp, $giasp, $hinhanhsp, $idtv;
			public function __construct($idsp, $tensp, $chitietsp, $giasp, $hinhanhsp, $idtv){
				$this->idsp = $idsp;
				$this->tensp = $tensp;
				$this->chitietsp = $chitietsp;
				$this->giasp = $giasp;
				$this->hinhanhsp = $hinhanhsp;
				$this->idtv = $idtv;
			}
		}
		class Product{
			private $dbcon;
			public function __construct($mcon){
				$this->dbcon = $mcon;
			}
			public function getProduct($idsp, $idtv){
				$result = $this->dbcon->query('SELECT * FROM sanpham WHERE idsp='.$idsp. ' AND idtv='. $idtv);
				if($result){
					if($result->num_rows){
						$product = $result->fetch_assoc();
						return new ProductInfo($product['idsp'], $product['tensp'], $product['chitietsp'], $product['giasp'], $product['hinhanhsp'], $product['idtv']);
					}else{
						throw new ProductNotFoundException('Không tìm thấy sản phẩm');
					}
				}else{
					throw new DBException($this->dbcon->error);
				}
			}
			public function getDBConnection(){
				return $this->dbcon;
			}
			public function getIDSanPham(){
				return $this->idsp;
			}
			public function getTenSanPham(){
				return $this->tensp;
			}
			public function getChiTietSanPham(){
				return $this->chitietsp;
			}
			public function getGiaSanPham(){
				return $this->giasp;
			}
			public function getHinhAnhSanPham(){
				return $this->hinhanhsp;
			}
			public function getIDThanhVien(){
				return $this->idtv;
			}
			public function addProduct($productinfo, $srcfile, $destfile){
				$result = $this->dbcon->query('START TRANSACTION READ WRITE');
				if(!$result){
					throw new DBException($this->dbcon->error);
				}
				
				$result = $this->dbcon->query("INSERT INTO sanpham(idsp, tensp, chitietsp, giasp, hinhanhsp, idtv) VALUES(null, '{$this->dbcon->real_escape_string(htmlspecialchars($productinfo->tensp))}', '{$this->dbcon->real_escape_string(htmlspecialchars($productinfo->chitietsp))}', {$productinfo->giasp}, '', {$productinfo->idtv})");
				if(!$result){
					$error = $this->dbcon->error;
					$this->dbcon->query('rollback');
					throw new DBException($error);
				}
				
				$idsp = $this->dbcon->insert_id;
				
				$filename = $idsp.'.'.UploadedFile::getExtension(UploadedFile::getMimeType($srcfile));
				
				$result = $this->dbcon->query("UPDATE sanpham SET hinhanhsp='{$filename}' WHERE idsp=".$idsp);
				if(!$result){
					$error = $this->dbcon->error;
					$this->dbcon->query('rollback');
					throw new DBException($error);
				}
				
				if(move_uploaded_file($srcfile, dirname($destfile).'/'.$filename)){
					$this->dbcon->query('commit');
				}else{
					$this->dbcon->query('rollback');
				}
			}
			public function editProduct($idsp, $newinfo, $src=null, $dest=null){
				$newinfo->tensp = $this->dbcon->real_escape_string($newinfo->tensp);
				$newinfo->chitietsp = $this->dbcon->real_escape_string($newinfo->chitietsp);
				$result = $this->dbcon->query('START TRANSACTION READ WRITE');
				if(!$result){
					throw new DBException($this->dbcon->error);
				}
				$result = $this->dbcon->query('SELECT hinhanhsp FROM sanpham WHERE idsp='.$idsp .' FOR UPDATE');
				if(!$result){
					throw new DBException($this->dbcon->error);
				}
				if($result->num_rows==0){
					throw new ProductNotFoundException('Sản phẩm không tồn tại không thể cập nhật');
				}
				$oldproductimage = $result->fetch_assoc()['hinhanhsp'];
				$result = $this->dbcon->query('UPDATE sanpham SET tensp=\''.$newinfo->tensp.'\', chitietsp=\''.$newinfo->chitietsp.'\', giasp=' . $newinfo->giasp .( $src!=null ? ', hinhanhsp=\'' .$newinfo->hinhanhsp .'\'': '' ).' WHERE idsp='.$idsp);
				if(!$result){
					throw new DBException($this->dbcon->error);
				}
				if($src!=null){
					unlink(dirname($dest) . '/' . $oldproductimage);
					if(move_uploaded_file($src, $dest)){
						$result = $this->dbcon->query('commit');
					}else{
						$this->dbcon->query('rollback');
						throw new Exception('Có lỗi trong lúc lưu hình ảnh sản phẩm');
					}
					
				}else{
					$result = $this->dbcon->query('commit');
					
				}
			}
			public function deleteProduct($idsp, $destfolder){
				$result = $this->dbcon->query('START TRANSACTION READ WRITE');
				if(!$result){
					throw new DBException($this->dbcon->error);
				}
				$result = $this->dbcon->query('SELECT * FROM sanpham WHERE idsp='.$idsp .' FOR UPDATE');
				if(!$result){
					throw new DBException($this->dbcon->error);
				}
				if($result->num_rows==0){
					throw new ProductNotFoundException('Sản phẩm không tồn tại không thể cập nhật');
				}
				$product = $result->fetch_assoc();
				
				$result = $this->dbcon->query('DELETE FROM sanpham WHERE idsp='.$idsp);
				if(!$result){
					throw new DBException($this->dbcon->error);
				}
				if(@unlink($destfolder.'/'.$product['hinhanhsp'])){
					$this->dbcon->query('commit');
				}else{
					$this->dbcon->query('rollback');
					throw new \Exception('Đã xảy ra lỗi trong lúc xóa sản phẩm');
				}
			}
			public function getProducts($userid){
				$result = $this->dbcon->query('SELECT * FROM sanpham WHERE idtv='.$userid);
				if(!$result){
					throw new DBException($this->dbcon->error);
				}
				
				if($result->num_rows){
					while($product = $result->fetch_assoc()){
						$products[] = new ProductInfo($product['idsp'], $product['tensp'], $product['chitietsp'], $product['giasp'], $product['hinhanhsp'], $product['idtv']);
					}
					return $products;
				}else{
					throw new ProductNotFoundException('Bạn chưa có sản phẩm nào');
				}
			}
		}
	}
?>