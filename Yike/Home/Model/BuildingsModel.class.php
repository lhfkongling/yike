<?php
// +----------------------------------------------------------------------
// | goods 商品 模型验证
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
class BuildingsModel extends RelationModel {
    
//     protected $patchValidate = true;
    //自动验证规则
    protected $_validate = array(
        
        array('name','require','新房源名称不能为空。'),  
        array('name','','新房源名称已经存在！',1,'unique',3),
        
        array('area','require','面积不能为空。'),
        array('city','require','城市不能为空。'),
//         array('brief','require','简介不能为空。'),

    );
    //关联模型关联信息
    protected $_link=array(
        'buildings_picture'=>[
            'mapping_type'          => self::HAS_MANY ,
            'class_name'            => 'buildings_picture',
            'mapping_name'          => 'picture',
            'foreign_key'           => 'build_id',
        ],
        
        'house_type'=>[
            'mapping_type'          => self::HAS_MANY ,
            'class_name'            => 'house_type',
            'mapping_name'          => 'housetype',
            'foreign_key'           => 'build_id',
        ],
        
        'important'=>[
            'mapping_type'          => self::HAS_MANY ,
            'class_name'            => 'important',
            'mapping_name'          => 'important',
            'foreign_key'           => 'type_id',
            'condition'             => 'type=1',
        ],
        
        'secondary'=>[
            'mapping_type'          => self::HAS_MANY ,
            'class_name'            => 'secondary',
            'mapping_name'          => 'secondary',
            'foreign_key'           => 'type_id',
            'condition'             => 'type=1',
        ],
        
    );
   
   
}