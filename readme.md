# 简易留言后端

这是一个简易的留言后端，使用PHP编写，在不储存账户的情况下实现了账号登录。

## API文档

 [查询留言列表](#查询留言列表)

 [获取验证码](#获取验证码)

 [获取token](#获取token)

 [更新token](#更新token)

 [发送留言](#发送留言)

 [删除留言](#删除留言)

### 查询留言列表

> `GET` <https://your.domain/>

- **响应内容**

| 返回值 | 类型 | 说明 |
| ---- | ---- | ---- |
| empty | string | 空 |
| `json数据` | application/json | 留言内容 |

### 获取验证码

> `POST` <https://your.domain/login.php>

- **body参数**

| 参数名 | 类型 | 说明 |
| ---- | ---- | ---- |
| email | string | 邮箱 |
| device | string | 设备识别码 |

> #### 设备识别码
>
> 由设备随机生成，生成之后不要改变，否则token也会改变。

- **响应内容**

| 返回值 | 类型 | 说明 |
| ---- | ---- | ---- |
| code_please | string | 验证码发送成功 |
| too_frequent | string | 验证码请求过于频繁 |
| invalid | string | 无效请求 |

### 获取token

> `POST` <https://your.domain/login.php>

- **body参数**

| 参数名 | 类型 | 说明 |
| ---- | ---- | ---- |
| email | string | 邮箱 |
| device | string | 设备识别码 |
| code | string | 邮箱收到的验证码 |

- **响应内容**

| 返回值 | 类型 | 说明 |
| ---- | ---- | ---- |
| code_invalid | string | 验证码错误 |
| invalid | string | 无效请求 |
| `json数据` | application/json | token内容 |

> #### token内容
>
> ```json
> {
>     "token": "abcd",
>     "f_token": "efgh"
> }
> ```
>
> | 参数名 | 类型 | 说明 |
> | :---: | :---: | :---: |
> | token | string | 当前token，有效期是30天 |
> | f_token | string | 下一个30天的token |

### 更新token

> `POST` <https://your.domain/login.php`>

- **body参数**

| 参数名 | 类型 | 说明 |
| ---- | ---- | ---- |
| email | string | 邮箱 |
| device | string | 设备识别码 |
| token | string | 当前token |

- **响应内容**

| 返回值 | 类型 | 说明 |
| ---- | ---- | ---- |
| token_invalid | string | token错误 |
| invalid | string | 无效请求 |
| `json数据` | application/json | token内容 |

### 发送留言

> `POST` `https://your.domain/`

- **body参数**

| 参数名 | 类型 | 说明 |
| ---- | ---- | ---- |
| content | string | 留言内容 |
| email | string | 邮箱 |
| device | string | 设备识别码 |
| token | string | 当前token |

- **响应内容**

| 返回值 | 类型 | 说明 |
| ---- | ---- | ---- |
| ok | string | 发送成功 |
| invalid | string | 非法请求 |

### 删除留言

> `DELETE` <https://your.domain>

- **请求参数**

| 参数名 | 类型 | 说明 |
| ---- | ---- | ---- |
| email | string | 邮箱 |
| position | string | 删除的留言序号 |
| token | string | 当前token |
| device | string | 设备识别码 |

- **响应内容**

| 返回值 | 类型 | 说明 |
| ---- | ---- | ---- |
| ok | string | 删除成功 |
| invalid | string | 非法请求 |
