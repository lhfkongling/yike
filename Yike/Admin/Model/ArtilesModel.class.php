<?php
// +----------------------------------------------------------------------
// | goods 商品 模型验证
// +----------------------------------------------------------------------
// | 参考网站网址：
// +----------------------------------------------------------------------
// | Author: 李洪发 2017-03
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model\RelationModel;
/**
* 站点Logic
*/
class ArtilesModel extends RelationModel {
    
//     protected $patchValidate = true;
    //自动验证规则
    protected $_validate = array(
        
        array('name','require','文章标题不能为空。'),  
        array('name','','文章标题已经存在！',1,'unique',3),
        array('release','number','是否发布必选选择'),
        array('sort','number','排序必须填写数字。'),
        array('read_number','number','阅读数量必须填写数字。'),
        array('author','require','作者不能为空。'),
        array('keyword','require','关键字不能为空。'),
        array('brief','require','简介不能为空。'),

    );
    //关联模型关联信息
    protected $_link=array(
        'Category'=>array(
            'mapping_type'=>self::BELONGS_TO,
            'class_name'=>'category',
            'foreign_key'=>'cat_id',
            'as_fields'=>'cat_name',
            
        ),
        
        
    );
   
}