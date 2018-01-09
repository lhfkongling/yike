<?php
// +----------------------------------------------------------------------
// | goods 预约 会员 与 新房源 关系 模型验证
// +----------------------------------------------------------------------
// | 参考网站网址：
// +----------------------------------------------------------------------
// | Author: 李洪发 2017-03
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model\RelationModel;
/**
* 站点Logic
*/
class UsersbuildingsModel extends RelationModel {
    
    protected $tableName        =   'Users_buildings';
//     protected $patchValidate = true;
    //自动验证规则
    protected $_validate = array(
        
        array('build_id','require','新房楼盘没有关联成功。'), 
        array('user_id','require','用户还没有登录。'),
        array('name','require','客户姓名不能为空。'),
        array('telephone','require','电话不能为空。'),
        array('see_time','require','看房时间不能为空。'),
        array('number','require','随行人数不能为空。'),
    );
    //关联模型关联信息
    protected $_link=array(
        'houses'=>[
            'mapping_type'          => self::BELONGS_TO,
            'class_name'            => 'houses',
            'mapping_name'          => 'houses',
            'foreign_key'           => 'build_id',
            'as_fields'        => 'name:build_name,address,user_id:u_id',
        ],
       
        'yk'=>[
            'mapping_type'          => self::BELONGS_TO,
            'class_name'            => 'users',
            'mapping_name'          => 'yk',
            'foreign_key'           => 'user_id',
            'as_fields'             => 'name:userYk,nickname:nickYk,uuid',
        ],
        'build'=>[
            'mapping_type'          => self::BELONGS_TO,
            'class_name'            => 'users',
            'mapping_name'          => 'build',
            'foreign_key'           => 'builduser_id',
            'as_fields'             => 'name:userBuild,nickname:nickBuild',
        ],
        
        
    );
   
}