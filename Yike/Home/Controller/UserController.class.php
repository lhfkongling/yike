<?php
namespace Home\Controller;
use Think\Controller;
use Home\Controller\BaseController;
class UserController extends BaseController {

    public function _empty()
    {
        $this->redirect('index') ;
    }

    public function index(){
       
        $row = M('Users') ->where([ 'user_id'=>$_SESSION['user']['user_id'] ])->find();
       
        $childCount = M('Users') ->where([ 'parent_id'=>$_SESSION['user']['user_id'] ])->count();
        
       $this->assign('header','我的');
        
       $user_id = $_SESSION['user']['user_id'] ;
      
       $user_banner = get_myad('user_banner');
       
       $note = getOneConfig('note');
       
       $this->assign('user_banner',$user_banner);
       $this->assign('note',$note);
       $this->assign('user',$row);
       $this->assign('childCount',$childCount);
//         pr($user_banner,1);
       $this->display();
    
	}
	
	/**
	 * 退出登录
	 * */
	public function logout(){
	    
	    session('user',NULL) ;
	  
	    $this ->redirect('index') ;
	}
	
	
    /**
     * 登录微信
     *
     * */
    public function login()
    { 
        if(getUserInfo()) $this->redirect('index');
        
        else $this->redirect('login'); 
    } 
    
    //子会员列表 
    public function children(){
        
       $this->assign('header','我的子会员');
       
       $this -> display();
        
    }
    
    public function ajaxChildrenList(){
        
        $data = I('post.');
      
        $wh = [
            'parent_id'=> $_SESSION['user']['user_id'] ,
        ];
     
        
        $data['setUrl'] = U('childrenInfo',['user_id'=>'00']);
        
        $data['count']  =  D('Users')
        -> where($wh)
        -> count();
        
        $data['countPage'] = ceil( $data['count'] /C('PAGE_SIZE') );
        
        $data['list']   =  D('Users')
//         ->relation(true)
        -> where($wh)
        -> page($data['page'],C('PAGE_SIZE'))
        -> order('user_id desc ')
        -> select();
        
        $data['sql'] = D('Users')->getLastSql();
        $data['page'] ++ ;
        $this->ajaxReturn($data);
        
        
    }
    
   
    
    /**
     * 账户信息修改
     * */
    public function account(){
        if(IS_POST){
            self::doAccount();
            die;
        }
        
        $row = M('Users as u') ->join(C('DB_PREFIX').'users as p on u.parent_id = p.user_id','left')->field('u.*,p.name as parent')->where('u.user_id="'.$_SESSION['user']['user_id'].'"') ->find(  );
        $this->assign('row',$row);
        $this->assign('header','我的信息');
        $this -> display();
//         pr($row);
    }
    protected function doAccount(){
        
        $data = I('post.');
        
        $data['authentication'] = $data['authentication'] == 0 ? 1 :  $data['authentication']  ;
        
        $res = M('Users') ->save($data) ;
        
        session('user',$data);
        
//         pr($data,1);
        if($res)
            $this -> success( '操作成功' , U('index') );
        else
            $this -> error( '操作失败' );
        
        die;
    }
    /**
     * 工作证件
     * */
    public function certificate(){
        
        $user_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user']['user_id'] ;
        
        $row = M('Users as u') ->field('u.*')->where('u.user_id="'.$user_id.'"') ->find();
        
        $this->assign('row',$row);
        $this->assign('header','工作证件');
        $this -> display();
//         pr($row);
    }
    
    /**
     * 预约列表
     * */
    public function bespoke(){
        $this->assign('header','预约列表 ');
        
        $this -> display();
    }
    public function ajaxBespokeList(){
        
        $data = I('post.');
        if($_SESSION['user']['type'] == '1')
        {
            $data['type'] = 1 ;
            $wh = [
                'user_id'=> !empty($data['userID']) ? $data['userID']: $_SESSION['user']['user_id'] ,
            ];
        }else{
            $data['type'] = 2 ;
           
            $wh = [
                'builduser_id'=> isset($_SESSION['user']['user_id'])?$_SESSION['user']['user_id']:2 ,
            ];
        }
        $data['setUrl'] = U('bespokeInfo',['ub_id'=>'00','note'=>1]);
        
        $data['count']  =  D('Usersbuildings')
        -> where($wh)
        -> count();
      
        $data['countPage'] = ceil( $data['count'] /C('PAGE_SIZE') );
        
        $data['list']   =  D('Usersbuildings')
        ->relation(true)
        -> where($wh)
        -> page($data['page'],C('PAGE_SIZE'))
        -> order('seetime desc ')
        -> select();
        
        $data['sql'] = D('Usersbuildings')->getLastSql();
        $data['page'] ++ ;
        
        $data['user'] = session('user');
        
        $this->ajaxReturn($data);
    }
    /**
     * 预约信息
     * */
    public function bespokeInfo(){
        
        
        if(IS_POST){
            $res = self::doBespokeInfo();
//             die($res);
            if($res)
                $this -> success( '操作成功' , U('bespoke') );
            else
                $this -> error( '操作失败' );
            
            die;
        }
        
        /**
         * 分析：
         * 
         * 蚁客添加预约信息的时候  $ub_id = 0 （预约ID ）   $id > 0 (新房源ID) 
         * $row['user_name'] = session('user.name') ；
         * $row['user_id'] = session('user.user_id') ；
         * $row['uuid'] = session('user.uuid') ；
         * 根据 $id  查询 出 预约房源的信息 显示出来 
         * 
         * 蚁客 / 开发商  修改 预约信息的时候  $ub_id  > 0 
         * 根据 $ub_id  查询 出 预约信息 显示出来 
         * 根据 $row['build_id']  查询 出 预约房源的信息 显示出来 
         * 
         * 根据 $_SESSION['user']['type'] 会员角色 判断 是否 有 修改 备注的权限 
         * （开发商有 ，蚁客没有)
         * 
         * */
        
        $ub_id = I('ub_id') + 0;
        $id = I('id')+0 ;
        $note = I('note') ;
        
        if($ub_id > 0){
            $row = D('Usersbuildings')->relation(true)->find($ub_id);
            $building = D('Houses')->relation(true)->find($row['build_id']);
        }elseif($id>0){
            $row = [
                'userYk' =>session('user.name'),
                'user_id' =>session('user.user_id'),
                'uuid' =>session('user.uuid'),
            ];
            $building = D('Houses')->relation(true)->find($id);
        }else{
            $this->error('参数有误') ;
        }
        
        $this->assign('header','报备看房预约');
        $this->assign('building',$building);
        $this->assign('row',$row);
        $this->assign('type',$_SESSION['user']['type']);
       
        $this -> display();
    }

    protected function doBespokeInfo(){
    
        $data = I('post.');
        
        $model = D('Usersbuildings') ;
        
        $data['seetime'] =  strtotime( $data['see_time'].':00:00') ;
        
		//预约房源标题
		$title = M('Houses') -> where( ['house_id' => $data['build_id'] ])->getField('name');
		
//         pr($data,1) ;
//         if( $data['note'] )
//             $data['agree']  = 1;
        
        if( !$model ->create($data) ){
            $this->error($model->getError()) ;
            die ;
        }
         
        unset($data['__hash__']) ;
        
        
//         pr($data ,1);

        if( empty($data['ub_id'] ) )
        {
           //添加预约信息 ，给地产商 客服信息提示  您有一条预约信息，请注意超查收
           
            $openID = M('Users')-> where(['user_id'=>$data['builduser_id']]) -> getField('wechat');
            $content = '您有一条关于 '.$title.' 的预约信息，请注意超查收';
            WxSendMessage($openID , $content );
            
            $model->add_time = NOW_TIME;
            return $model->add();//新增
        }
        else
        {
            //修改预约信息， 蚁客/地产伤  修改信息 回复信息 
            $content = '您有一条关 '.$title.' 的预约信息已有回复，请注意超查收';
            
            if($_SESSION['user']['type'] == 1){
                //蚁客 -> 开发商   发送信息
                $uid = $data['builduser_id'] ;
            }else{
                $agree = $model ->where(['ub_id' =>$data['ub_id']]) ->getField('agree') ;
                
                if($data['agree'] == 1 && $agree < 1 )
                    $content = "您有一条关于 ".$title." 的预约信息已成功，请注意查收，祝您带客成功  /::) ";
                
                //开发商 -> 蚁客  发送信息
                $uid = $data['user_id'] ;
            }
            $openID = M('Users')-> where( [ 'user_id'=>$uid ] ) -> getField('wechat');
            
          
            WxSendMessage($openID , $content );
            
            $model->save();//修改
            
            return $data['ub_id'];
        }
        
    }
    
    /**
     * 我的发布
     * */
    public function release(){
        
        $_SESSION['user']['authentication'] =  M('Users')->where(['user_id'=>session('user.user_id')]) -> getField('authentication');
        
        if(session('user.authentication') != 2)
            $this->error('对不起，你的会员还没有通过管理员审核。');
        
      if(session('user.type') == 1){
            $this->assign('header','二手房');
            $tag = 2; 
        }else{
            $this->assign('header','新楼盘');
            $tag = 1 ;
        }
        $this->assign('tag',$tag);
        $this->assign('id','secondList');
        $this->assign('showUrl',U('editFouses',['id'=>'00','tag'=> $tag ]));
        $this->assign('ajaxListPage',U('relraseFouses'));
        
        $this -> display();
    }
    
    public function editFouses(){
       
      $tag = I('tag');
        if(IS_POST)
        {
            $res = logic('domysql') -> doEditHouses();
            if($res['error'] == 0)
                $this -> success( '操作成功' , U('release',['tag'=>$tag]) );
            else
                $this -> error( $res['data'] );
            
            die;
        }
        
        $house_id = I('id',0,'intval') + 0;
        $model = D('Houses');
        

        $_SESSION['user']['type'] =  M('Users')->where(['user_id'=>session('user.user_id')]) -> getField('type');
        
        if(session('user.type') == 1){
            $this->assign('header','二手房');
            $tag = 2; 
        }else{
            $this->assign('header','新楼盘');
            $tag = 1 ;
        }
        
        if($house_id > 0)
            $row = $model->relation(true)->where(['house_id'=>$house_id])->find();
    
        $up_pic_num = M('Config')->where(['field'=>'up_pic_num'])->getField('value');
    
        $pic_num = $up_pic_num > count($row['picture']) ? $up_pic_num - count($row['picture']) : 0  ;

         $m = new  \Home\Controller\HousesController ;       
        $m -> assignAttr();

        $row['tag'] = isset($row['tag'])?$row['tag']:$tag;
        $row['linkman'] = explode(',', $row['linkman']);
        $row['telephone'] = explode(',', $row['telephone']);
        
        if(!C('CONFIG'))
        {
            $list = M('Config')->select();
            foreach ($list as $v)
            {
                $row[$v['field']] = $v['value'] ;
            }
            C('CONFIG',$row) ;
        }
        
        $this -> assign('img_size',explode(',',C('CONFIG.img_size'))) ;
        
        $this       ->assign('tag',$tag);
        $this       ->assign('row',$row);
        $this       ->assign('pic_num',$pic_num);
//         pr($row,1);
        $this       -> display();
      
        
    }
    
    /**
     * 异步查询房源信息
     * 
     * */
    public function relraseFouses(){
      
        $data = I('post.');
        $wh = [
           'user_id'=>session('user.user_id'),
           'tag'=>$data['tag'],
        ];
        
        $data['count']  =  D('Houses')
        -> where($wh)
        -> count();
        
        $data['countPage'] = ceil( $data['count'] /C('PAGE_SIZE') );
        $data['user_id'] = session('user.user_id') ;
        
        $data['list']   =  D('Houses')
        ->relation(true)
        -> where($wh)
        -> page($data['page'],C('PAGE_SIZE'))
        -> order('house_id desc ')
        -> select();
        
        $data['sql'] =  D('Houses') ->getLastSql();
        $data['page'] ++ ;
        $this->ajaxReturn($data);
        
    }

    //删除属性
    public function deleteAttr(){
    
        $data = I('post.');
    
        if(M($data['table'])->delete($data['id']))
            $this -> ajaxReturn(1) ;
        else
            $this -> ajaxReturn(0) ;
         
    }
    

 
    //上传二手房图片
    public  function updateSecondPic(){ 
       $data =  logic('Domysql') -> updateSecondPic();         
       $this->ajaxReturn($data) ;
    }
    
    //删除房源图片
    public function imagesDelete (){        
       $data = logic('Domysql')  -> imagesDelete();
       $this->ajaxReturn($data) ;
    }
    
}