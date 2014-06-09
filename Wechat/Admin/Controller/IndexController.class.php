<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    /**
     * 后台登陆页面的地址
     * 如果已经登陆过了就直接跳转到后台首页
     * 如果退出浏览器就需要重新登陆
    */
    public function index(){
        if(!session('?PHPSESSID')){
            $this->display();
        }
      else{
          $this->redirect('Index/adminHome');
        }

    }

    /**
     * 这个是生成验证码
     */
    public function generatorVerifyCode(){
        $verify = new \Think\Verify();
        $verify->entry();

    }

    /**
     * 这个操作是验证前台发送过来得验证码
     */

    public function verifyCode(){
        $verify = new \Think\Verify();
        echo $verify->check($_POST['code']);
    }

    /**
     * 这个是验证用户名和密码
     * 如果验证成功就跳转到后台首页
     */

    public function verifyUser(){
        $user=I('post.user','stripslashes、htmlentities、htmlspecialchars,strip_tags');
        $passwd=I('post.passwd','stripslashes、htmlentities、htmlspecialchars,strip_tags');
        if(!$user || !$passwd){
            echo "非法请求!";
        }else{
            $passwd=md5($passwd);
            $userTable = D('admin');
            $data=$userTable->where('username="'.$user.'" and password="'.$passwd.'"')->find();
            if($data){
                session("PHPSESSID",cookie("PHPSESSID"));
                echo "ok";
            }
        }
    }

    /**
     * 后台首页地址
     */

    public function adminHome(){
                $this->display();

    }

    /**
     * 二维码生成器
     * 第一个参数是二维码写入的数据，
       第二个参数$outfile表示是否输出二维码图片文件(默认为false输出图片)
     * 如果不输出图片这个就填图片地址
       第三个参数H是ECC纠错级别，
      第四个参数是每个黑点的像素，
       第五个参数4是margin边缘空白的大小，
       第六个参数false $saveandprint表示是否保存二维码并显示。
    **/

    public function QRcodebuilder(){
        $value="http://www.baidu.com";
        $errorCorrectionLevel = "L";
        $matrixPointSize = "4";
        \PHPQRCode\QRcode::png($value, '.\Public\resource\QRcode\qiuge.png', $errorCorrectionLevel, $matrixPointSize);

    }


}