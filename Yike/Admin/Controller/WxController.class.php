<?php
/**
 * 视频信息
 * author：LHF
 * time：2017-3-30
 * */
namespace Admin\Controller;
use Think\Controller;

class WxController extends Controller {
    
    public function _empty(){
        $this->redirect('index');
    }
    
    public function index(){

         $this->display();
    }
    

    //验证是否来自于微信 + 微信自动回复
    public  function checkWeixin()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

         
        $data = I('get.');
    
        foreach ($data as $k =>$v ){
    
//             \Think\Log::write($k . ' -> ' .$v) ;
        }
    
        checkWechat();
    
        
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        //extract post data
        
//         微信自动回复
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim( $postObj->Content );
            $time = time();
            $ev = $postObj->Event;
            
            $msgType = "text";
            $textTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[%s]]></MsgType>
    <Content><![CDATA[%s]]></Content>
    <FuncFlag>0</FuncFlag>
    </xml>";
            
            if ($ev == "subscribe"){
                
                $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[%s]]></MsgType>
<ArticleCount>1</ArticleCount>
<Articles>
<item>
<Title><![CDATA[%s]]></Title>
<Description><![CDATA[%s]]></Description>
<PicUrl><![CDATA[%s]]></PicUrl>
<Url><![CDATA[%s]]></Url>
</item>
</Articles>
</xml>";

                
                $msgType = "news";
                
                $row = $this->getForeverMedia();
                $title =$row['news_item'][0]['title'];
                $description = $row['news_item'][0]['digest'];
                $picUrl = $row['news_item'][0]['thumb_url'];
                $url = $row['news_item'][0]['url'];

                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $title,$description,$picUrl,$url);
                
//                 \Think\Log::write(  'subscribe  ----> ' . $resultStr) ;
                echo $resultStr;

                exit ; 
            }
//             \Think\Log::write(  ' $ev  ----> ' . $ev) ;
            
            //查询自动回复内容
            $content = M('autoreply') -> where(['key'=>$keyword]) ->field('id,value') -> find() ;
            
//             \Think\Log::write(  ' sql  ----> ' .M('autoreply') ->getLastSql()) ;
            
//             \Think\Log::write(  ' value  ----> ' .$content['value']) ;
            
            if(empty($content['value']) && !empty($keyword) )
            {
                //查询不在关键字内的咨询  并保存数据 ，以备客服回复
                $content = M('autoreply') ->where(['id'=>1]) ->find('id,value');
                $sql = "INSERT INTO `mz_usermessage` (`fromUsername`,`toUsername`,`add_time`,`key`) VALUES ('$fromUsername','$toUsername','".NOW_TIME."','$keyword')";

               M() -> execute($sql) ;
                

            }

            if($content['value']){              
                M('Autoreply') ->where(['id'=>$content['id']]) ->setInc('count');
                
//                 \Think\Log::write(  ' value  ----> ' .$content['value']) ;
                
                //$contentStr = @iconv('UTF-8','gb2312',$keyword);
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $content['value']);
                echo $resultStr;
            }else {
                echo "";
                exit;
            }

        }else {
            echo "";
            exit;
        }
    }

    function keyrep($key)
    {
        //return $key;
        if( $key=='嗨' || $key=='在吗' || $key=='你好' ){
            $mt = mt_rand(1,17);
            $array = array(
                1=>'自杀中，稍后再说...',
                2=>'有事找我请大叫！',
                3=>'我正在裸奔，已奔出服务区',
                4=>'我现在位置：WC； 姿势：下蹲； 脸部：抽搐； 状态：用力中。。。。',
                5=>'去吃饭了，如果你是帅哥，请一会联系我，如果你是美女...............就算你是美女，我也要先吃饱肚子啊',
                6=>'洗澡中~谢绝旁观！！^_^0',
                7=>'有熊出?]，我去诱捕，尽快回来。',
                8=>'你好，我是500，请问你是250吗？',
                9=>'喂！乱码啊，再发',
                10=>'不是我不理你，只是时间难以抗拒！',
                11=>'你刚才说什么，我没看清楚，请再说一遍！',
                12=>'发多几次啊~~~发多几次我就回你。',
                13=>'此人已死，有事烧纸！',
                14=>'乖，不急哦…',
                15=>'你好.我去杀几个人,很快回来.',
                16=>'本人已成仙?有事请发烟?佛说有烟没火成不了正果?有火没烟成不了仙。',
                17=>'你要和我说话？你真的要和我说话？你确定自己想说吗？你一定非说不可吗？那你说吧，这是自动回复，反正我看不见其实我在~就是不回你拿我怎么着？'
            );
            return $array[$mt];
        }
        if( $key=='靠' || $key=='啊' || $key=='阿' )
        {
            $mt = mt_rand(1,19);
            $array = array(
                1=>'人之初?性本善?玩心眼?都滚蛋。',
                2=>'今后的路?我希望你能自己好好走下去?而我 坐车',
                3=>'笑话是什么?就是我现在对你说的话。',
                4=>'人人都说我丑?其实我只是美得不明显。',
                5=>'A;猪是怎么死的?B;你还没死我怎么知道',
                6=>'奥巴马已经干掉和他同姓的两个人?奥特曼你要小心了。 ',
                7=>'有的人活着?他已经死了?有的人活着?他早该死了。',
                8=>'"妹妹你坐船头?哥哥我岸上走"据说很傻逼的人看到都是唱出来的。',
                9=>'我这辈子只有两件事不会?这也不会?那也不会。',
                10=>'过了这个村?没了这个店?那是因为有分店。',
                11=>'我以为你只是个球?没想到?你真是个球。',
                12=>'你终于来啦，我找你N年了，去火星干什么了？我现在去冥王星，回头跟你说个事，别走开啊',
                13=>'你有权保持沉默，你所说的一切都将被作为存盘记录。你可以请代理服务器，如果请不起网络会为你分配一个。',
                14=>'本人正在被国际刑警组织全球范围内通缉，如果您有此人的消息，请拨打当地报警电话',
                15=>'洗澡中~谢绝旁观！！^_^0',
                16=>'嘀，这里是移动秘书， 美眉请再发一次，我就与你联系；姐姐请再发两次，我就与你联系；哥哥、弟弟就不要再发了，因为发了也不和你联系！',
                17=>'其实我在~就是不回你拿我怎么着？',
                18=>'你刚才说什么，我没看清楚，请再说一遍！',
                19=>'乖，不急。。。'
                
            );
            return $array[$mt];
        }
        if( $key =='请问' )
        {
            $mt = mt_rand(1,5);
            $array = array(
                1=>'"我脸油吗"反光？?反正我不清楚',
                2=>'走，我请你吃饭',
                3=>'此人已死，有事烧纸！',
                4=>'喂！什么啊！乱码啊，再发',
                5=>'笑话是什么？?就是我现在对你说的话。'
                
            );
            return $array[$mt];
        }
        return "";
    }
    function keylist()
    {
        $array = array(1=>'嗨',2=>'你好',3=>'靠',4=>'在吗',5=>'请问');
    }
    
    //查询自动回复 
    public function listAutoreply(){

        $list     =  M('Autoreply') -> order('id asc')->select();
        $this     -> assign('list',$list);
        
        $this -> assign('_act','TEST') ;
        
        $this ->display();
    }
    //编辑自动回复
    public function editAutoreply(){
        if(IS_POST) self::doEditAutoreply() ;
        
        $id = I('id')+0;
        
        $row = M('Autoreply')->find($id) ;
        
        $this     -> assign('row',$row);
        
        $this     -> display();
        }
        //处理分类编辑提交的数据
        protected  function  doEditAutoreply()
        {
            $data = I('post.');
            $d = D('Autoreply');
            if(!$d ->create($data))
            {
                $this->error($d -> getError());
            }
            else
            {
                if($data['id'] > 0)
                    $res = $d->save();
                else
                    $res = $d->add();
        
        
                if($res)
                    $this -> success( '操作成功' , U('listAutoreply') );
                else
                    $this -> error( '操作失败' );
        
            }
            die;
        }
    //删除自动回复
    public function delAutoreply(){
        $id = I('id')+0;
        
        if($id > 2)
        {
            if(M('Autoreply')->delete($id))
                $this -> success( '操作删除成功' );
            else
                $this -> error( '操作删除失败' );
        }
        else 
            $this -> error( '系统信息禁止删除' );
        
    }
    
    //会员咨询记录列表
    public function listUserMessage(){
    
        $base = new \Admin\Controller\BaseController ;
        $base::CommonGetList(0,'Usermessage','id',[],$field='*',0);
        $this     -> display();
        
    }
    //编辑会员咨询记录
    public function editUserMessage(){
            $id = I('id') + 0 ;
            if(IS_POST){
                $openID = I('post.fromusername') ;
                $content = I('post.value') ;
                $content = trim($content) ;
               
                $res = WxSendMessage ($openID,$content) ;
                if($res){
                    M('Usermessage') -> where(['id'=>$id]) ->save(['value' =>$content ]);
                    $this->success('信息回复成功',U('listUserMessage'));
                }else{
                    $this->error('信息回复失败');
                }
                die;
            }
        
           
            
            $row = M('Usermessage') ->find($id) ;
            if($row){
                $this -> assign('row',$row);
                $this -> display();
            }else{
                $this->error('信息不存在');
            }
           
    }
    //删除会员咨询记录
    public function delUserMessage(){
        $id = I('id') + 0 ;
        
        $res =  M('Usermessage') -> delete($id);
        if($res) $this->success('删除成功',U('Wx/listUserMessage')) ;
        else $this->error('删除失败' ) ;
         
    }
    
    
    
    
    //配置文件
    public function config(){
        $m = M('Config');
        if(IS_POST)
        {
            $data = I('post.');
             
            foreach ($data as $k=>$v)
            {
                $res =   $m ->where(['field'=>$k]) -> save(['value'=>$v]);
            }
        
            $this->success('修改成功。');die;
        }
        
        $config = $m -> where(['type'=>1])->select();
        foreach ($config as $v)
        {
            $row[$v['field']] = $v['value'] ;
        }
        
        $this->assign('row',$row);
        $this->display();
    }
    
//   自定义菜单列表
    public function menuList(){
        $list = self::getMenu(0);
        
        $this->assign('list',$list);
        
        $this->display();
        
    }
    //自定义菜单编辑
    public function menuEdit(){
    
        if(IS_POST){
            $data = I('post.');
            $m = M('menu');
            if($data['menu_id'] > 0){
               $res = $m ->where(['menu_id' => $data['menu_id'] ]) ->save($data);
            }else{
               $res = $m ->add($data);
            }
            
            if($res) $this->success('添加/修改成功',U('Wx/menuList')) ;
            else $this->error('添加/修改失败' ) ;
            
            die;
        }
        $id = I('id') + 0 ;
        $row = M('Menu')->where(['menu_id'=>$id]) ->find();
        $row['parent_id'] = isset($row['parent_id'])?$row['parent_id']: I('parent_id') ;
        $list = self::getMenu(0,'menu_id,name');
        $this->assign('list',$list);
        $this->assign('row',$row);
        $this->display();
       
    }
    // 删除自定义菜单
    public function menuDelete(){
        $id = I('id') + 0 ;
        $count =  M('menu') ->where(['parent_id' => $id ])  ->count();
        if($count > 0){
            $this->error('对不起，请先删除子菜单。' ) ;
            die;
        }
       $res =  M('menu') -> delete($id);
       if($res) $this->success('删除成功',U('Wx/menuList')) ;
       else $this->error('删除失败' ) ;
       
    }
    /* 
     * 创建自定义菜单
     * 发送到微信公众号服务器
     * 二级菜单
     */
     public function  createMenu(){
          $list = self::getMenu(0,'*');
         foreach ($list as $k =>$v)
         {
            $data[$k] = self::createMenuInfo($v);          
            $data[$k]['sub_button'] = [];
            foreach ($v['sub_button'] as $key =>$val){
                
                $data[$k]['sub_button'][$key]  = self::createMenuInfo($val);
                $data[$k]['sub_button'][$key]['sub_button'] = [];
            }
               
         }
         
         deleteMenu() ;
         $res = createMenu(['button'=>$data]);
         
        $this->ajaxReturn($res) ;
         
     }
     private function createMenuInfo($v){
         $data = [];
         if($v['name']) $data['name'] = $v['name'] ;
          
         switch ($v['type']){
             case 'click'://点击推事件
                 $data['type'] = $v['type'] ;
                 if( $v['url'] ) $data['key'] = $v['url'] ;
                 break;
             case 'view': //跳转URL
                 $data['type'] = $v['type'] ;
                 $data['url'] = $v['url'] ;
                 break;
             case 'scancode_push': // 扫码推事件
                 $data['type'] = $v['type'] ;
                 $data['key'] = $v['url'] ;
                 break;
             case 'scancode_waitmsg': //扫码推事件且弹出
                 $data['type'] = $v['type'] ;
                 $data['url'] = $v['url'] ;
                 break;
             case 'pic_sysphoto': //弹出系统拍照发图
                 $data['type'] = $v['type'] ;
                 $data['key'] = $v['url'] ;
                 break;
             case 'pic_photo_or_album': //弹出拍照或者相册发图
                 $data['type'] = $v['type'] ;
                 $data['key'] = $v['url'] ;
                 break;
                  
             case 'pic_weixin': //弹出微信相册发图器
                 $data['type'] = $v['type'] ;
                 $data['key'] = $v['url'] ;
                 break;
             case 'location_select': //弹出地理位置
                 $data['type'] = $v['type'] ;
                 if( $v['url'] ) $data['key'] = $v['url'] ;
                 break;

             case 'media_id':
                 $data['type'] = $v['type'] ;
                 $data['media_id'] = $v['url'] ;
                 break;
             case 'view_limited':
                 $data['type'] = $v['type'] ;
                 $data['media_id'] = $v['url'] ;
                 break;
         
             default:
         }
         return $data ;
     }
    
    //获取自定义菜单列表
    private function getMenu($parent_id,$field ='*'){
        $list =  M('menu') ->where(['parent_id' => $parent_id ]) ->field($field) ->select();
        if( count($list)>0 ) {
            foreach ($list as $k=>$v){
                $list[$k]['sub_button'] = self::getMenu($v['menu_id']);
                if(isset($v['parent_id'])){
                    $list[$k]['parent'] = self::getMenuNmae($v['parent_id']);
                }
            }
        }
        return $list ;
    }
    private function getMenuNmae($menu_id){
        if($menu_id == 0) return '顶级' ;
        else return  M('menu') ->where(['menu_id' => $menu_id ]) ->getField('name') ;
    }
    
    
    /*
     * 微信公众号 功能开发
     * 1 自定义菜单 
     * 2 会员信息
     * 3 会员分组
     * 4 设置关键词自动回复
     * 5 获取消息记录信息
     * 6 回复消息
     * 7 对全体会员推送消息，对部分会员（分组 ，或者指定某几个个）  推送消息 ，对单独个人推送消息
     */  
    
    
    public function getGroup(){
        pr('getGroup：');
       $list =  getWechatObject()  ->getGroup() ;
       
       pr($list) ;
       
    }
    
    public function getForeverList(){
    
       $list =  getWechatObject() -> getForeverList('news',0,20) ;
       
       pr($list);
    
    }
    
    public function getForeverMedia(){
       return  getWechatObject() -> getForeverMedia('gZDFxVtFRCCKtCPbh0uBab4Q7NFvu7JGVGqinsUuFcY',0) ;
         
//         pr($list);
    }
    public function message(){
        

        
        pr('sendMassMessage：');
        
        WxSendMessage('o678K1YpnRgoZthRJNrZ90gYVWxQ',"你的预约信息已经处理了，请注意查收。" ); 
        
//         sendCustomMessage
        die('sendMassMessage') ;
//         o678K1YpnRgoZthRJNrZ90gYVWxQ
        
        pr('信息推送：');
        
        //推送给所有人 
        
        //接收会员信息 
        $list = $wechat -> getRev() ;
        pr('getRev：');
        pr($list) ;
       
        
        $getRevFrom = $wechat -> getRevFrom() ;
        pr('getRevFrom：');
        pr($getRevFrom) ;
        
        
//         回复会员发送过来的信息
        
//         自动回复 
        
        
    }
}
