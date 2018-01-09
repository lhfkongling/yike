<?php
// +----------------------------------------------------------------------
// | TP-Admin [ 多功能后台管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2016 http://www.5bkk.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 逍遥·李志亮 <xiaoyao.working@gmail.com>
// +----------------------------------------------------------------------

namespace Logic;

use Lib\Log;
use Common\Exception\PHPMailer;
/**
* Logic 基类
* @author 李志亮 <lizhiliang@kankan.com>
*/
class BaseLogic {
    protected $errorCode = 0;
    protected $errorMessages = array('0' => '');
    protected $errorMessage = '';

    /**
     * 接口返回的错误信息
     * @var null
     */
    protected $serviceErrorInfo = null;

    public function getInterfaceData($epi_curl_manager) {
        $response = $epi_curl_manager->getResponse();
        $temp = false;
        if ($response['code'] == 200) {
            $response_data = json_decode($response['data'], true);
            if ($response_data['rtn'] === 0) {
                $temp = isset($response_data['data']) ? $response_data['data'] : '';
                if ($response['time'] > 0.2) {
                    // 记录慢查询接口
                    Log::notice("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
                }
            } else {
                // 记录接口返回错误数据
                $this->errorCode = $response_data['rtn'];
                $this->serviceErrorInfo = $response_data;
                Log::warn("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
                return false;
            }
        } else {
            // 记录接口请求错误
            Log::error("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
            return false;
        }
        return $temp;
    }

    public function getErrorMessage() {
        return empty($this->errorMessage) ? (isset($errorMessages[$this->errorCode]) ? $errorMessages[$this->errorCode] : '') : $this->errorMessage;
    }

    public function getErrorCode() {
        return $this->errorCode;
    }

    public function getServiceErrorInfo() {
        return empty($this->serviceErrorInfo) ? false : $this->serviceErrorInfo;
    }
	/**
	 * 获取表的一个字段信息
	 * $table 表名
	 * $where 查询条件
	 * $field 查询字段
	 * @return string
	 * */
	public function getOneField($table,$where,$field){
		return M($table)->where($where)->getField($field);
	
	}
	
	/**
	 * 发送邮件
	 * $address : 收件邮箱
	 * $title ： 标题
	 * $message ：内容
	 * **/
	public function SendMail($address,$title,$message)
	{
	
	   // import('Common.Exception.PHPMailer');
	
	    $mail=new PHPMailer();
	    // 设置PHPMailer使用SMTP服务器发送Email
	    $mail->IsSMTP();
	    // 设置邮件的字符编码，若不指定，则为'UTF-8'
	    $mail->CharSet='UTF-8';
	    //HTML模式
	    $mail->IsHTML(true);
	    // 添加收件人地址，可以多次使用来添加多个收件人
	   // $mail->AddAddress($address);
	    $address_arr = explode(',', $address);
	    if($address_arr){
	        foreach ($address_arr as $val){
	            $mail->AddAddress($val);
	        }
	    }
	    
	   $emailInfo =  M('Config')->where(['code'=>'email'])->getField('value');
	   $emailInfo = unserialize($emailInfo);
	
	    // 设置邮件正文
	    $mail->Body=$message;
	
	    //使用发送方式
	    $mail->Mailer='smtp';
	
	    // 设置邮件头的From字段。
	    $mail->From= $emailInfo['email']; //C('MAIL_ADDRESS');
	    // 设置发件人名字
	    $mail->FromName=$emailInfo['sendname']; 
	    // 设置邮件标题
	    $mail->Subject=$title;
	    // 设置SMTP服务器。
	    $mail->Host= $emailInfo['smtp']; //C('MAIL_SMTP');
	    // 设置为“需要验证”
	    $mail->SMTPAuth=true;
	    // 设置用户名和密码。
	    $mail->Username= $emailInfo['username'];  //C ('MAIL_LOGINNAME');
	    $mail->Password= $emailInfo['password']; // C ('MAIL_PASSWORD');
	    // 发送邮件。
	    return($mail->Send());
	}
	
	/**
	 * 模拟post进行url请求
	 * @param string $url
	 * @param string $param
	 */
	function request_post($url = '', $param = '') {
	    if (empty($url) || empty($param)) {
	        return false;
	    }
	    
	    \Think\Log::write('支付：'.$url ,'LHF');
	    
	    $postUrl = $url;
	    $curlPost = $param;
	    $ch = curl_init();//初始化curl
	    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
	    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
	    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
	    $data = curl_exec($ch);//运行curl
	    curl_close($ch);
	    \Think\Log::write('支付：'.$data ,'LHF');
	    return $data;
	}
	//短信发送
	function testAction($telphone,$text){
	    $url                       = C('SMS_URL');
	    $post_data['uid']          = C('SMS_UID');
	    $post_data['passwd']       = C('SMS_PASSWD');
	    $post_data['phonelist']    = $telphone; 
	    $post_data['content']      = $text.C('SMS_AUTOGRSPH') ;
	    $o = "";
	    foreach ( $post_data as $k => $v )
	    {
	        $o.= "$k=" . urlencode( $v ). "&" ;
	    }
	    $post_data = substr($o,0,-1);
	
	    $res = $this->request_post($url, $post_data);
	    return $res;
	    //print_r($res);
	
	}
	
}