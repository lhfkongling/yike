<?php
/**
 * HOME基类
 *
 * author ：zhangxudong
 * date ：2017-03-30
 */
namespace Home\Controller;

use Think\Controller;

class BaseController extends Controller
{
    public function _initialize(){
         
        if  (ACTION_NAME != login ) {
             if($_GET['uid']){
                 $row = M('Users') ->where(['user_id'=>$_GET['uid']]) ->find() ;
                 session('user',$row) ;
             }else if($_SESSION['user']['user_id'] <= 0 ){
    
                // 设置浏览器手动登录 跳过微信登录验证
                if(isset($_GET['suid'])){
                    $row = M('Users') ->where(['user_id'=>$_GET['suid']])->find();
                    session('user',$row) ;
                }else{
                    //判断是否微信浏览器
                    $user_agent = $_SERVER['HTTP_USER_AGENT'];
                    if (strpos($user_agent, 'MicroMessenger') === false) {
                        $this->error('本站目前只是支持微信浏览器，请关注蚁客公众号并在微信打开访问');
                    } else {
                        $this->redirect('User/login') ;
                    }
                }
            }
        }
    
    }
    
}