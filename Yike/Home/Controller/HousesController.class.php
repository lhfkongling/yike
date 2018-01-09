<?php
/**
 * 视频信息
 * author：LHF
 * time：2017-3-30
 * */
namespace Home\Controller;
use Think\Controller;
// use Home\Controller\BaseController;
class HousesController extends Controller {
    public function _empty(){

      $this->redirect('listHouses');
    }
    public function listHouses(){

        $_SESSION['user']['type'] =  M('Users')->where(['user_id'=>session('user.user_id')]) -> getField('type');
        
        $layout     =  M('Layout') -> order('sort desc , layout_id asc')->select();
        $floor      =  M('Floor') -> order('sort desc , floor_id asc')->select();
        
        $this       -> assign('floor',$floor);
        $this       -> assign('layout',$layout);
        
        $tag        =  I('tag');
        $this       -> assign('tag',$tag);
        if($tag == 1) 
            $this->assign('header','新房源');
       else
            $this->assign('header','二手房');

       $this       -> assignAttr();       
       $this       -> assign('tag',$tag);       
       $this       -> display();
    }

    //异步分页加载信息
    public function ajaxListPage()
    {
        $data = I('post.');
        
        $wh = [
            'release'=>1,
            'tag'=>$data['tag'],
        ];
        if(($data['keyword']) != 1){
            $wh[] = "name like ('%$data[keyword]%') ";    
        }
        
        $data['order'] = isset($data['order']) ? $data['order']: ' sort ' ;
        $data['sort']= isset($data['sort']) ? $data['sort']: ' asc ' ;
        
        //筛选属性
        if($data['fitter']){
            
            $data['fitter'] = (array)json_decode(htmlspecialchars_decode($data['fitter']));
        
            if($data['fitter']['address'])  
                $wh[] = "address like ('%".$data['fitter']['address']."%') ";
            
            if($data['fitter']['floor_id'])  
                $wh[] = "floor_id ='".$data['fitter']['floor_id']."'";
            
            if($data['fitter']['layout_id'])  
                $wh[] = "layout_id ='".$data['fitter']['layout_id']."'";
        
            if($data['fitter']['area_id'])  
                $wh[] = "area_id ='".$data['fitter']['area_id']."'";
        
            if($data['fitter']['age_id'])
                $wh[] = "age_id ='".$data['fitter']['age_id']."'";
            
            if($data['fitter']['fitment_id'])
                $wh[] = "fitment_id ='".$data['fitter']['fitment_id']."'";
            
            if($data['fitter']['from_id'])
                $wh[] = "from_id ='".$data['fitter']['from_id']."'";
            
            if($data['fitter']['nature_id'])
                $wh[] = "nature_id ='".$data['fitter']['nature_id']."'";
            
            if($data['fitter']['orientation_id'])
                $wh[] = "orientation_id ='".$data['fitter']['orientation_id']."'";
            
            if($data['fitter']['property_id'])
                $wh[] = "property_id ='".$data['fitter']['property_id']."'";
            
            if($data['fitter']['type_id'])
                $wh[] = "type_id ='".$data['fitter']['type_id']."'";
        }
        
        $data['count']  =  D('Houses')
                        -> where($wh)
                        -> count();
    
        $data['countPage'] = ceil( $data['count'] /C('PAGE_SIZE') );
    
        $data['list']   =  D('Houses')
                        -> relation(true)
                        -> where($wh)
                        -> page($data['page'],C('PAGE_SIZE'))
                        -> order($data['order'].' '.$data['sort']  )
                        -> select();
        
        $data['page'] ++ ;
        $this->ajaxReturn($data);    
    }
    
    //新房源添加 修改
    public function show(){

        $second_id  = I('id',0,'intval') + 0;
        $model      = D('Houses');
        $row        = $model->relation(true)->find($second_id);
       
        $row['perimeter_map'] = explode(',',$row['perimeter_map']);        
        $row['perimeter_introduce'] =  htmlspecialchars_decode(htmlspecialchars_decode($row['perimeter_introduce']));
        $row['linkman'] = explode(',', $row['linkman']);
        $row['telephone'] = explode(',', $row['telephone']);
//         房源推荐 
        if($row['tag'] == 2)
        {
           $best =   D('Houses')
                    ->relation(true)
                    -> where([ 'release' => 1 ,'tag'=>2 , 'is_best' => 1])
                    ->field('house_id,name,area,sell_price,layout_id')
                    ->limit(10)
                    -> order('release_time desc , house_id desc')
                    -> select();
           
           $this    -> assign('best',$best);                 
        }
        $baiduAK = M('Config')->where(['field'=>'baiduak'])->getField('value') ;
        $this       -> assign('baiduAK',$baiduAK);
        $this       -> assign('header',$row['name']);
        $this       -> assign('title',$row['name']);
        $this       -> assign('row',$row);
        $this       -> display();
 
    }
    //找房 
    public function findBuildings($type = false){

        $this->assignAttr();
        
        if($type == false){
            $tag = I('tag');
            $this       -> assign('tag',$tag);
            $this->assign('header','找房');
            $this ->display() ;
        }
    }
    

    //属性列表查询赋值
    protected function  assignTable($table)
    {
        $list    =  M($table) ->field($table.'_id as id,'.$table.'_name as name,sort' )-> order('sort desc')->select();
        $this    -> assign($table,$list);
    }
    
    public function  assignAttr(){
    
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
    }

}