(function(){

ADMIN["tpl"] = ADMIN["tpl"] || {};
ADMIN["tpl"] ["adminHome"] = _.template('<header class="header">微信商城系统管理</header><div class="padding"></div><article id="container"><div class="leftNav"><ul></ul></div><div id="showContent"><h1 class="currentTitle">后台首页</h1><div class="settingContainer"><div id="tem_Dialog"></div></div></div></article><footer class="footer"></footer>');
ADMIN["tpl"] ["navListUI"] = _.template('<li><dl><dt class="<%= className %>"><i class="icon-contract"></i><%= category %></dt><div class="switchContainer"><% _.each(contentSet,function(value,key){ %><dd class="<%= key %>"><%= value %></dd><% }) %></div></dl></li>');

})();