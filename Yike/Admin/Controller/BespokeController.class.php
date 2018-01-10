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
class BespokeController extends BaseController {
    public function _empty(){

      $this->redirect('listSecondhouses');
    }

    //预约列表
    public function listBespoke(){ 
    	$user = M('users') ->where(['type' => 1]  ) ->field('user_id,name,nickname') ->select() ;
        $this ->assign('user',$user);
        self::CommonGetList(0,'Usersbuildings','ub_id',['user_id','agree'],$field='*',1); 
    	$this->display();	
    } 
    
//  预约详情
  	public function editBespoke(){
  	    
  	    if(IS_POST){ 
  	        $res = self::doBespokeInfo(); 
  	        if($res)     $this -> success( '操作成功' , U('listBespoke') );
  	        else         $this -> error( '操作失败' ); 
  	        die;
  	    }
  	    
  	    $ub_id   = I('ub_id')+ 0;
  	    $id      = I('id')+0 ;
  	    $note    = I('note') ;
  	    
  	    if($ub_id > 0){
  	        $row = D('Usersbuildings')->relation(true)->find($ub_id);
  	        $building = D('Houses')->relation(true)->find($row['build_id']);
  	    }elseif($id>0){
  	        $row = D('users_buildings')->find($ub_id);
  	        $building = D('Houses')->relation(true)->find($id);
  	    }else{
  	        $this->error('参数有误') ;
  	    }

  	    $this->assign('header',  '报备看房预约');
  	    $this->assign('building',$building);
  	    $this->assign('row',     $row);
  	    $this->assign('type',    $_SESSION['user']['type']);
  	    
  	    $this -> display();
  	    }
  	    
  	    /**
  	     * 预约数据处理
  	     * 
  	     */
  	    protected function doBespokeInfo(){
  	    
  	        $data                = I('post.');     
  	        $model               = D('Usersbuildings') ;
  	        $data['seetime']     =  strtotime( $data['see_time'].':00:00') ;
  	    
  	        if( $data['note'] ) $data['agree']  = 1;
  	    
  	        if( !$model ->create($data) ){
  	            $this->error($model->getError()) ;
  	            die ;
  	        }
			//预约房源标题
			$title = M('Houses') -> where( ['house_id' => $data['build_id'] ])->getField('');
  	         
  	        unset($data['__hash__']) ;
  	        if($data['ub_id']){  	          
  	            
  	            $model->save();//修改
  	            
                //蚁客 -> 开发商   发送信息
                $uid = $data['builduser_id'] ;
                $openID = M('Users')-> where( [ 'user_id'=>$uid ] ) -> getField('wechat');                 
                $content = '您有一条关于于 '.$title.' 的预约信息已有回复，请注意超查收';
                WxSendMessage($openID , $content );
  	           
  	            //开发商 -> 蚁客  发送信息
                $uid = $data['user_id'] ;  	           
  	            $openID = M('Users')-> where( [ 'user_id'=>$uid ] ) -> getField('wechat');  	            
  	            $content = '您有一条关于于 '.$title.' 的预约信息已有回复，请注意超查收';
  	            WxSendMessage($openID , $content );
  	            
  	            return $data['ub_id'];
  	        }  	    
  	}
    
    // 预约信息删除
    public function delBespoke(){ 
        $id = I('id')+0; 
        if( D('Usersbuildings')->delete($id) ) $this -> success( '操作删除成功' );
        else     $this -> error( '操作删除失败' );
    }
    
//  判断是否有新的预约信息
	public function checkNewBespoke(){
		$prompt  = D('Usersbuildings')->where(['prompt'=> 0]) ->count() ;
		$user  = D('Users')->where(['prompt'=> 0]) ->count() ;
		$res['type'] = null ;
		$res['number'] = 0 ;
		if($prompt > 0 ){
		    $res['type'] = 'pormpt'; 
		    $res['number'] = $prompt ;
			D('Usersbuildings')->where(['prompt'=> 0]) ->save(['prompt'=> 1 ]) ;
		}
		if($user > 0 ){
		    $res['type'] = 'user';
		    $res['number'] = $user ;
		    D('Users')->where(['prompt'=> 0]) ->save(['prompt'=> 1 ]) ;
		}
		
		$this ->ajaxReturn($res);
	}
	
	
    
}