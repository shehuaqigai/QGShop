jshint运用
===========
已经全局安装jshint  npm install jshint -g

运行这个检查代码编码规范
更好的团队开发
更好的可读性
更好的一致性
实现优雅的项目设计

prop	description
bitwise	如果是true，则禁止使用位运算符
curly	如果是true，则要求在if/while的模块时使用TAB结构
debug	如果是true，则允许使用debugger的语句
eqeqeq	如果是true，则要求在所有的比较时使用===和!==
evil	如果是true，则允许使用eval方法
forin	如果是true，则不允许for in在没有hasOwnProperty时使用
maxerr	默认是50。 表示多少错误时，jsLint停止分析代码
newcap	如果是true，则构造函数必须大写
nopram	如果是true，则不允许使用arguments.caller和arguments.callee
nomen	如果是true，则不允许在名称首部和尾部加下划线
onevar	如果是true，则在一个函数中只能出现一次var
passfail	如果是true，则在遇到第一个错误的时候就终止
plusplus	如果是true，则不允许使用++或者- -的操作
regexp	如果是true，则正则中不允许使用.或者[^…]
undef	如果是ture，则所有的局部变量必须先声明之后才能使用
sub	如果是true，则允许使用各种写法获取属性(一般使用.来获取一个对象的属性值)
strict	如果是true，则需要使用strict的用法，
详见http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/
white	如果是true，则需要严格使用空格用法。