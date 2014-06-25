(function(root){

    var AppRouter = Backbone.Router.extend({
        routes : {
            '' : 'main'
        },
        collection:APP.M.collections,
        main : function() {
            var app=new APP.V.indexView({collection:this.collection()});
        },
        initialize:function(){//实例化对象后调用  可以写一些初始化要干的事情
        }
    });
    root.APP.C.AppRouter=AppRouter;
}(window));