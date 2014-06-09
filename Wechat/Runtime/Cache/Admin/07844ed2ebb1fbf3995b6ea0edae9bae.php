<?php if (!defined('THINK_PATH')) exit();?>        <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, target-densitydpi=device-dpi, initial-scale=1, user-scalable=0, maximum-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>微商城后台管理</title>
    <link rel="shortcut icon" href="/xampp/remoteLinux/Public/imgs/favicon.ico" type="image/x-icon">
    <link href="/xampp/remoteLinux/Public/css/lib/reset.css" rel="stylesheet"/>
    <link href="/xampp/remoteLinux/Public/css/lib/iconmoon.css" rel="stylesheet"/>





        <!--加载jquery日历插件样式-->
        <link href="/xampp/remoteLinux/Public/css/lib/jquery.datetimepicker.css" rel="stylesheet"/>
        <!--加载文件上传样式-->
        <link rel="stylesheet" href="/xampp/remoteLinux/Public/css/lib/plupload/css/queue.css" type="text/css" media="screen" />
         <!--对话框插件样式-->
        <link rel="stylesheet" href="/xampp/remoteLinux/Public/css/lib/Dialog/flat/zebra_dialog.css" type="text/css">
        <!--加载-各个视图样式-->
        <link href="/xampp/remoteLinux/Public/css/custom/admin.css" rel="stylesheet"/>
        <link href="/xampp/remoteLinux/Public/css/custom/wechatSet.css" rel="stylesheet"/>
        <link href="/xampp/remoteLinux/Public/css/custom/adminManage.css" rel="stylesheet"/>
        <link href="/xampp/remoteLinux/Public/css/custom/commodityManage.css" rel="stylesheet"/>
        <link href="/xampp/remoteLinux/Public/css/custom/otherSetting.css" rel="stylesheet"/>
        <link href="/xampp/remoteLinux/Public/css/custom/tradeManage.css" rel="stylesheet"/>
        <link href="/xampp/remoteLinux/Public/css/custom/customMenu.css" rel="stylesheet"/>
        <link href="/xampp/remoteLinux/Public/css/custom/wechatUserManage.css" rel="stylesheet"/>
        </head>
    <body>
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
                ADMIN.global.APPPATH="/xampp/remoteLinux/";//网站的跟路径
                ADMIN.global.ADMINPATH="/xampp/remoteLinux/index.php/Admin/";//后台模块admin的根路径
                ADMIN.global.mixData={};
        </script>
        <!--加载第三方类库文件 start-->
<script src="/xampp/remoteLinux/Public/js/lib/jquery-2.0.2.js"></script>
<script src="/xampp/remoteLinux/Public/js/lib/underscore.js"></script>
<script src="/xampp/remoteLinux/Public/js/lib/backbone.js"></script>
<script src="/xampp/remoteLinux/Public/js/lib/modernizr.js"></script>
<script src="/xampp/remoteLinux/Public/js/lib/jquery.datetimepicker.js"></script>
<script src="/xampp/remoteLinux/Public/js/lib/iscroll.js"></script>
<!--文件上传类库-->
<script type="text/javascript" src="/xampp/remoteLinux/Public/js/lib/plupload/moxie.js"></script>
<script type="text/javascript" src="/xampp/remoteLinux/Public/js/lib/plupload/plupload.dev.js"></script>
<script type="text/javascript" src="/xampp/remoteLinux/Public/js/lib/plupload/queue.js"></script>
<!--jquery对话弹框插件-->
<script type="text/javascript" src="/xampp/remoteLinux/Public/js/lib/zebra_dialog.src.js"></script>








        <!--加载模板文件 start-->
        <script src="/xampp/remoteLinux/Public/js/template/admin.tpl.js"></script>
        <script src="/xampp/remoteLinux/Public/js/template/customMenu.tpl.js"></script>
        <script src="/xampp/remoteLinux/Public/js/template/wechatSet.tpl.js"></script>
        <script src="/xampp/remoteLinux/Public/js/template/wechatUserManage.tpl.js"></script>
        <script src="/xampp/remoteLinux/Public/js/template/adminManage.tpl.js"></script>
        <script src="/xampp/remoteLinux/Public/js/template/commodityManage.tpl.js"></script>
        <script src="/xampp/remoteLinux/Public/js/template/otherSetting.tpl.js"></script>
        <script src="/xampp/remoteLinux/Public/js/template/tradeManage.tpl.js"></script>
        <!--加载模型文件 start-->
        <script src="/xampp/remoteLinux/Public/js/model/adminModel.js"></script>
        <script src="/xampp/remoteLinux/Public/js/model/customMenuModel.js"></script>
        <script src="/xampp/remoteLinux/Public/js/model/wechatSetModel.js"></script>
        <script src="/xampp/remoteLinux/Public/js/model/wechatUserManageModel.js"></script>
        <script src="/xampp/remoteLinux/Public/js/model/adminManageModel.js"></script>
        <script src="/xampp/remoteLinux/Public/js/model/commodityManageModel.js"></script>
        <script src="/xampp/remoteLinux/Public/js/model/otherSettingModel.js"></script>
        <script src="/xampp/remoteLinux/Public/js/model/tradeManageModel.js"></script>
        <!--加载视图文件 start-->
        <script src="/xampp/remoteLinux/Public/js/view/customMenuView.js"></script>
        <script src="/xampp/remoteLinux/Public/js/view/wechatSetView.js"></script>
        <script src="/xampp/remoteLinux/Public/js/view/wechatUserManageView.js"></script>
        <script src="/xampp/remoteLinux/Public/js/view/adminView.js"></script>
        <script src="/xampp/remoteLinux/Public/js/view/adminManageView.js"></script>
        <script src="/xampp/remoteLinux/Public/js/view/commodityManageView.js"></script>
        <script src="/xampp/remoteLinux/Public/js/view/otherSettingView.js"></script>
        <script src="/xampp/remoteLinux/Public/js/view/tradeManageView.js"></script>
        <!--加载控制器文件 start-->
        <script src="/xampp/remoteLinux/Public/js/controller/adminController.js"></script>
        <!--加载启动文件 start-->
        <script src="/xampp/remoteLinux/Public/js/bootstrap/admin.js"></script>
    </body>
</html>