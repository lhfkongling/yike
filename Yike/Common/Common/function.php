<?php
/* ---------------------
 * 测试打印信息
 * ----------------------
 * LHF 2016-06-20
 * ----------------------
 */

/* ---------------------
 * 获取随机字符串
 * ----------------------
 * LHF 2016-06-20
 * ----------------------
 */
if(!function_exists('getRandChar'))
{
    function getRandChar($length){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;
    
        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
    
        return $str;
    }
}


/* ---------------------
 * 返回时间格式
 * ----------------------
 * LHF 2016-06-20
 * ----------------------
 */
if(!function_exists('fDate'))
{
    function fDate($l1,$l2=0,$int= 0){
        if (strlen($l1)==0) { return ; }
        //时间-》时间戳
        if($int == 1){
            
            $year=((int)substr($l1,0,4));//取得年份
            
            $month=((int)substr($l1 ,5,2));//取得月份
            
            $day=((int)substr($l1 ,8,2));//取得几号
            
            return mktime(0,0,0,$month,$day,$year);            
        }
        //时间戳 -》时间
		//$l1 = strtotime($l1);
        switch ($l2) {
            case '0':
                $I1 = date('Y-m-d H:i:s',$l1);
                break;
            case '1':
                $I1 = date('Y-n-j G:i:s',$l1);
                break;
            case '2':
                $I1 = date('Y-m-d',$l1);
                break;
            case '3':
                $I1 = date('Y-n-j',$l1);
                break;
            case '4':
                $I1 = date('Y年m月d日',$l1);
                break;
            case '5':
                $I1 = date('m月 Y',$l1);
                break;
            case '6':
                $I1 = date('Y-m',$l1);
                break;
            case '7':
                $I1 = date('Y/m/d',$l1);
                break;
            default:
                $I1 = date( $l2 , $l1 );
                break;
        }
        return $I1;
    }
}
//判断是否是手机端登录
if(!function_exists('ismobile'))
{
    function ismobile() {
    
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
            return true;
        //此条摘自TPM智能切换模板引擎，适合TPM开发
        if(isset ($_SERVER['HTTP_CLIENT']) &&'PhoneClient'==$_SERVER['HTTP_CLIENT'])
            return true;
        //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA']))
            //找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
        //判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array(
                'nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile'
            );
            //从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        //协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }
}

/* 过滤数据 验证信息函数
 * 字符串，数字
 * @parm $data 检查数组
 * @Parm $condistion 检查条件
 */

function filter_data($data = array(),$condistion = array('str'=>[],'int' => [],'float' => [])  ){

    //str字符串类型
    if(count($condistion['str']) > 0){
        foreach ($condistion['str'] as $k => $v){
            if(!is_numeric($k)) $res[$k] = !empty($data[$k])?(trim($data[$k])) :$v;
            else  $res[$v] = !empty($data[$v])?(trim($data[$v])) :'';
        }
    }
    //int 整数类型
    if(count($condistion['int']) > 0){
        foreach ($condistion['int'] as $k =>$v){
            if(!preg_match('/^\d*$/',$k) ) $res[$k] = !empty($data[$k]) ? intval($data[$k]) : $v;
            else  $res[$v] = !empty($data[$v]) ? intval($data[$v]) :0;
        }
    }
    //float 浮点数 类型
    if(count($condistion['float']) > 0){
        foreach ($condistion['float'] as $k => $v){
            if(!is_numeric($k)) $res[$k] = !empty($data[$k])?(floatval($data[$k])) : $v;
            else  $res[$v] = !empty($data[$v])?(floatval($data[$v])) :0;
        }
    }

    return $res;
}
/**
 * 获得广告内容
 *
 * @access  public
 * @param   string   $tagname   广告标识
 * @param   int      $cat_id    商品分类id
 * @param   int      $onlycode  是否只输出广告代码
 * @return  string
 */
function get_myad($tagname = '', $cat_id = 0, $onlycode = 0) {
    $tagname = trim($tagname);
    $tagname = str_replace("myad_", '', $tagname);

    if (!isset ($cat_id)) {
        $cat_id = 0;
    }

    if (!isset ($onlycode)) {
        $onlycode = 0;
    }

    if ($cat_id == '0') {
        $typesql = " AND is_show ='1'";
    } else {
     
    }

    /* 获取广告数据 */
    $Myad   = M('ad');
    $arr    = $Myad -> where(" code='" . $tagname . "'" . $typesql ) ->order(' ad_id DESC') -> find(); 
   

    /*取得广告时间*/
    $starttime  = fDate($arr['starttime'],'Y-m-d');
    $endtime    = fDate($arr['endtime'],'Y-m-d' );
    /*当前时间*/
    $nowtime    =fDate(time(),'Y-m-d' ); 
    /*是否限时*/
    $timeset = $arr['timeset'];
    /*广告类型*/
    $adtype = $arr['adtype'];

    
        /*代码广告*/
   
        if ($onlycode == 0) {
			$slidearr = json_decode($arr['info']);
			
	 		$slid = (array) $slidearr ;
	 		foreach ($slid as $k =>  $v)
	 		{
	 		    $v = (array)$v ;
	 		    $sl[$k]['img_url'] =  $v['img_url'] ; 
	 		    $sl[$k]['link'] = empty($v['param']) ? 'javascript:;':$v['param'];
	 		    $sl[$k]['name'] = $v['name'] ;
	 		}
        } 
	return $sl;
}


/**
 * 点播视频时间格式转换
 * pram：$time 影片长度
 * pram:$type 返回格式 
 * return: string
 * **/
function fVideoTime($time=0,$type=0){
    $m = floor($time/60);
    $s = ($time%60);
    if(strlen($m) == 1)  $m = '0'.$m;
    if(strlen($s) == 1)  $s = '0'.$s;
    switch ($type){
        case 1:
            $str = $m."'".$s.'"';
        break;
        
        default:
            $str = $m.':'.$s;
    }
    
    return $str ;
    
}

/**
 * 正则表达式 验证
 * LHF 
 * 
 * **/
function regular_verification($var,$type='email'){
    switch ($type){
        case 'email':
            $re = '/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/';
            break; 
        case 'telephone':
            $re = '/^1\d{10}$/';
            break;
        default:
            return false;
            
    }
//     return $re;

    if (!preg_match( $re , $var )) 
        return false;
    else 
        return true;
}

/**
 * 创建logic
 * @param string $name logic名称
 * @return Think\Model
 * @author 李志亮 <lizhiliang@kankan.com>
 */
function logic($name) {
    $name = ucfirst($name);
    static $_logic  =   array();
    if(isset($_logic[$name]))
        return $_logic[$name];
    $class      =   '\\Logic\\' . $name . 'Logic';
    $logic      =   class_exists($class)? new $class : new \Logic\BaseLogic;
    $_logic[$name]  =  $logic;
    return $logic;
}

/**
 * 创建service
 * @param string $name service名称
 * @return Service\BaseService
 * @author 李志亮 <lizhiliang@kankan.com>
 */
function service($name) {
    $name = ucfirst($name);
    static $_service  =   array();
    if(isset($_service[$name]))
        return $_service[$name];
    $class      =   '\\Service\\' . $name . 'Service';
    $service      =   class_exists($class)? new $class : new \Service\BaseService;
    $_service[$name]  =  $service;
    return $service;
}


/**
 * 创建service
 * @param string $name service名称
 * @return Service\BaseService
 * @author 李志亮 <lizhiliang@kankan.com>
 */
function model($name) {
    $name = ucfirst(parse_name($name, 1));
    static $_model  =   array();
    if(isset($_model[$name]))
        return $_model[$name];
    $class      =   '\\Model\\' . $name . 'Model';
    $model      =   class_exists($class)? new $class : new \Think\Model($name);
    $_model[$name]  =  $model;
    return $model;
}
//过滤字符串
function filterStr($str){
    return isset($str)?trim($str):'';
}
//过滤数字
function filterInt($Int){
    return isset($Int)?intval($Int):0;
}
//post ajax 请求页面限制
function post_ajax_restrict(){
    if(!IS_POST && !IS_AJAX)die('Cannot Access');
}

//折扣值验证
function checkSale($sale){

    if(is_numeric($sale)) {
       // $sale = floatval($sale);
        if ($sale > 1 or $sale < 0 ) return false ;
        else return true;
    }
    return $sale ;
}


/**
 * 生成面包屑网站定位 
 * Author：LHF
 * Time：2017-02-23
 * */
function wbeHere($arr=array()){
    $here = '<a href="'.U('Video/index').'">SOD</a> > ';
    foreach ($arr as $k=>$v){
        if( isset($v[1]) ) $here .=  '<a href="'.U($v[1]).'">'.$v[0].'</a> > ';
        else $here .= $v[0];
    }
    return $here;
    
}

//时间格式转换 秒 转换成  h:m:s
function sToTime($s,$type=0){
    if($type == 0){
        $h          = intval(intval($s) / 3600) ;
        $_m         = intval($s) % 3600 ;
        $m          = intval($_m / 60) ;
        $s          = $_m % 60;
        $smg = '';
        $smg .= $h > 0 ? $h.':':'';
        if(strlen($m) == 1 && $smg != '') $smg .= '0'.$m.':';
        else   $smg .= $m.':';
        if(strlen($s) == 1) $smg .= '0'.$s;
        else   $smg .= $s;
        return $smg; 
        
          
    }
}

/**
 * 上传图片过滤
 * @param $pictures 图片数组
 * @param $picture_ids 图片ID
 * @param $id_key_val 关联表的键值
 * */
function fitterImg($pictures,$picture_ids,$id_key_val){
    
    foreach ($pictures as $k =>$v){
        if(!empty($v)){
            $pictures[$k] = [
                $id_key_val['key'] =>$id_key_val['val'],
                'url' =>$v,
            ];
            if(!empty($picture_ids[$k]))
                $pictures[$k]['picture_id'] = $picture_ids[$k] ;
        }
        else
            unset($pictures[$k]) ;
    }
    return $pictures ;
    
}
/**
 * 三维数组 转 入数据库库 数组
 * 
 * */

function  toMysqlArr($arr){
    $data = [] ;
    foreach ($arr as $k =>$v)
    {
        $a[] = $k;
    }
    
    $v = current($arr) ;
    if(is_array($v))
    {
        $count = count($v);
        if($count > 0)
        {
            foreach ($v as $ke =>$va)
            {
                if(!empty($va))
                {
                    foreach ($a as $key =>$val)
                    {
                        $data[$ke][$val] = $arr[$val][$ke] ;
                    }
                     
                }

            }

        }
        
    }
    return $data ;
    
}

/**
 * 检查字符串是否在二维数组中
 * 
 * */
function chkInArray($v,$k,$arr=[],$return = ''){
    $i = 0;
    if( count($arr)> 0 )
    {
        foreach ($arr as $key => $val)
        {
            if ($val[$k] == $v)
            {
                $i = 1 ;
            }
        }
    }
    if ($return){
       if($i == 1) return  $return ;
    }else 
    {
        if($i == 1) return  true ;
        else return false ;
    }
    
}

//获取预约数量
function getBespokeCount($type = 1)
{
    if($type == 1) $field = 'user_id' ;
    else $field = 'builduser_id' ;
    
    return    D('Usersbuildings')-> where( $field ."='".session('user.user_id')."' and seetime > ".NOW_TIME )-> count();
}

//获取配置信息
function getOneConfig($field){
    return M('Config')->where(['field'=>$field])->getField('value');
}



// 获取微信配置信息
function getWechatObject(){
  $options =  array(
        'token'             => getOneConfig('token'), //填写你设定的key
        'encodingaeskey'    => getOneConfig('encodingaeskey'), //填写加密用的EncodingAESKey
        'appid'             => getOneConfig('appid'), //填写高级调用功能的app id
        'appsecret'         => getOneConfig('appsecret') //填写高级调用功能的密钥
    );//微信后台填写的TOKEN
    
  vendor('wechat.wechat','','.class.php');
  return new Wechat($options);
}

/**
 * 发送客服信息
 * 
 * 
 * */
function WxSendMessage($openID , $content ){

    $data = array(
        "touser"   =>$openID,  //o678K1YpnRgoZthRJNrZ90gYVWxQ
        "msgtype"  =>"text",
        "text"     =>array( "content"=>$content ) ,
    );
    return getWechatObject() -> sendCustomMessage($data) ;
}


// 获取素材
function getForeverList(){
    return getWechatObject() -> getForeverList('news',0,10) ;
}


function JsApiPay(){
   
    vendor('Wxpay.example.WxPay','','.JsApiPay.php');
    return  new JsApiPay() ;
}

function WxPayUnifiedOrder(){
    vendor('Wxpay.lib.WxPay','','.Api.php');
    return  new WxPayUnifiedOrder() ;
    
}
function WxPayConfig(){
    vendor('Wxpay.lib.WxPay','','.Config.php');
    return  new WxPayConfig() ;
}

//打印输出数组信息
function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}
    
function  checkWechat()
{
    $wechat = getWechatObject();
    $wechat->valid();
}
//微信会员登录注册信息
function getUserInfo(){
    
    $wechat = getWechatObject();
    
    $callback= getOneConfig('WechatCallback'); 'http://yk.900tech.com/Home/Wx/login';
    
    if(empty($_GET['code'])){
        
        $url = $wechat -> getOauthRedirect($callback);
        
        header("Location:".$url);
        die();
    }
    
//     pr($_GET) ;
    
    
    $res            = $wechat -> getOauthAccessToken();  
    $UserInfo       = $wechat -> getOauthUserinfo($res['access_token'],$res['openid']);
//     pr($_GET) ;
//     pr($UserInfo,1);
    
    $row = M('Users') ->where(['wechat'=>$UserInfo['openid']]) ->find() ;
    
    if(($row['user_id']) > 0 ){
        
        M('Users') ->save(
        [
            'user_id'=>$row['user_id'] , 
            'login_time'=>fDate(NOW_TIME),
            'login_ip'  =>get_client_ip(),
        ]
        );
//         pr($row);
        session('user',$row) ;
        return true;
    }else{
        if(empty($UserInfo['openid'] )){
            
            dump($UserInfo);die;
        }
        $data = [
            'nickname'      =>$UserInfo['nickname'] ,
            'wechat'        =>$UserInfo['openid'] ,
            'authentication'=>0,
            'uuid'          =>'YK'. date('Ymdhis',NOW_TIME).rand(1111, 9999),
            'login_time'    =>fDate(NOW_TIME),
            'login_ip'      =>get_client_ip(),
        ];
        $id = M('Users') ->add($data);
        if($id){
            $data['user_id'] = $id ;
            session('user', M('Users') ->where(['wechat'=>$UserInfo['openid']]) ->find() ) ;
//             pr($row);
            
            return true;
        }else 
            return false;
    }  

}
// 获取菜单
function getMenu(){
    $wechat = getWechatObject();
    return $wechat ->getMenu();
    
}
// deleteMenu() 删除菜单
function deleteMenu(){

    $wechat = getWechatObject();
    $wechat ->deleteMenu();
 
}

// createMenu($data) 创建菜单
function createMenu($data = array()){

    $wechat = getWechatObject();
    $res    = $wechat -> checkAuth();
   
//     $data = array (
//          'button' => array (
//            0 => array (
//              'name' => '扫码',
//              'sub_button' => array (
//                  0 => array (
//                    'type' => 'scancode_waitmsg',
//                    'name' => '扫码带提示',
//                    'key' => 'rselfmenu_0_0',
//                  ),
//                  1 => array (
//                    'type' => 'scancode_push',
//                    'name' => '扫码推事件',
//                    'key' => 'rselfmenu_0_1',
//                  ),
//              ),
//            ),
//            1 => array (
//              'name' => '发图',
//              'sub_button' => array (
//                  0 => array (
//                    'type' => 'pic_sysphoto',
//                    'name' => '系统拍照发图',
//                    'key' => 'rselfmenu_1_0',
//                  ),
//                  1 => array (
//                    'type' => 'pic_photo_or_album',
//                    'name' => '拍照或者相册发图',
//                    'key' => 'rselfmenu_1_1',
//                  )
//              ),
//            ),
//            2 => array (
//              'type' => 'location_select',
//              'name' => '发送位置',
//              'key' => 'rselfmenu_2_0'
//            ),
//          ),
//      );
    
    /* 获取请求信息 */
    return  $wechat -> createMenu($data);
   

}
/* 打印输出信息
 * param $arr 需要打印的数组
 * param $type 是否die 阻断
 * 默认 为 0 不阻断 ，1 为阻断程序运行 
 * Author ： LHF 
 * Time ： 2017-10-03
 */
function pr($arr , $type = 0){
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
    if($type == 1) die;
}

function testFunction(){
    $wechat =  getWechatObject();
//     $res = $wechat -> getMenu();
    //会员 列表 分组 
    
    //创建分组
//     $res = $wechat ->createGroup('开发者');
//     pr($res);
    //移动分组
    $res = $wechat -> updateGroup(100,'guanlizhe') ;//更改分组名称
    
    $res = $wechat -> updateGroupMembers(100,'ogUiAt4fFETkneFcmZIBpWo2lJYo');// 移动用户分组
       
        pr($res);
    
    //获取分组名单 
    $res = $wechat ->getGroup();
    pr($res);
    
    //获取会员 列表
//     $res = $wechat -> getUserList();
    //信息查询 与 回复信息 
  
    pr($res);
}

//公共函数
//获取文件修改时间
function getfiletime($file, $DataDir) {
    $a = filemtime($DataDir . $file);
    $time = date("Y-m-d H:i:s", $a);
    return $time;
}


//获取文件的大小
function getfilesize($file, $DataDir) {
    $perms = stat($DataDir . $file);
    $size = $perms['size'];
    // 单位自动转换函数
    $kb = 1024;         // Kilobyte
    $mb = 1024 * $kb;   // Megabyte
    $gb = 1024 * $mb;   // Gigabyte
    $tb = 1024 * $gb;   // Terabyte

    if ($size < $kb) {
        return $size . " B";
    } else if ($size < $mb) {
        return round($size / $kb, 2) . " KB";
    } else if ($size < $gb) {
        return round($size / $mb, 2) . " MB";
    } else if ($size < $tb) {
        return round($size / $gb, 2) . " GB";
    } else {
        return round($size / $tb, 2) . " TB";
    }
}

