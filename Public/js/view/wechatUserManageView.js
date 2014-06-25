(function(){
    //首页视图生成页
    var wechatUserManage=Backbone.View.extend({
        el:"#wechatUserManage",
        events:{

        },
        template:ADMIN.tpl,//模板
        button:null,
        modelData:null,
        //初始化应用
        initialize:function(){
            this.data=this.modelData;
        },
        init:function(button){
            this.$el.empty();
            this.button=button;
            this.renderSwitch(button);

        },
        renderSwitch:function(button){
            this[button]();
        },
        groupManage:function(){},
        userProfile:function(){},
        fllowList:function(){},
        //页面渲染
        render:function(){

        },
        destroy:function(){
            this.remove();

        }
    });
    window.ADMIN.V.wechatUserManageView=wechatUserManage;
}());