<?php
// +----------------------------------------------------------------------
// | TP-Admin [ 多功能后台管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2016 http://www.5bkk.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 逍遥·李志亮 <xiaoyao.working@gmail.com>
// +----------------------------------------------------------------------

namespace Logic;
use Lib\Log;

/**
* 站点Logic
*/
class DomysqlLogic extends BaseLogic {

    //二手房 数据处理
    public function doEditHouses()
    {
        $data = I('post.');
       
        $msg = ['error'=>0];
         
        $data['picture'] = fitterImg ($data['picture'] , $data['picture_id'] , ['key'=>'build_id','val'=>$data['build_id']]);
         
        $data['housetype'] = toMysqlArr ($data['housetype']) ;
        
        $data['important'] = toMysqlArr ($data['important']) ;
    
        $data['secondary'] = toMysqlArr ($data['secondary']) ;
        
        $data['linkman']    = implode(',',array_filter($data['linkman'])) ;
        
        $data['telephone']  = implode(',',array_filter($data['telephone'])) ;
    
        if($data['editorValue']){
            $data['perimeter_introduce'] = $data['editorValue'];
        }
        unset($data['editorValue']) ;
        
        //地图代码
        if(!$data['allow_map']){
            unset($data['perimeter_map']);
        }
        unset($data['allow_map']) ;
        
        unset($data['picture_id']) ;
         
        $model = D('Houses');
    
        if( !$model ->create($data) ){
            $msg['error']= 1;
            $msg['data']= $model->getError() ;
            return $msg ;
            die ;
        }

        $data['user_id'] = isset($_SESSION['user']['user_id']) ? $_SESSION['user']['user_id']:$data['user_id']   ;
        
        if($data['release'] == 1)
            $data['release_time'] = NOW_TIME ;
    
    
        unset($data['__hash__']) ;
    
        if( empty($data['house_id'] ) )
        {
            $data['add_time'] = NOW_TIME;
            $msg['data']= $model->relation(true)->add($data);//新增
            return $msg ;
        }
        else
        {
            $model->relation(true)->save($data);//修改
            $msg['data']= $data['house_id'];
            return $msg ;
        }
         
    }
    
    //新房源 数据处理
    public function doEditBuildings()
    {
        $data = I('post.');
       
        pr($data,1) ; die;
        
        $msg = ['error'=>0];
        
        $data['picture'] = fitterImg ($data['picture'] , $data['picture_id'] , ['key'=>'build_id','val'=>$data['build_id']]);
    
        $data['housetype'] = toMysqlArr ($data['housetype']) ;
         
        $data['important'] = toMysqlArr ($data['important']) ;
    
        $data['secondary'] = toMysqlArr ($data['secondary']) ;
    
        //地图代码
        if(!$data['allow_map']){
            unset($data['perimeter_map']);
        }
        unset($data['allow_map']) ;
        unset($data['editorValue']) ;
        unset($data['picture_id']) ;
    
        if($data['release'] == 1)
            $data['release_time'] = NOW_TIME ;

        $model = D('Buildings');

        pr($data,1) ;
        
        if( !$model ->create($data) )
        {
            $msg['error']= 1;
            $msg['data']= $model->getError() ;
            return $msg ;
            die ;
        }
        
        
        unset($data['__hash__']) ;
    
       
        
        if( empty($data['build_id'] ) )
        {
         $data['add_time'] = NOW_TIME;
            $msg['data']= $model->relation(true)->add($data);//新增
            return $msg ;
        }
        else
        {
            $model->relation(true)->save($data);//修改
            $msg['data']= $data['build_id'];
            return $msg ;
        }
    }
    
    //上传二手房图片    
    public  function updateSecondPic(){
         
        $res['error'] = 0;
        $res['info'] = '';
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './uploads/second/'; // 设置附件上传根目录
        $upload->savePath  =     ''; // 设置附件上传（子）目录
        // 上传文件
        $info =   $upload->upload();
        if($info){
            foreach ($info as $k => $v){
                $res['up'][$k] = '/uploads/second/' .$v['savepath'] .$v['savename'] ;
            }
            $res['info'] = $v ;
        }else{
            $res['error'] = 1;
            $res['info'] = $upload->getError() ;
        }
        return $res ; 
    }
    
    //删除房源图片
    public function imagesDelete (){
        $data = I('post.');
    
        if($data['id']){
            M('HousePicture') -> delete($data['id']);
        }
        if($data['url']){
            unlink( './'.$data['url']) ;
        }
        return $data ; 
    }
    
    
  
}