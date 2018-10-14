<?php
    ini_set('date.timezone', 'Asia/Ho_Chi_Minh');
    $f = fopen('log.txt', 'a+');
    if($f){
        $date = getdate();
        if(isset($_GET['name']) && $_GET['name'] == 'buoi2_bai1'){
            $f_name = 'Buoi2 - Bai1: ';
        }
        fwrite($f,(isset($f_name) ? $f_name: '') .  $date['mday']. '/'. $date['mon']. '/'.$date['year']. ' ' . $date['hours'] . ':' . $date['minutes'] . ':' . $date['seconds']);
        fwrite($f, PHP_EOL);
        fclose($f);
    }
?>