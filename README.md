##一.签名算法
####在该项目中除非特殊注明，所有接口均遵循此算法。
#####关于签名分别有三个参数：
| 参数        | 用途          |
| ---------- |:-------------:|
| sign       | 签名 |
| app_id     | 与app_key相对应并且与不同数据库相对应      |
| timestamp  | 十位精确至秒UNIX时间戳      |
#####计算签名时先对所有参数名进行字典序排序，然后把值转换为url格式并拼接为txt格式，并在最后加上appkey，然后进行md5计算得到签名并且转为大写。
#####举例：
```
{
    'key1':'我是一个参数',
    'key2':'我也是一个参数',
    'app_id':1,
    'timestamp':1581952284
}
```
#####按照字典序排序后如下：
```
{
    'app_id':1,
    'key1':'我是一个参数',
    'key2':'我也是一个参数',
    'timestamp':1581952284
}
```
#####各个值转换为url格式得到：
```
{
    'app_id':1,
    'key1':'%E6%88%91%E6%98%AF%E4%B8%80%E4%B8%AA%E5%8F%82%E6%95%B0',
    'key2':'%E6%88%91%E4%B9%9F%E6%98%AF%E4%B8%80%E4%B8%AA%E5%8F%82%E6%95%B0',
    'timestamp':1581952284
}
```
#####按照该顺序拼接为txt格式得到：
```
'app_id=1&key1=%E6%88%91%E6%98%AF%E4%B8%80%E4%B8%AA%E5%8F%82%E6%95%B0&key2=%E6%88%91%E4%B9%9F%E6%98%AF%E4%B8%80%E4%B8%AA%E5%8F%82%E6%95%B0&timestamp=1581952284'
```
#####在尾部加入app_key(假定为1q2w3e4r5t)：
```
'app_id=1&key1=%E6%88%91%E6%98%AF%E4%B8%80%E4%B8%AA%E5%8F%82%E6%95%B0&key2=%E6%88%91%E4%B9%9F%E6%98%AF%E4%B8%80%E4%B8%AA%E5%8F%82%E6%95%B0&timestamp=1581952284&app_key=1q2w3e4r5t'
```
#####计算得到md5：
```
'15b97b0ee95141af598ca1888d259d05'
```
#####转换成大写：
```
'15B97B0EE95141AF598CA1888D259D05'
```
#####将该值作为参数sign插入到要提交的数据中：
```
//json格式
{
    'app_id':1,
    'key1':'%E6%88%91%E6%98%AF%E4%B8%80%E4%B8%AA%E5%8F%82%E6%95%B0',
    'key2':'%E6%88%91%E4%B9%9F%E6%98%AF%E4%B8%80%E4%B8%AA%E5%8F%82%E6%95%B0',
    'timestamp':1581952284,
    'sign':'15B97B0EE95141AF598CA1888D259D05'
}
//txt格式
'app_id=1&key1=%E6%88%91%E6%98%AF%E4%B8%80%E4%B8%AA%E5%8F%82%E6%95%B0&key2=%E6%88%91%E4%B9%9F%E6%98%AF%E4%B8%80%E4%B8%AA%E5%8F%82%E6%95%B0&timestamp=1581952284&sign=15B97B0EE95141AF598CA1888D259D05'
```

##二.登录接口
####地址:https://api.aiim.ren/Identify.php
######应有以下参数
```
{
    'type': 'login',//这里必须为login
    'id': 'UID/邮箱/手机号',
    'password': '密码',
}
```
如果登录成功会返回状态码100，若失败请见上文中其他状态码
