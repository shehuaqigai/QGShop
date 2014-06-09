<?php
namespace Admin\Controller;
use Think\Controller;
class CommodityMController extends Controller {
       private static $Item_cate;//产品分类表对象
       private static $item;//商品列表
    /**
     *商品分类数据处理动作
     */
    public function _initialize(){
        self::$Item_cate=D('Item_cate');
        self::$item=D('Item');
    }
    public function index(){}
    public function productManage(){
       $str=self::$item->productList();
        echo json_encode($str);

    }
    public function get_Cate(){

       $cate=self::$Item_cate->productCategory();
        echo json_encode($cate);
    }
    private function upload(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   = 512000 ;// 设置附件上传大小
        $upload->exts      = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath="./Public/resource/uploads/";
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            return false;
        }else{// 上传成功
            return $info;
        }
    }

    public function add_cate(){
        $post=I("post.");
        $files=$this->upload();
        if($files){
            $return=[];
            $file=$files['file'];
            $path=$file['savepath'].$file['savename'];
            $post['img']=$path;
            $result=self::$Item_cate->add_cate($post);
            if($result){
                $return['img']=$path;
                $return['id']=$result;
                echo json_encode($return);
            }
        }

    }


    public function update_cate(){
        $post=I("post.");
        $files=$this->upload();
        if($files){
            $return=[];
            $file=$files['file'];
            $path=$file['savepath'].$file['savename'];
            $post['img']=$path;
            $id=$post['id'];
            $image=self::$Item_cate->getImageUrl($id);
            $result=self::$Item_cate->update_cate($post);
            if($result){
                $this->deleteImageFile($image);
                echo $path;
            }
        }

    }

    public function deleteImageFile($img){
        $path="./Public/resource/uploads/".$img;  //传递的路径两端要有/
        if(file_exists($path)){
            $res=unlink($path);
            if($res){}
        }

    }




}