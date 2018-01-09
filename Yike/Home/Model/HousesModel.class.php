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
class HousesModel extends RelationModel {
    
    protected $tableName        =   'houses';
    //自动验证规则
    protected $_validate = array(
        
        array('name','require','房源名称不能为空。'),  
        array('name','','房源名称已经存在！',1,'unique',3),
       
       
    );
    //关联模型关联信息
    protected $_link=array(
        
        //会员 电话  二手房 详情页 打电话功能  
        'yk'=>[
            'mapping_type'          => self::BELONGS_TO,
            'class_name'            => 'users',
            'mapping_name'          => 'yk',
            'foreign_key'           => 'user_id',
            'as_fields'             => 'telephone:tel',
        ],
        
        
        //楼层
        'floor'=>[
            'mapping_type'          => self::BELONGS_TO ,
            'class_name'            => 'floor',
            'mapping_name'          => 'floor',
            'foreign_key'           => 'floor_id',
            'as_fields'             =>'floor_name',
        ],
        //户型
         'layout'=>[
            'mapping_type'          => self::BELONGS_TO ,
            'class_name'            => 'layout',
            'mapping_name'          => 'layout',
            'foreign_key'           => 'layout_id',
            'as_fields'             => 'layout_name',
        ],
        //区域
        'area'=>[
            'mapping_type'          => self::BELONGS_TO ,
            'class_name'            => 'area',
            'mapping_name'          => 'area',
            'foreign_key'           => 'area_id',
            'as_fields'             => 'area_name',
        ],
        //特色
        'nature'=>[
            'mapping_type'          => self::BELONGS_TO ,
            'class_name'            => 'nature',
            'mapping_name'          => 'nature',
            'foreign_key'           => 'nature_id',
            'as_fields'             => 'nature_name',
        ],
        //装修
        'fitment'=>[
            'mapping_type'          => self::BELONGS_TO ,
            'class_name'            => 'fitment',
            'mapping_name'          => 'fitment',
            'foreign_key'           => 'fitment_id',
            'as_fields'             => 'fitment_name',
        ],
        //朝向
        'orientation'=>[
            'mapping_type'          => self::BELONGS_TO ,
            'class_name'            => 'orientation',
            'mapping_name'          => 'orientation',
            'foreign_key'           => 'orientation_id',
            'as_fields'             => 'orientation_name',
        ],
        //产权
        'property'=>[
            'mapping_type'          => self::BELONGS_TO ,
            'class_name'            => 'property',
            'mapping_name'          => 'property',
            'foreign_key'           => 'property_id',
            'as_fields'             => 'property_name',
        ],
        //类型
        'type'=>[
            'mapping_type'          => self::BELONGS_TO ,
            'class_name'            => 'type',
            'mapping_name'          => 'type',
            'foreign_key'           => 'type_id',
            'as_fields'             => 'type_name',
        ],
        //来源
        'from'=>[
            'mapping_type'          => self::BELONGS_TO ,
            'class_name'            => 'from',
            'mapping_name'          => 'from',
            'foreign_key'           => 'from_id',
            'as_fields'             => 'from_name',
        ],
        //面积
        'area'=>[
            'mapping_type'          => self::BELONGS_TO ,
            'class_name'            => 'area',
            'mapping_name'          => 'area',
            'foreign_key'           => 'area_id',
            'as_fields'             => 'area_name',
        ],
        //房龄
        'age'=>[
            'mapping_type'          => self::BELONGS_TO ,
            'class_name'            => 'age',
            'mapping_name'          => 'age',
            'foreign_key'           => 'age_id',
            'as_fields'             => 'age_name',
        ],
    
        //房源与图片关系 
        'house_picture'=>[
            'mapping_type'          => self::HAS_MANY ,
            'class_name'            => 'house_picture',
            'mapping_name'          => 'picture',
            'foreign_key'           => 'house_id',
        ],
        
        //新房源 与 户型关系 
        'house_type'=>[
            'mapping_type'          => self::HAS_MANY ,
            'class_name'            => 'house_type',
            'mapping_name'          => 'housetype',
            'foreign_key'           => 'house_id',
        ],
        //重要字段
        'important'=>[
            'mapping_type'          => self::HAS_MANY ,
            'class_name'            => 'important',
            'mapping_name'          => 'important',
            'foreign_key'           => 'house_id',            
        ],
        //次要字段
        'secondary'=>[
            'mapping_type'          => self::HAS_MANY ,
            'class_name'            => 'secondary',
            'mapping_name'          => 'secondary',
            'foreign_key'           => 'house_id',      
        ],
    
    );
   
}