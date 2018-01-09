<?php
/**
 * 文章模型
 * author：LHF
 * time：2017-9-27
 * */
namespace Home\Controller;
use Think\Controller;
// use Home\Controller\BaseController;
class ArtilesController extends Controller {
    public function _empty(){

      $this->redirect('listArtiles');
    }

    //文章列表
    public function listArtiles()
    {
        
        $cat = M('category') ->where('is_show=1')->order('sort asc')->field('cat_id,cat_name')->select();
       
        $catid = I('catid');
        $catid = !empty($catid) ? $catid : $cat[0]['cat_id'];
        $this -> assign('catid',$catid);
        $this -> assign('cat',$cat);
        $this->assign('header','资讯');
        $this     -> display();
    }
    //异步分页加载信息
    public function ajaxListPage()
    {
        $data = I('post.');
        $wh = [
            'cat_id'=>$data['catid'],
            'release'=>1
        ];
        if(($data['keyword']) != 1){
            $wh[] = "name like ('%$data[keyword]%') or keyword  like ('%$data[keyword]%') ";            
        }
        
        $data['count']  =  D('Artiles')  
                        -> where($wh) 
                        -> count();
        
        $data['countPage'] = ceil( $data['count'] /C('PAGE_SIZE') );
        
        $data['list']   =  D('Artiles')  
                        -> where($wh) 
                        -> page($data['page'],C('PAGE_SIZE')) 
                        -> order('sort desc , release_time desc') 
                        -> select();
        
        $this->assign('header','资讯');
       
        $data['page'] ++ ; 
        $this->ajaxReturn($data);
        
    }
    
    //分类编辑（添加、修改）
    public function show(){

        $id     = I('id')+0;        
        $row    = D('Artiles') -> find($id);
        
        $row['release']         = isset( $row['release'] ) ?  $row['release'] : 0;
        $row['sort']            = isset( $row['sort'] ) ?  $row['sort'] : 50;
        $row['read_number']     = isset( $row['read_number'] ) ?  $row['read_number'] : rand(100, 500);
        
        $this->assign('header','资讯');
        $this->assign('title',$row['name']);
        $row['detials'] =  htmlspecialchars_decode(htmlspecialchars_decode($row['detials']));
        $this     -> assign('row',$row);            
        $this     -> display();    
    }
    public function img(){
        $tag = I('get.tag');
        $this     -> assign('tag',$tag);
        $this->assign('header','资讯');
        $this     -> display();
    }
}