<?php
	namespace MApp;
	if(defined('M_RUNNING')){
		class DBException extends \Exception{
			public function __construct($msg){
				parent::__construct($msg);
			}
		}
		
		class LoginFailedException extends \Exception{
			public function __construct($msg){
				parent::__construct($msg);
			}
		}
		
		class ProductNotFoundException extends \Exception{
			public function __construct($msg){
				parent::__construct($msg);
			}
		}
		class FormDataNotValidException extends \Exception{
			private $aerror;
			public function __construct($a){
				$this->aerror = $a;
			}
			public function getErrors(){
				return $this->aerror;
			}
		}
		class ExistedUserException extends \Exception{
			public function __construct($msg){
				parent::__construct($msg);
			}
		}
		class NotExistedUserException extends \Exception{
			public function __construct($msg){
				parent::__construct($msg);
			}
		}
	}
?>