一.签名算法
在该项目中除非特殊注明，所有接口均遵循此算法。
关于签名分别有三个参数sign, decoder_str, app_id, timestamp分别代表签名，随机字符串，appid，UNIX时间戳(10位精确至秒)。
计算签名时先对所有参数名进行字典序排序，然后拼接各个参数的值，并在最后加上appkey，然后进行md5计算得到签名并且转为大写。
举例：
{
    'key1':'我是一个参数',
    'key2':'我也是一个参数',
    'decoder_str':'512fd712gdga62',
    'app_id':1,
    'timestamp':1581952284
}
按照字典序排序后如下：
{
    'app_id':1,
    'decoder_str':'512fd712gdga62',
    'key1':'我是一个参数',
    'key2':'我也是一个参数',
    'timestamp':1581952284
}
按照该顺序对各值进行连接得到：
'1512fd712gdga62我是一个参数我也是一个参数1581952284'
在尾部加入appkey(假定为1q2w3e4r5t)：
'1512fd712gdga62我是一个参数我也是一个参数15819522841q2w3e4r5t'
计算得到md5：
'0e634ebb5f557628b7cf02fdab281542'
转换成大写：
''
将该值作为sign插入到要提交的数据中：
{
    'app_id':1,
    'decoder_str':'512fd712gdga62',
    'key1':'我是一个参数',
    'key2':'我也是一个参数',
    'timestamp':1581952284,
    'sign':'0e634ebb5f557628b7cf02fdab281542'
}
二.登录接口
地址:https://api.aiim.ren/Identify.php
应有以下参数
{
    'type': 'login',//这里必须为login
    'id': 'UID/邮箱/手机号',
    'password': '密码',
}
如果登录成功会返回状态码100，若失败请见上文中其他状态码
