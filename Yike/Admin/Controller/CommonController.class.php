<?php
/**
 * 后台默认公共模块
 * author：LHF
 * time：2017-3-30
 * */
namespace Admin\Controller;
use Think\Controller;
use Admin\Controller\BaseController;
class CommonController extends BaseController {
    //空方法跳转设置
    public function _empty(){ $this->redirect('Index/index');}
    
    
    //管理员登录
    public function login(){
        if(IS_POST){
            $code = I('post.code');
            //是否启用验证码验证
            if(M('Config')->where(['field'=>'admincode'])->getField('value') == '1'){
                $verify = new \Think\Verify();
    	        $chk_verify =  $verify->check($code , '');
    	        if(!$chk_verify) $this->error('你的验证码输入有误。');
            }
            
            $admin_name  = I('post.admin_name');
            $password  = I('post.password');
            $admin = M('admin') -> where(['admin_user'=>$admin_name]) -> find();
            
	        if( self::checkAdminPasswod($password, $admin['password'], $admin['salt']) ){
	            self::updateAdminInfo($admin['admin_id']);
	            session('admin',$admin);
	            $this->redirect('Index/index');
	        }else{
	            $this->error('你的用户名或者密码不正确。');
	        }
            die();
        }
        
        // 默认账户  mtadmin  madmin
        if(session('admin')['admin_id'] > 0 ){
            $this->redirect('Index/index');
        }
       
        $isShowCode = M('Config')->where(['field'=>'admincode'])->getField('value') ;
        $this->assign('isShowCode',$isShowCode) ;
        $this->display();
        
        
    }
    //更改管理员登录信息
    private function updateAdminInfo($adminID){
        $data['login_time'] = fDate($_SERVER['REQUEST_TIME']) ;
        $data['login_ip'] = get_client_ip() ;
        M('Admin')->where(['admin'=>$adminID]) ->save($data) ;
    }
    
    //检查登录密码
    private function checkAdminPasswod($pw1,$pw2,$salt){
       if(md5(md5($pw1).$salt) === $pw2 ) return true ;
       else return  false ;
    }
    
    //验证码
    public  function code($id = ""){
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
    //退出登录
    public function logout(){
        session('admin','');
        unset($_SESSION['admin']); 
        $this->redirect('login');
    }
    
    
    
    //系统配置
    public  function  config(){
        $m = M('Config');
        if(IS_POST)
        {
            $data = I('post.');
                 
            foreach ($data as $k=>$v)
            {
                $res =   $m ->where(['field'=>$k]) -> save(['value'=>$v]);
            }
            //系统配置文件修改后重新设置参数
            $list = M('Config')->select();
            foreach ($list as $v)
            {
                $row[$v['field']] = $v['value'] ;
            }
            C('CONFIG',$row) ;
            
            $this->success('修改成功。');die;
        }
    
        $list = $m->select();
        foreach ($list as $v)
        {
            $row[$v['field']] = $v['value'] ;
        }
        
        $this->assign('row',$row);
        $this->display();
    }
    
    //广告列表
    public function adList(){
    
        $this -> CommonGetList(0,'ad','ad_id',['ad_name'],'ad_id,ad_name,code,is_show,moduletype,position');
        $this->display();
    
    }
    //广告编辑
    public function adEdit(){
        if(IS_POST){
            $data = I('post.');
    
            $id = filterInt($data['id']);
            $parem['ad_name'] = filterStr($data['ad_name']);
            $parem['moduletype'] = filterInt($data['moduletype']);
            $parem['position'] = filterInt($data['position']);
            $parem['code'] = filterStr($data['code']);
            $parem['is_show']   = filterInt($data['is_show']);
            $parem['note'] = filterStr($data['note']);
            $count = count($data['imgs_url']);
    
            for($i = 0; $i<= $count;$i++){
                if($data['imgs_url'][$i]){
                    $parem['info'][$i]['img_url'] = filterStr($data['imgs_url'][$i]);
                    $parem['info'][$i]['type'] = filterInt($data['type'][$i]);
                    $parem['info'][$i]['param'] = filterStr($data['param'][$i]);
                    $parem['info'][$i]['name'] = filterStr($data['name'][$i]);
                }
            }
    
            //php检测数据合法性
            if(empty($parem['ad_name'])  ){
                $this->error('广告名称不能为空。');
            }
//             elseif(empty($parem['note']) ){
//                 $this->error('广告备注不能为空。');
//             }
            elseif( empty($parem['info']) ){
                $this->error('广告图片不能为空。');
            }
            
            
            $model = M('ad') ;
            if($model ->where("ad_name = '$parem[actor_name]' and ad_id != $id") -> count() ){
                $this->error('广告名称已经存在。');
            }
    
            //广告位JSON转字符串 ， 禁止  中文转义 、斜杠转义
            $parem['info'] = json_encode($parem['info'],JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
            if( $id > 0) {
               
                $res =  $model->where(['ad_id'=>$id])->save($parem);
            }
            else $res =  $model ->add($parem);
            if($res)
                $this->success('添加/修改成功。',U('adList') );
            else $this->error('添加/修改失败。');
            die;
        }
    
        $id = I('get.id',0,'intval');
        $row = M('Ad')->where(['ad_id'=>$id])->find();
        $row['info'] = json_decode($row['info']);
        foreach ( $row['info']  as $k => $v){
            $row['info'][$k] = (array)$v;
        }
        $row['notefCount'] = strlen($row['note']);
        $row['imgCount'] = count($row['info']);
        $this->assign('row',$row);
    
        $this->display();
    
    }
    //广告删除
    public function adDelete(){
        $id = I('get.id',0,'intval');
        $data = I('get.');
        //删除之前 保存S3 的文件路径
        $model = M('ad') ;
        $row =  $model->where(['ad_id'=>$id])->find();

        if($model->where("ad_id='$id'")->delete()){
            //删除的图片
            $row =  $model->where(['ad_id'=>$id])->find();
            $row['info'] =json_decode($row['info']) ;
//            foreach ($row['info'] as $v){wLog($v->img_url,'s3/'.date('Y-m-d',time()) .'.txt');}
            unset($row);
            $this->resuccess('删除成功。',[],['url'=>U('adList'),'name'=>'返回广告列表']);
        }
    
        else $this->error('删除失败。');
    
    }
    
    public  function updateField(){
        
        if(empty($_FILES)) die('upload failed');
        
        $suffix = strtolower($_POST['suffix']) ;
        $data = ['error'=>0, 'url'=>'',id=>$_POST['id']] ;
        if(in_array($suffix , ['png','jpg','jpeg','gif']))
        {
            
            $name = $_FILES['file']['name'] ;
            
            if( $_POST['rename'] == 1 )
            {
                $name = $suffix.rand(100000,900000).'_'.time().'.'.$suffix ;
            }
            
            move_uploaded_file ($_FILES['file']['tmp_name'],'./uploads/images/ad/'.$name);
            $data['url'] =  '/uploads/images/ad/'.$name ;
        }
        else $data['error'] = 1 ;
        
        //输出 接收的 POST 数据
        $this->ajaxReturn( $data ) ;
//         var_dump($name) ;
        
    }
    
    
}