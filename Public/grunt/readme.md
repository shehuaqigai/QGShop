grunt安装
========
npm install -g grunt-cli
这条命令将会把grunt命令植入到你的系统路径中，这样就允许你从任意目录来运行它(定位到任意目录运行grunt命令)。
注意，安装grunt-cli并不等于安装了grunt任务运行器！Grunt CLI的工作很简单：
在Gruntfile所在目录调用运行已经安装好的相应版本的Grunt。
这就意味着可以在同一台机器上同时安装多个版本的Grunt
配置文件
=======
一个Grunt项目还有两个文件特别的重要：package.json用于Nodejs包管理，比如Grunt插件安装，
Gruntfile.js是Grunt配置文件，配置任务或者自定义任务。

Gruntfile.js由以下内容组成
1、wrapper函数，结构如下，这是Node.js的典型写法，使用exports公开API
    module.exports = function(grunt) {
      // Do grunt-related things in here
    };
2、项目和任务配置
3、载入grunt插件和任务
4、定制执行任务
