<?php
namespace Home\Controller;
use Admin\Controller\BaseController;
//use Addons\PublicBind\PublicBindAddon;
class WxController extends BaseController {
    
    
    public function index(){
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        
        \Think\Log::write( '$signature -> ' .$signature) ;
        \Think\Log::write( ' $timestamp -> ' .$timestamp) ;
        \Think\Log::write( '$nonce -> ' .$nonce) ;
         
        $data = I('get.');
        
        foreach ($data as $k =>$v ){
        
            \Think\Log::write($k . ' -> ' .$v) ;
        }
        
        
        checkWechat();
       
	}
	public function login(){
	   return getUserInfo();
	     
	}
	
	public function getMenu(){
	    return getMenu();
	
	}
	
	//生成支付配置信息
    public  function wxPay(){
        

    }
    
    //微信支付回调处理
    public function WxNotify()
    {
        $postStr = file_get_contents("php://input");
        $array = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        //支付成功
        if($array['result_code'] == 'SUCCESS'){
           $record_id =   $array['attach'] ;
           $this->paySuccess($record_id);
        }
    }
    
    public function paySuccess($record_id = 0){
        
        if(IS_AJAX){
            
            $paychar = I('paychar') ;
            if(session('paychar') !== $paychar) die('fail');
            $record_id =  I('id')  ;
        }
       $payRecord = service('Wx');
       
       $payRecord ->payRecord($record_id);
       
       if(IS_AJAX){
           $this->ajaxReturn(['error'=>0,'url'=>U('User/paySuccess')]) ;
       }
       
    }
    

    //验证是否来自于微信
   public  function checkWeixin()
   {
       $signature = $_GET["signature"];
       $timestamp = $_GET["timestamp"];
       $nonce = $_GET["nonce"];
        
       \Think\Log::write( '$signature -> ' .$signature) ;
       \Think\Log::write( ' $timestamp -> ' .$timestamp) ;
       \Think\Log::write( '$nonce -> ' .$nonce) ;
       
    $data = I('get.');
    
    foreach ($data as $k =>$v ){
    
        \Think\Log::write($k . ' -> ' .$v) ;
    }

    }
 
   
}