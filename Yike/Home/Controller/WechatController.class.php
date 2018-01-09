<?php
namespace Home\Controller;
use Admin\Controller\BaseController;

class WechatController extends BaseController {
    
    
    public function index(){
        include '/Mutan/Common/Exception/wechat/wechat.class.php';
        $token = 'uicbol1394945155'; //微信后台填写的TOKEN
        /* 加载微信SDK */
        $wechat = new Wechat($token);
        /* 获取请求信息 */
        $data = $wechat->request();
        if($data && is_array($data)){
            //在这里你可以分析用户发送过来的数据来决定需要做出什么样的回复
            $content = ''; //回复内容，回复不同类型消息，内容的格式有所不同
            $type    = ''; //回复消息的类型
            /* 响应当前请求(自动回复) */
            $wechat->response($content, $type);
        }
	}
	
	
    /**
     * 设备搜索结果
     *
     * */
    public function login(){
    
    
    } 
    
    
}