<?php

//获取文件修改时间
//function getfiletime($file, $DataDir) {
//  $a = filemtime($DataDir . $file);
//  $time = date("Y-m-d H:i:s", $a);
//  return $time;
//}

//获取文件的大小
//function getfilesize($file, $DataDir) {
//  $perms = stat($DataDir . $file);
//  $size = $perms['size'];
//  // 单位自动转换函数
//  $kb = 1024;         // Kilobyte
//  $mb = 1024 * $kb;   // Megabyte
//  $gb = 1024 * $mb;   // Gigabyte
//  $tb = 1024 * $gb;   // Terabyte
//
//  if ($size < $kb) {
//      return $size . " B";
//  } else if ($size < $mb) {
//      return round($size / $kb, 2) . " KB";
//  } else if ($size < $gb) {
//      return round($size / $mb, 2) . " MB";
//  } else if ($size < $tb) {
//      return round($size / $gb, 2) . " GB";
//  } else {
//      return round($size / $tb, 2) . " TB";
//  }
//}


function resetFormatNumber($number){
    $num = floor($number) ;
     
    if(strlen($num) > 5 ){
        //大于10W显示方法
        $mod = $num % 10000 ;
        $num = floor($num / 10000) ;
        $i = strlen($num) % 3;
        if($i) $num = str_repeat('-', 3 - $i).$num;
        $num =  ltrim(implode(',', str_split($num,3)),'-');
        // $num =  number_format($mod/10000,2);
        if(floor($mod/100) > 0)  $num .= '.'.floor($mod/100);
        $num .= ' w' ;
    }else{
        //小于10W显示方法
        $i = strlen($num) % 3;
        if($i) $num = str_repeat('-', 3 - $i).$num;
        $num =  ltrim(implode(',', str_split($num,3)),'-');

        $mod = explode('.', $number);
        if(isset($mod[1])) $num.='.'. $mod[1];
         
    }
    return $num ;
}