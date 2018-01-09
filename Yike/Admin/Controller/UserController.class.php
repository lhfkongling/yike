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
class UserController extends BaseController {
    public function _empty(){

      $this->redirect('userList');
    }
    //验证码
    public  function Code($id = ""){
        $Verify = new \Think\Verify();
        $Verify->fontSize = 14;
        $Verify->length   = 4;
        $Verify->useNoise = false;
        $Verify->codeSet = '0123456789';
        $Verify->imageW = 90;
        $Verify->imageH = 25;
        $Verify->useCurve = false;
        $Verify->entry();
    }
    
    //会员列表
    public function userList(){
        
        $model = M('Users as u');
     
        
        $data = I('get.');
        
        if(IS_POST){
            $data = I('post.');
            $_GET['p']           ='1';
        }
        $data['type']               = $data['type'];
        $data['name']               = $data['name'] ;
    
        $data['order']              = isset($data['order'])?$data['order']:'user_id';
        $data['sort']               = isset($data['sort']) ?$data['sort'] :'desc';
        $data['pagesize']           = isset($data['pagesize'])?filterInt($data['pagesize']):C('PAGESIZE');
        $_GET['p']                  = isset($_GET['p']) ?$_GET['p'] :'1';
        $data['p']                  = isset($data['p']) ?$data['p'] :'1';
        $wh  = ' 1 ';
        if($data['name'] != '')      $wh .= "and u.name like ('%$data[name]%')";
        if($data['type'] != '')      $wh .= "and u.type = '".$data['type']."'";
        if($data['authentication'] != '')      $wh .= "and u.authentication = '".$data['authentication']."'";
      
      
        
        $count                       =  $model ->where($wh) ->count();
        $p                          = new AdminPage($count, $data['pagesize'] ,$data);
        $page                       = $p->show();
        $list                       = $model
                                    ->join(C('DB_PREFIX').'users as g on g.user_id = u.parent_id','left')
                                    -> where($wh)
                                    -> page($_GET['p'], $data['pagesize'])
                                    -> order('u.'.$data['order'].' '.$data['sort']) 
                                    -> field('u.*,g.name as parent_name ')
                                    -> select();
        
     
        $groups                     =  M('Group')  -> order('id asc')->select();
        $this                       -> assign('groups',$groups);
        $this                       -> assign('page' ,$page );
        $this                       -> assign('data' ,$data );
        $this                       -> assign('list' ,$list );
        
//         dump($data); 
//         die;
        $this->display();
       
    }
    
    /**
     * 批量操作 
     * LHF 
     * */
    public function batchUser(){
        $data = I('post.') ;
        $M_user = M('Users') ;
        
        if(!$data['ids'])
            $this ->ajaxReturn(['error'=>1,'info'=>'请至少选择一个选项']);
        
        if($data['authentication'] == 2)
            $list = $M_user ->where("user_id in ('$data[ids]')  ")->Field('authentication,wechat')->select();
        
        if($data['type'])
            $res = $M_user ->where("user_id in ($data[ids])  ")->save(['type' => $data['type']]) ;
        
        if($data['authentication'])
            $res = $M_user ->where("user_id in ($data[ids])  ")->save(['authentication' => $data['authentication']]) ;
        
        if($res){
            foreach ($list as $row){
    
                if($row['authentication'] != 2 && $data['authentication'] == 2)
                    WxSendMessage($row['wechat'],"您的申请信息已认证，恭喜成为蚁客。" );
                
            }
            $this ->ajaxReturn(['error'=>0,'info'=>'批量操作成功']);
            
        }else
            $this ->ajaxReturn(['error'=>1,'info'=>'批量操作失败' ,'sql'=>$M_user->getLastSql(),'res'=> $res] );
        
    }
    
    //会员编辑（添加、修改）
    public function userEdit(){

        if(IS_POST){
            self::doUserEdit();
            die;

            die;
        }
        
        $id                         = I('get.id',0,'intval');
        $row                        =  M('Users as u')->where("u.user_id = '$id'") -> find();
        $groups                     =  M('Group')  -> order('id asc')->select();
        $this                       -> assign('groups',$groups);
        $parent                     =  M('Users') ->where("user_id != '$id' and type='1'") -> order('user_id asc')->select();
        $this                       -> assign('parent',$parent);
        $this                       -> assign('row',$row);
        $this                       -> display();
//        pr($row);
    }
    //处理会员提交过来的信息
    protected function doUserEdit(){
        $data = I('post.');

        $M_user = M('Users') ;
        
        $row = $M_user ->where(['user_id'=>$data['user_id']])->Field('authentication,wechat')->find();

        $res = $M_user -> save($data);
        
        if($res){
            
            if($row['authentication'] != 2 && $data['authentication'] == 2){
                WxSendMessage($row['wechat'],"您的申请信息已认证，恭喜成为蚁客。" );
            }
            
            $this -> success( '操作成功' , U('userList') );
            
        }else{
            $this -> error( '操作失败' );
        }
               
        
    }
    //会员删除
    public function usersDelete(){
        
        $id = I('id')+ 0;
        
        $row = M('Users')->find($id);
        
        if($row['user_id']){
            if(M('Users') ->delete($id)){
                @unlink('./'.$row['face_url']);
                @unlink('./'.$row['work_url']);
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
            
        }else
            $this->error('该用户不存在');
        
    }
    //检查会员字段是否重复
    public function checkRepeatField($Field='',$val='',$id=''){
        if($Field == ''){
            if(!IS_AJAX) $this->redirect("Index/index");
            $id = I('post.id',0,'intval');       
            $val = I('post.val','','trim');       
            $Field = I('post.field','','trim');
        }
            
        $data['error']= M("Users")->where("user_id != $id and $Field = '$val'  ")->count();
        $data['field'] = $Field;
        if($Field == '')
        $this->ajaxReturn($data) ;
        else return $data;
    }
    //分组列表
    function groupList(){
      $model    =  M('Group') ;
      $list     =  $model -> order('id asc')->select();
      $this     -> assign('list',$list);
      $this     -> display();
      
      
      //查询跟新数据
      $res      =  getWechatObject() ->getGroup() ;
      $groupIDs = [];
      $servers  = [];
      foreach ($list as $v){
          $groupIDs[] = $v['id']; 
      }
      //添加遗漏数据
      foreach ($res['groups'] as $v ){
          $servers[] =  $v['id'];  
          if(!in_array($v['id'], $groupIDs))
              $model -> add( $v );
      }
      //删除多余数据
      foreach ($list as $v){
           if(!in_array($v['id'], $servers))
              $model -> delete( $v['id'] );
      }
      
    }
    //分组编辑
    function   groupEdit(){
        
      $id = I('id');
     
      if(IS_POST)
      {
          $data = I('post.');
          if(empty($data['id']))
          {
            $res = getWechatObject() ->createGroup($data['name']) ;
            if($res['group']['id'])
            {
                M('Group')->add($res['group']);
                $this->success('添加/修改成功。',['url'=>U('groupList'),'name'=>'返回分组列表']);
            }
            else
                $this->error('修改失败。');
            
          }
          else
          {
              $res = getWechatObject() ->updateGroup($data['id'],$data['name']) ;
              if($res['errmsg'] == 'ok')
              {
                  M('Group')->save($data); 
                  $this->success('添加/修改成功。',['url'=>U('groupList'),'name'=>'返回分组列表']);
              } 
              else 
                  $this->error('修改失败。');
          }
         
          die;
      }
      if(!empty($id)){
          $row = M('Group')->find($id);
          $this->assign('row',$row);
      }
      $this->display();
    }
    
    
    /**
     * 测试
     * 
     * */

}