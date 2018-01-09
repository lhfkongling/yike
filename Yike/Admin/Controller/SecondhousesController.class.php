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
class SecondhousesController extends BaseController {
    public function _empty(){

      $this->redirect('listSecondhouses');
    }
    public function listSecondhouses(){
        self::CommonGetList(0,'Secondhouses','second_id',[],$field='*',1);
        $this     -> display();
    }
    
    //新房源添加 修改
    public function editSecondhouses()
    {
        if(IS_POST)
        {
            $res = logic('domysql') -> doEditSecondhouses();
            if($res['error'] == 0)
                $this -> success( '操作成功' , U('listCategory') );
            else
                $this -> error( $res['data'] );
            
            die;
        }
        
        $second_id = I('id',0,'intval') + 0;
        $model = D('Secondhouses');
        if($second_id > 0)
            $row = $model->relation(true)->where(['second_id'=>$second_id])->find();
    
        $up_pic_num = M('Config')->where(['field'=>'up_pic_num'])->getField('value');
    
        $pic_num = $up_pic_num > count($row['picture']) ? $up_pic_num - count($row['picture']) : 0  ;
        
        self::assignTable('floor');
        self::assignTable('layout');
        self::assignTable('region');
        self::assignTable('nature');
        self::assignTable('fitment');
        self::assignTable('orientation');
        self::assignTable('property');
        self::assignTable('type');
        self::assignTable('from');
        self::assignTable('area');
        self::assignTable('age');

        
        $this       ->assign('row',$row);
        $this       ->assign('pic_num',$pic_num);
        $this       -> display();
    }
    //属性列表查询赋值
    protected function  assignTable($table)
    {
        $list    =  M($table) -> order('sort desc')->select();
        $this    -> assign($table,$list);
    }
    
    
    public function delSecondhouses()
    {

        $id = I('id')+0;
         
        if(D('Secondhouses')->relation(true)->delete($id))
            $this -> success( '操作删除成功' );
        else
            $this -> error( '操作删除失败' );
         
    
    }
    
    
    public function listTable(){
        
        $table = I('table');
        
        $list     =  M($table) ->field($table.'_id as id,'.$table.'_name as name,sort' ) -> order($table.'_id asc')->select();
        
        $this     -> assign('list',$list);
        
        $this     -> assign('table',$table);
        
        $this     -> display();
    }
    
    //分类编辑（添加、修改）
    public function editTable(){
    
        if(IS_POST) self::doEditTable() ;
    
        $id = I('id')+0;
    
        $table = I('table');
        
        $row = M($table)->field($table.'_id as id,'.$table.'_name as name,sort' )->find($id) ;
    
        $this     -> assign('row',$row);
        
        $this     -> assign('table',$table);
    
        $this     -> display();
    }
    //处理分类编辑提交的数据
    protected  function  doEditTable()
    {
        $data = I('post.');
        $table = I('table');
        $d = D($table);
        
        if(!$d ->create($data))
        {
            $this->error($d -> getError());
        }
        else
        {
            if($data[$table.'_id'] > 0)
                $res = $d->save();
            else
                $res = $d->add();
    
            if($res)
                $this -> success( '操作成功' , U('listTable',['table'=>$table]) );
            else
                $this -> error( '操作失败' );
    
        }
        die;
    }
    //分类删除
    public function delTable(){
    
        $id = I('id')+0;
        
        $table = I('table');
        
      switch ($table){
          case 'floor': $tablename = '楼层';
          case 'layout': $tablename = '户型';
          case 'region': $tablename = '区域';
          case 'nature': $tablename = '特色';
          case 'fitment': $tablename = '';
          case 'orientation': $tablename = '朝向';
          case 'property': $tablename = '产权';
          case 'type': $tablename = '类型';
          case 'from': $tablename = '来源';
          case 'area': $tablename = '面积';
          case 'age': $tablename = '房龄';
      }
      
        
        if( D('Secondhouses')->where([$table.'_id'=>$id])  ->count() )
            $this->error('该'.$tablename.'下有相关连房源信息，请移除或者删除后在删除此'.$tablename);
        else
        {
            if(M($table)->delete($id))
                $this -> success( '操作删除成功' );
            else
                $this -> error( '操作删除失败' );
        }
    }
    
}