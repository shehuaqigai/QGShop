<?php if (!defined('THINK_PATH')) exit();?>        <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, target-densitydpi=device-dpi, initial-scale=1, user-scalable=0, maximum-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>微商城后台管理</title>
    <link rel="shortcut icon" href="/QGShop/Public/imgs/favicon.ico" type="image/x-icon">
    <link href="/QGShop/Public/css/lib/iconmoon.css" rel="stylesheet"/>
    <link href="/QGShop/Public/css/lib/reset.css" rel="stylesheet"/>






        <!--加载文件上传样式-->
        <link rel="stylesheet" href="/QGShop/Public/css/lib/plupload/css/queue.css" type="text/css" media="screen"/>
         <!--对话框插件样式-->
        <link rel="stylesheet" href="/QGShop/Public/css/lib/Dialog/flat/zebra_dialog.css" type="text/css">
        <!--日期选择器-->
        <link rel="stylesheet" href="/QGShop/Public/css/lib/jquery.datetimepicker.css" type="text/css">
        <!--各个设置样式-->
        <link rel="stylesheet" href="/QGShop/Public/build/debug/css/custom/admin.css" type="text/css">
        <link rel="stylesheet" href="/QGShop/Public/build/debug/css/custom/adminManage.css" type="text/css">
        <link rel="stylesheet" href="/QGShop/Public/build/debug/css/custom/commodityManage.css" type="text/css">
        <link rel="stylesheet" href="/QGShop/Public/build/debug/css/custom/customMenu.css" type="text/css">
        <link rel="stylesheet" href="/QGShop/Public/build/debug/css/custom/otherSetting.css" type="text/css">
        <link rel="stylesheet" href="/QGShop/Public/build/debug/css/custom/tradeManage.css" type="text/css">
        <link rel="stylesheet" href="/QGShop/Public/build/debug/css/custom/wechat.css" type="text/css">
        <link rel="stylesheet" href="/QGShop/Public/build/debug/css/custom/wechatSet.css" type="text/css">
        <link rel="stylesheet" href="/QGShop/Public/build/debug/css/custom/wechatUserManage.css" type="text/css">
        <!--压缩css-->
        <!--link href="/QGShop/Public/bulid/release/css/admin_home.min.css" rel="stylesheet"/-->
        </head>
    <body>
    <!--qunit开头的都是单元测试ui-->
    <!--div id="qunit-tests"></div>
    <div id="qunit-header"></div>
    <div id="qunit-banner"></div>
    <div id="qunit-testrunner-toolbar"></div>
    <div id="qunit-userAgent"></div>
    <div id="qunit-testresult"></div>
    <div id="qunit-modulefilter"></div>
    <div id="qunit-modulefilter-container"></div>
    <div id="qunit-fixture"></div-->
    <!--单元ui结束-->
    <div id="adminPage"></div>
        <script>
                /**
                 *dom的操作全部交给js来处理
                 * 前端采用mvc的前端架构
                 * 为了可维护，可扩展，采用mvc开发
                 * 采用build工具打包
                 * 整个应用全局变量就一个ADMIN
                 * 其他的必须放入闭包里
                 * @type {{}}
                 */
            var ADMIN={};//后台应用
                ADMIN.M={};//模型层
                ADMIN.V={};//视图层
                ADMIN.C={};//控制器层
                ADMIN.tpl={};//模板
                ADMIN.global={}//对象之间交互用到的变量都放入这个容器里
                ADMIN.global.APPPATH="/QGShop/";//网站的跟路径
                ADMIN.global.ADMINPATH="/QGShop/index.php/Admin/";//后台模块admin的根路径
                ADMIN.global.mixData={};
        </script>
        <!--加载第三方类库文件 start-->
<script src="/QGShop/Public/js/lib/jquery-2.0.2.js"></script>
<script src="/QGShop/Public/js/lib/underscore.js"></script>
<script src="/QGShop/Public/js/lib/backbone.js"></script>
<script src="/QGShop/Public/js/lib/modernizr.js"></script>
<script src="/QGShop/Public/js/lib/jquery.datetimepicker.js"></script>
<script src="/QGShop/Public/js/lib/iscroll.js"></script>
<!--文件上传类库-->
<script type="text/javascript" src="/QGShop/Public/js/lib/plupload/moxie.js"></script>
<script type="text/javascript" src="/QGShop/Public/js/lib/plupload/plupload.dev.js"></script>
<script type="text/javascript" src="/QGShop/Public/js/lib/plupload/queue.js"></script>
<!--jquery对话弹框插件-->
<script type="text/javascript" src="/QGShop/Public/js/lib/zebra_dialog.src.js"></script>
<!--单元测试库-->
<script type="text/javascript" src="/QGShop/Public/js/lib/unitTest/qunit1.14.js"></script>









        <!--加载模板文件 start-->
        <script src="/QGShop/Public/js/template/admin.tpl.js"></script>
        <script src="/QGShop/Public/js/template/customMenu.tpl.js"></script>
        <script src="/QGShop/Public/js/template/wechatSet.tpl.js"></script>
        <script src="/QGShop/Public/js/template/wechatUserManage.tpl.js"></script>
        <script src="/QGShop/Public/js/template/adminManage.tpl.js"></script>
        <script src="/QGShop/Public/js/template/commodityManage.tpl.js"></script>
        <script src="/QGShop/Public/js/template/otherSetting.tpl.js"></script>
        <script src="/QGShop/Public/js/template/tradeManage.tpl.js"></script>
        <!--加载模型文件 start-->
        <script src="/QGShop/Public/js/model/adminModel.js"></script>
        <script src="/QGShop/Public/js/model/customMenuModel.js"></script>
        <script src="/QGShop/Public/js/model/wechatSetModel.js"></script>
        <script src="/QGShop/Public/js/model/wechatUserManageModel.js"></script>
        <script src="/QGShop/Public/js/model/adminManageModel.js"></script>
        <script src="/QGShop/Public/js/model/commodityManageModel.js"></script>
        <script src="/QGShop/Public/js/model/otherSettingModel.js"></script>
        <script src="/QGShop/Public/js/model/tradeManageModel.js"></script>
        <!--加载视图文件 start-->
        <script src="/QGShop/Public/js/view/customMenuView.js"></script>
        <script src="/QGShop/Public/js/view/wechatSetView.js"></script>
        <script src="/QGShop/Public/js/view/wechatUserManageView.js"></script>
        <script src="/QGShop/Public/js/view/adminView.js"></script>
        <script src="/QGShop/Public/js/view/adminManageView.js"></script>
        <script src="/QGShop/Public/js/view/commodityManageView.js"></script>
        <script src="/QGShop/Public/js/view/otherSettingView.js"></script>
        <script src="/QGShop/Public/js/view/tradeManageView.js"></script>
        <!--加载控制器文件 start-->
        <script src="/QGShop/Public/js/controller/adminController.js"></script>
        <!--加载启动文件 start-->
        <script src="/QGShop/Public/js/bootstrap/admin.js"></script>

 <!-------------------------------下面是测试单元在发布版本将移除-------------------------------->
        <!--单元测试文件 start-->
        <!--测试代码模型-->
        <!--script src="/QGShop/Public/js/test/model/adminModelTest.js"></script>
        <script src="/QGShop/Public/js/test/model/customMenuModelTest.js"></script>
        <script src="/QGShop/Public/js/test/model/wechatSetModelTest.js"></script>
        <script src="/QGShop/Public/js/test/model/wechatUserManageModelTest.js"></script>
        <script src="/QGShop/Public/js/test/model/adminManageModelTest.js"></script>
        <script src="/QGShop/Public/js/test/model/commodityManageModelTest.js"></script>
        <script src="/QGShop/Public/js/test/model/otherSettingModelTest.js"></script>
        <script src="/QGShop/Public/js/test/model/tradeManageModelTest.js"></script>
        <!--测试代码视图-->
        <!--script src="/QGShop/Public/js/test/view/customMenuViewTest.js"></script>
        <script src="/QGShop/Public/js/test/view/wechatSetViewTest.js"></script>
        <script src="/QGShop/Public/js/test/view/wechatUserManageViewTest.js"></script>
        <script src="/QGShop/Public/js/test/view/adminViewTest.js"></script>
        <script src="/QGShop/Public/js/test/view/adminManageViewTest.js"></script>
        <script src="/QGShop/Public/js/test/view/commodityManageViewTest.js"></script>
        <script src="/QGShop/Public/js/test/view/otherSettingViewTest.js"></script>
        <script src="/QGShop/Public/js/test/view/tradeManageViewTest.js"></script>
        <!--测试代码控制器-->
        <!--script src="/QGShop/Public/js/test/controller/adminControllerTest.js"></script>
        <!--测试代码-->
        <!--script src="/QGShop/Public/js/test/bootstrap/adminTest.js"></script>
        <script src="/QGShop/Public/js/test/test.js"></script-->
    </body>
</html>