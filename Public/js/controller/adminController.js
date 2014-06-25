(function(root){

    var Router = Backbone.Router.extend({
        routes : {
            //后台首页页面默认路由
            '' : 'main',
            //微信设置控制路由
            'wechatSet/:indexPage':'wechatSet',
            'wechatSet/:interfaceSet':'wechatSet',
            'wechatSet/:massSendMesg':'wechatSet',
            'wechatSet/:mesgSet':'wechatSet',
            'wechatSet/:fllowPush':'wechatSet',
            'wechatSet/:sceneQRcode':'wechatSet',
             //微信用户管理控制路由
            'wechatUserManage/:groupManage':'wechatUserManage',
            'wechatUserManage/:userProfile':'wechatUserManage',
            'wechatUserManage/:fllowList':'wechatUserManage',
            //自定义菜单控制路由
            'customMenu/:createMenu' : 'customMenu',
            'customMenu/:selectMenu' : 'customMenu',
            'customMenu/:deleteMenu' : 'customMenu',
            //商品管理控制路由
            'commodityManage/:productManage' : 'commodityManage',
            'commodityManage/:productCategory' : 'commodityManage',
            'commodityManage/:addProduct' : 'commodityManage',
            'commodityManage/:brandManage' : 'commodityManage',
            'commodityManage/:add_categoryAction':'add_categoryAction',//添加商品动作
            //交易管理控制路由
            'tradeManage/:indentManage' : 'tradeManage',
            'tradeManage/:expressage' : 'tradeManage',
            'tradeManage/:address' : 'tradeManage',
            'tradeManage/:gathering' : 'tradeManage',
            //管理员管理控制路由
            'adminManage/:admin' : 'adminManage',
            'adminManage/:role' : 'adminManage',
            //其他设置控制路由
            'otherSetting/:Advertise' : 'otherSetting',
            'otherSetting/:siteSet' : 'otherSetting'
},
        collection:ADMIN.M,
        /**
         * 这个数组是是一个视图队列
         * 这里采用的设计是设置视图压入压出
         * 为了防止内存溢出
         * 当切换到另一个视图设置的时候
         * 销毁之前的视图，回收内存
         */
        taskViewQueue:[],
        ismain:false,
        iswechatSet:false,
        iswechatUserManage:false,
        iscustomMenu:false,
        iscommodityManage:false,
        istradeManage:false,
        isadminMamage:false,
        isotherSetting:false,
        /**
         * 后台首页默认页
         */
        main : function() {
            if(!this.ismain){
               new ADMIN.V.adminView({collection:new this.collection.navList()});
                this.ismain=true;
            }else{
            }
        },
        /**
         * 微信基本配置功能对象
         * @param item
         */
        wechatSet:function(item){

             this.jumpViewAction('wechatSet',item);

        },
        /**
         * 微信用户管理设置功能对象
         * @param item
         */
        wechatUserManage:function(item){
            this.jumpViewAction('wechatUserManage',item);

        },
        /**
         * 自定义菜单设置功能对象
         * @param item
         */
        customMenu:function(item){
            this.jumpViewAction('customMenu',item);

        },
        /**
         * 商品管理设置功能对象
         * @param item
         */
        commodityManage:function(item){
            this.jumpViewAction('commodityManage',item);

        },
        /**
         * 订单管理设置功能对象
         * @param item
         */
        tradeManage:function(item){
             this.jumpViewAction('tradeManage',item);

        },
        /**
         * 管理员管理设置功能对象
         * @param item
         */
        adminMamage:function(item){
                this.jumpViewAction('adminMamage',item);
        },
        /**
         * 其他设置功能对象
         * @param item
         */
        otherSetting:function(item){
               this.jumpViewAction('otherSetting',item);
        },
        /**
         * 进入到设置视图之前的工作
         * @param view
         * @param item
         */
        jumpViewAction:function(viewName,item){
            //判断当前设置分类是否已经实例化
            var isViewObj='is'+viewName;
            //任务队列
            var task=this.taskViewQueue;
            //触发自定义事件改变标题
            Backbone.Events.trigger('changeTitle',item,viewName);
            if(!this[isViewObj]){
                $('#showContent .settingContainer').attr('id',viewName);
                this[isViewObj]=true;
                if(task.length==2){
                    task[0].destroy();
                    this[task[1]]=false;
                    task.length=0;
                }
                var data=this.collection[viewName] ? this.collection[viewName] : null;
                var obj=new ADMIN.V[viewName+'View']({collection:data,item:item});
                this.taskViewQueue.push(obj);
                this.taskViewQueue.push(isViewObj);
            }else{
                task[0].init(item);
            }



        },
        /**
         * 路由初始化调用
         */
        initialize:function(){//实例化对象后调用可以如果指定
           // this.on("route:",function(e){console.log(e);});
        }
    });
    root.ADMIN.C.AdminRouter=Router;
}(window));

