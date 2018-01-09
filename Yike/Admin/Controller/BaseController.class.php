<?php
/**
 * HOME基类
 *
 * author ：zhangxudong
 * date ：2017-03-30
 */
namespace Admin\Controller;

use Think\Controller;
use Common\Exception\AdminPage;

class BaseController extends Controller
{
   
	/**
	 * 初始化数据
	 * return void
	 */
	protected function _initialize(){
     
       //判断是否登录
	  $admin = I('session.admin');
        if(!IS_AJAX && !IS_POST) {
    	    if(empty($admin)  && ACTION_NAME != 'login' ) $this->redirect('Common/login'); 
        }
        
        if(!C('CONFIG')) {
            $list = M('Config')->select();
            foreach ($list as $v) {
                $row[$v['field']] = $v['value'] ;
            }
            C('CONFIG',$row) ; 
        }
        
        $this -> assign('img_size',explode(',',C('CONFIG.img_size'))) ;
        
        $this -> assign('_con',CONTROLLER_NAME) ;
        $this -> assign('_act',ACTION_NAME) ;

    }
  
    
    /**
     * 提取  list列表查询方法优化
     * $return 返回类型  1 返回数据  ，0 assign 模板赋值
     * $table  查询表名
     * $order  默认排序字段
     * $param  筛选搜索参数  如   
     * ['name'=>['type'=>'eq',field=>['ad_name','email']]  ]
     * 
     * 参数说明：  
     * name: 为 参数键  如  $data['name'] 如果 name  为 值   如 传入$param  = ['name']  则  参数键 和 数据表查询键 都是 name  
     * type: 如果   type => eq  搜索条件 是   =  ， 
     *       否则  搜索条件 是  like  当前只支持这两种 查询方式 
     * field:  如果 field 不存在  数据表查询键 为  name ，
     *         如果存在 且为 字符串     数据表查询键 为  field 值 
     *         如果存在 且为数组    则是 or  关联 数组的值 为  数据表查询键   与  $data['name'] 组合查询数据库 
     * $relation : 是否使用关系模型
     * Author ： LHF 
     * Time ： 2017-06-07
     * */
    public function CommonGetList($return,$table,$order,$param = array(),$field='*',$relation= false){
        
        $data = I('get.');
        if(IS_POST){
            $data = I('post.');
            $_GET['p']           ='1';
        }
        trace($data['sort']) ;
        //搜索筛选排序参数过滤
        $data['order']              = isset($data['order'])?$data['order']:$order;
        $data['sort']               = isset($data['sort']) ?$data['sort'] :'desc';
        $data['pagesize']           = isset($data['pagesize'])?filterInt($data['pagesize']):C('PAGESIZE');
        $_GET['p']                  = isset($_GET['p']) ?$_GET['p'] :'1';
        $data['p']                  = isset($data['p']) ?$data['p'] :'1';
        $wh  = ' 1 ';
        
        trace($data['sort']) ;
       
        //如果筛选参数存在
        if(count($param) > 0){
            foreach ($param as $k => $v ){
                //如果参数是键值对 
                if(is_string($k)){
                    
                    $data[$k]  = isset($data[$k])?trim($data[$k]) :'';
                    //如果是一参数对应多字段筛选查询
                    if(is_array($v['field'])){ 
                        if($data[$k]) {
                            $wh .= ' and (';
                            $msg = ''; //设置 or 关联  且过滤 第一个参数   or 存在 
                            foreach ($v['field'] as $key => $val){ 
                                if($v['type'] == 'eq' ) $wh .= " $msg $val = '$data[$k]' ";
                                else  $wh .= " $msg $val like ('%$data[$k]%') "; 
                                $msg = ' or ';
                            }
                            $wh .= ')';
                        }
                    }else{ 
                        if($data[$k]) {
                             if($v['type'] == 'eq' ) $wh .= " and $v[field] = '$data[$k]' ";
                             else $wh .= " and $v[field] like ('%$data[$k]%') ";
                        }
                    }
                }else{
                  
                    //如果参数只是值
//                  $data[$v]     = isset($data[$v])?trim($data[$v]) :'';
                  
                    if( $data[$v] !== null ) {
                    	 
                        if($v['type'] == 'eq' ) $wh .= " and $v = '$data[$v]' ";
                        else $wh .= " and $v like ('%$data[$v]%') ";
                    }
                    
                }
            }
        }
      
        $count                      = D($table)->where($wh) ->count();
        $p                          = new AdminPage($count, $data['pagesize'] ,$data);
        $page                       = $p->show();
        
        if($relation){
            
            $list                   = D($table)
                                    -> relation(true)
                                    -> where($wh)
                                    -> page($_GET['p'], $data['pagesize'])
                                    -> order($data['order'].' '.$data['sort'])
                                    -> field($field)
                                    -> select();
        }else{
                
            $list                   = D($table)
                                    -> where($wh)
                                    -> page($_GET['p'], $data['pagesize'])
                                    -> order($data['order'].' '.$data['sort'])
                                    -> field($field) 
                                    -> select();
         }
        
       //数据返回方式 
       if($return == 1){ 
           return ['page'=>$page,'data'=>$data,'list'=>$list] ; 
       } else{
           $this                     -> assign('page' ,$page );
           $this                     -> assign('data' ,$data );
           $this                     -> assign('list' ,$list );
       }

    }
    

//     处理 base64 图片上传保存
    public function doBase64Image(){
        
        $base_img = I('imageDate');
        $arr =  explode(';base64,', $base_img) ;
        $base_img = $arr[1];
        $type =  str_replace('data:image/', '', $arr[0]);
        switch ($type){
            case 'png':
                $fix = '.png';
                break;
            case 'gif':
                $fix = '.gif';
                break;
            default:
                $fix = '.jpg';
                
        }
        
        //  设置文件路径和文件前缀名称
        $path = "./uploads/images/".fDate(NOW_TIME,'Ymd').'/' ;
        if(!is_dir($path)) mkdir($path);
        $prefix='nx_';
        $output_file = $prefix.time().rand(100,999).$fix;
        $path = $path.$output_file;
        //  创建将数据流文件写入我们创建的文件内容中
        $ifp = fopen( $path, "wb" );
        fwrite( $ifp, base64_decode( $base_img) );
        fclose( $ifp );
        
        $this->ajaxReturn(['url'=>substr($path, 1),'picNumber'=> I('picNumber')]) ;
        
    }
    public function upBase64Image(){
        $this->display();
    }
    
}