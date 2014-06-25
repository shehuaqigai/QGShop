(function(){
    //首页视图
    ADMIN.tpl.adminHome = _.template('' +
        '<header class="header">微信商城系统管理</header>' +
        '<div class="padding"></div>'+//由于头是采用fixed定位，所有用了个清除的div占位置，无实际用处
        '<article id="container">' +
            '<div class="leftNav">' +
                '<ul></ul>' +
            '</div>' +
            '<div id="showContent">' +
                '<h1 class="currentTitle">后台首页</h1>'+
                '<div class="settingContainer">' +
                    '<div id="tem_Dialog"></div>' +
                '</div>'+
            '</div>' +
        '</article>' +
        '<footer class="footer"></footer>'+
        '');

//左边的设置列表导航视图
    ADMIN.tpl.navListUI= _.template(
        "<li>" +
            "<dl>" +
                "<dt class='<%= className %>'><i class='icon-contract'></i><%= category %></dt>" +
                "<div class='switchContainer'>"+
                "<% _.each(contentSet,function(value,key){ %><dd class='<%= key %>'><%= value %></dd><% }) %>"+
                "</div>"+
            "</dl>" +
        "</li>"+
    "");
}).call(this);
