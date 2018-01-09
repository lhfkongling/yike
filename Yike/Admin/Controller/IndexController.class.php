<?php
namespace Admin\Controller;
use Admin\Controller\BaseController;
class IndexController extends BaseController {
    
 //空方法跳转设置
    public function _empty(){ $this->redirect('Index/index');}
    
    //后台首页方法
    public function index(){
    	
    	$Usersbuildings  = D('Usersbuildings') ->count() ;
    	$build  = D('Houses') ->where(['tag' => 1]) ->count() ;
    	$second  = D('Houses') ->where(['tag' => 2])->count() ;
    	$user  = D('Users') ->count() ;
    	
    	$this -> assign('Usersbuildings' , $Usersbuildings);
    	$this -> assign('build' , $build);
    	$this -> assign('second' , $second);
    	$this -> assign('user' , $user);
    	
        $this->display();
    }

}