<?php
	namespace MApp;
	if(defined('M_RUNNING')){
		class DataChecker{
			public static function emptyValueInArray($array){
				foreach($array as $v){
					if($v != ''){
						return false;
					}
				}
				return true;
			}
		}
	}
?>