一个非常简易的留言后端，在不储存账户的情况下实现了账号登录

Api文档：

获取留言内容：  
get：https://your.domain/  
返回值:  
empty:无留言时返回  
[json数据]:留言内容

登录（获取验证码）：  
post：https://your.domain/login.php  
body参数：  
email（传入要登陆的邮箱）  
device（设备识别码，设备自己随机生成一个，生成之后不要改变，不然对应的token也会改变）  
返回值：  
code_please：验证码发送成功  
too_frequent：验证码请求过于频繁  
invalid：无效请求

登录（获取token）：  
post：https://your.domain/login.php  
body参数：  
email（传入要登陆的邮箱）  
device（设备识别码，设备自己随机生成一个，生成之后不要改变，不然对应的token也会改变）  
code（邮箱收到的验证码）  
返回值：  
code_invalid：验证码错误  
invalid：无效请求  
[json数据]:token内容包含token和f_token，token为当前token，f_token为未来的token，如果token失效了可以用f_token，避免总是需要获取邮箱验证码


登录（更新token）：  
post：https://your.domain/login.php  
body参数：  
email（传入要登陆的邮箱）  
device（设备识别码，设备自己随机生成一个，生成之后不要改变，不然对应的token也会改变）  
token（当前token）  
返回值：  
token_invalid：token错误  
invalid：无效请求  
[json数据]:token内容包含token和f_token，token为当前token，f_token为未来的token，如果token失效了可以用f_token，避免总是需要获取邮箱验证码


发送留言：  
post：https://your.domain/  
body参数：  
content（留言内容）  
email（传入要登陆的邮箱）  
device（设备识别码，设备自己随机生成一个，生成之后不要改变，不然对应的token也会改变）  
token（当前token）  
返回值：  
ok：发送成功  
invalid：非法请求


删除留言（只能删除自己的留言）：  
使用delete请求，参数如下:  
https://your.domain?email=【邮箱】&position=【删除的留言序号】&token=【token】&device=【设备识别码】  
返回值：  
ok：删除成功  
invalid：非法请求