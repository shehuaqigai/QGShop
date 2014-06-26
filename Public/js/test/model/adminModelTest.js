(function(){
//分类表中的一行
    var navData=Backbone.Model.extend({
        defaults:{
            category: "",
            contentSet: "",
            type: ""
        }
    });
    var navList=Backbone.Collection.extend({
            model:navData,
            initialize:function(){
                var mode='';
                var self=this;
                var curmodel=new this.model();
                var navList=[
                     {
                        category:'微信设置',
                        contentSet:{
                            indexPage:'后台首页',
                            interfaceSet:'基本配置',
                            mesgSet:'消息设置',
                            fllowPush:'关注回复',
                            massSendMesg:'群发消息',
                            sceneQRcode:"带参数二维码"

                        },
                         className:'wechatSet'
                     },
                     {
                        category:'微信用户管理',
                        contentSet:{
                            groupManage:'分组管理',//包括创建分组,查询所有分组,修改分组名,
                            userProfile:'用户基本信息',//移动用户分组,查询用户所在分组
                            fllowList:'关注者列表'

                        },
                       className:'wechatUserManage'
                     },
                    {
                        category:'自定义菜单',
                        contentSet:{
                            createMenu:'创建菜单',
                            selectMenu:'菜单查询',
                            deleteMenu:'删除菜单'
                        },
                        className:'customMenu'
                    },
                    {
                        category:'商品管理',
                        contentSet:{
                            productManage:'产品管理',
                            productCategory:'商品分类',
                            addProduct:'添加商品',
                            brandManage:'品牌管理'

                        },
                        className:'commodityManage'
                    },
                    {
                        category:'交易管理',
                        contentSet:{
                            indentManage:'订单管理',
                            expressage:'快递方式',
                            address:'发货地址',
                            gathering:'收款方式'

                        },
                        className:'tradeManage'
                    },
                    {
                        category:'管理员管理',
                        contentSet:{
                            admin:'管理员管理',
                            role:'角色管理'
                        },
                        className:'adminMamage'
                    },
                    {
                        category:'其他设置',
                        contentSet:{
                            Advertise:'广告设置管理',
                            siteSet:"站点设置"
                        },
                        className:'otherSetting'
                    }



                ];
                _.each(navList,function(value){
                    mode=curmodel.clone();
                    mode.set(value);
                    self.push(mode);
                });

            }
        });
    //创建表数据
    window.ADMIN.M.navList=navList;


})();





