<?php
namespace Home\Controller;
use Think\Controller;
// use Home\Controller\BaseController;
class IndexController extends Controller {
    
    
    public function _empty()
    {
        $this->redirect('index') ;
    }
    
    public function index(){

        //楼盘推荐最新发布  5条信息
        
        $buildCount = D("Houses")-> where( [ 'release' => 1 ,'tag' => 1] ) ->count()  ;
        $secondCount = D("Houses")-> where( [ 'release' => 1 ,'tag' => 2] ) ->count()  ;
        
        $build = D("Houses") 
                -> relation(true)
                -> where( [ 'release' => 1 ,'tag' => 1 ,'is_best' => 1] )
                -> order('release_time desc')
                -> limit(10)
                -> select();
        
        //二手房推荐最新发布  5条信息
        
        $second = D("Houses")
                -> relation(true)
                -> where( [ 'release' => 1 ,'tag' => 2,'is_best' => 1 ] )
                -> order('release_time desc')
                -> limit(10)
                -> select();
       $sql =  D("Houses") ->getLastSql();
       trace($sql);
        //广告位  
        $banner = get_myad('index_banner');
        $leftad = get_myad('left_ad');
        $rightad = get_myad('right_ad');
        
        $this->assign('banner',$banner);
        $this->assign('leftad',$leftad);
        $this->assign('rightad',$rightad);
        
        $this->assign('buildCount',$buildCount);
        $this->assign('secondCount',$secondCount);
        $this->assign('build',$build);
        $this->assign('second',$second);
        $this->assign('header','蚁客');
        $this -> display () ;
    }
    
 
    
//     房贷计算器
    public function  counter(){
        $this->assign('header','房贷计算');
        $this->display();
    }
    
   public function text(){
        C();
    }
}