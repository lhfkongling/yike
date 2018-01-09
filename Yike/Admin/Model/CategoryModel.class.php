<?php
// +----------------------------------------------------------------------
// | goods 商品 模型验证
// +----------------------------------------------------------------------
// | 参考网站网址：
// +----------------------------------------------------------------------
// | Author: 李洪发 2017-03
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;
/**
* 站点Logic
*/
class CategoryModel extends Model {
    
//     protected $patchValidate = true;
    
    protected $_validate = array(
        
        array('cat_name','require','技能名称不能为空。'),  
        array('cat_name','','技能名称已经存在！',1,'unique',3),
        array('is_show','number','是否显示必选选择'),
        array('sort','number','排序必须填写数字。'),
       
     
    );
   
}