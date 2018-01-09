<?php
/**
 * 文章模型
 * author：LHF
 * time：2017-9-27
 * */
namespace Admin\Controller;
use Think\Controller;
use Admin\Controller\BaseController;
use Common\Exception\AdminPage;
class ArtilesController extends BaseController {
    public function _empty(){

      $this->redirect('listArtiles');
    }
    
    /* --------------------------------------------------------
     * 文章fenlei 信息处理
     *---------------------------------------------------------*/
    
    //分类列表
    public function listCategory(){
       
        $list     =  M('Category') -> order('sort asc , cat_id asc')->select();
        $this     -> assign('list',$list);
        
        $this -> assign('_act','TEST') ;
        $this     -> display();
       
    }
    //分类编辑（添加、修改）
    public function editCategory(){
        
        if(IS_POST) self::doEditCategory() ;
        
        $id = I('id')+0;
        
        $row = M('Category')->find($id) ;
        
        $this     -> assign('row',$row);
        
        $this     -> display();
    }
    //处理分类编辑提交的数据
    protected  function  doEditCategory()
    {
        $data = I('post.');        
        $d = D('Category');
        if(!$d ->create($data))
        {
           $this->error($d -> getError()); 
        }
        else
        {
            if($data['cat_id'] > 0)
                $res = $d->save();
            else 
                $res = $d->add();
            
            
            if($res)
                $this -> success( '操作成功' , U('listCategory') );    
            else 
                $this -> error( '操作失败' );
            
        }
        die;
    }
    //分类删除
    public function delCategory(){
        
        $id = I('id')+0;
        
        if(M('Artiles')->where(['cat_id'=>$id])  ->count())
            $this->error('该分类下有相关连文章，请移除或者删除后在删除此分类',U('listArtiles',['cat_id'=>$id]));
        else 
        {
            if(M('Category')->delete($id))
                 $this -> success( '操作删除成功' );    
            else 
                $this -> error( '操作删除失败' );
        } 
    }
    
    
    /* --------------------------------------------------------
     * 文章 信息处理 
     *---------------------------------------------------------*/
    //文章列表
    public function listArtiles()
    {
        self::CommonGetList(0,'Artiles','artile_id',[],$field='*',1);
        $this     -> display();
    }
    //分类编辑（添加、修改）
    public function editArtiles(){
        
        if(IS_POST)
        {
            self::doEditArtiles();
            die;
        }

        $cat     =   M('Category') ->field('cat_id,cat_name')-> order('sort asc , cat_id asc')->select();
       
        $id = I('id')+0;
        
        $row = D('Artiles')->where(['cat_id'=>$id])  ->find($id);
        
        $row['release'] = isset( $row['release'] ) ?  $row['release'] : 0;
        $row['sort'] = isset( $row['sort'] ) ?  $row['sort'] : 50;
        $row['read_number'] = isset( $row['read_number'] ) ?  $row['read_number'] : rand(100, 500);
        $row['detials'] =  htmlspecialchars_decode(htmlspecialchars_decode($row['detials']));
        
        $this     -> assign('cat',$cat);
        $this     -> assign('row',$row);
        $this -> assign('img_size',[500,500]) ;
        $this     -> display();
        
//        pr($row) ;
    
    }
    /**
     * 处理文章编辑数据
     * 2017-10-2
     * LHF
     * */
    protected function doEditArtiles()
    {
        //数据处理
        $data = I('post.');       
        $data['detials'] = $data['editorValue'] ;
        unset($data['editorValue']) ;
//         pr($data);
//         die;
        
        $model= D('Artiles');
        //检验数据
        if(!$model ->create($data)){
            $this->error($model->getError());
            die();
        }
        if($model->artile_id > 0){
            //修改
            $oldRelease = $model ->where(['artile_id' =>$model->artile_id])->getField('release') ;
            if( $oldRelease == 0 && $data['release'] == 1 )
            {
                $model->release_time = NOW_TIME;
            }
            elseif($data['release'] == 0)
            { 
                $model->release_time = 0;
            }
            
            
            $res = $model->save() ;
        }else{
            //添加
            $model->add_time = NOW_TIME;
            if($data['release'] == 1)
            {
                $model->release_time = NOW_TIME;
            }
            
            $res = $model->add() ;
            
        }
        
        if($res)
            $this -> success( '操作成功' , U('listArtiles') );
        else
            $this -> error( '操作失败' );
    }
    
    /**
     * 检查文章表字段是否重复 
     * 2017-10-2
     * LHF
     * */
    public function checkRepeatName(){
        
        if(!IS_AJAX) die('非法访问');
        $data = I('post.');
        if($data['id']){
           $res = M('Artiles')->where([ $data['idFiled']=>$data['id'] ,$data['field']=>$data['val']  ])->count();
            if($res > 0) $data['error'] = 1 ;
        }
        $this->ajaxReturn($data);
        
    }
    //分类删除
    public function delArtiles(){
       
        $id = I('id')+0;
       
        if(M('Artiles')->delete($id))
            $this -> success( '操作删除成功' );
        else
            $this -> error( '操作删除失败' );
       
    }
    
   
}