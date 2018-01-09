<?php
/**
 * 视频信息
 * author：LHF
 * time：2017-3-30
 * */
namespace Admin\Controller;
use Think\Controller;
use Admin\Controller\BaseController;
use Common\Exception\AdminPage;
class BuildingsController extends BaseController {
    public function _empty(){

      $this->redirect('listBuildings');
    }
    
    //新房源列表
    public function listBuildings()
    {
        self::CommonGetList(0,'Buildings','build_id',[],$field='*',1);
        $this     -> display();
    }
    
    
    
    //新房源添加 修改
    public function editBuildings()
    {
    
        if(IS_POST){
           $res = logic('domysql')->doEditBuildings();
            if($res)
                $this -> success( '操作成功' , U('listCategory') );
            else
                $this -> error( '操作失败' );
            
            die;
        }
        $build_id = I('id',0,'intval') + 0;
        $model = D('buildings');
        if($build_id > 0)
            $row = $model->relation(true)->where(['build_id'=>$build_id])->find();
        
        $up_pic_num = M('Config')->where(['field'=>'up_pic_num'])->getField('value');
        
        $pic_num = $up_pic_num > count($row['picture']) ? $up_pic_num - count($row['picture']) : 0  ;
        
        $this->assign('row',$row);
        $this->assign('pic_num',$pic_num);
        $this     -> display();
    }
   
    
    public function delBUildings()
    {
        

        $id = I('id')+0;
         
        if(D('Buildings')->relation(true)->delete($id))
            $this -> success( '操作删除成功' );
        else
            $this -> error( '操作删除失败' );

    }
    
    //删除属性
    public function deleteAttr(){
    
        $data = I('post.');
    
        if(M($data['table'])->delete($data['id']))
            $this -> ajaxReturn(1) ;
        else
            $this -> ajaxReturn(0) ;
         
    }

}