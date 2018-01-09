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
class SecondhousesModel extends RelationModel {
    
   
    protected $tableName        =   'second_houses';
    //自动验证规则
    protected $_validate = array(
        
        array('name','require','二手房名称不能为空。'),  
        array('name','','二手房名称已经存在！',1,'unique',3),
        array('area','require','建设面积不能为空。'),
        array('sell_price','require','售价不能为空。'),
        array('average_price','require','单价不能为空。'),
        array('first_pay','require','首付不能为空。'),
        array('floor','require','楼层不能为空。'),
        array('layout','require','户型不能为空。'),
        array('orientation','require','朝向不能为空。'),
        array('step','require','楼梯不能为空。'),
        array('disgin','require','装修不能为空。'),
        array('years','require','年代不能为空。'),        
        
    );
    //关联模型关联信息
    protected $_link=array(
        
        'layout'=>[
            'mapping_type'          => self::BELONGS_TO ,
            'class_name'            => 'layout',
            'mapping_name'          => 'layout',
            'foreign_key'           => 'layout_id',
            'as_fields'             => 'layout_name',
        ],
        'floor'=>[
            'mapping_type'          => self::BELONGS_TO ,
            'class_name'            => 'floor',
            'mapping_name'          => 'floor',
            'foreign_key'           => 'floor_id',
            'as_fields'             => 'floor_name',
        ],
        
        'second_picture'=>[
            'mapping_type'          => self::HAS_MANY ,
            'class_name'            => 'second_picture',
            'mapping_name'          => 'picture',
            'foreign_key'           => 'second_id',
        ],
        'important'=>[
            'mapping_type'          => self::HAS_MANY ,
            'class_name'            => 'important',
            'mapping_name'          => 'important',
            'foreign_key'           => 'type_id',
            'condition'             => 'type=2',
            'parent_key'            =>'second_id',
        ],
        
        'secondary'=>[
            'mapping_type'          => self::HAS_MANY ,
            'class_name'            => 'secondary',
            'mapping_name'          => 'secondary',
            'foreign_key'           => 'type_id',
            'condition'             => 'type=2',
            'parent_key'            =>'second_id',
        ],
        
    );
   
}