# Image Captcha

[TOC]

## 关于扩展包

- 本扩展包基于 `intervention/image` 库。安装、使用条件与 `intervention/image` 库相同。

    `intervention/image` 库默认使用 `gd` 驱动来创建图片，如需更改驱动，修改 `intervention/image` 的配置文件既可。

    `intervention/image` 库：

    官网：[http://image.intervention.io](http://image.intervention.io)
    
    GitHUb：[https://github.com/Intervention/image](https://github.com/Intervention/image)

- **本扩展包使用的字体为：站酷文艺体。**

    **商业使用时，请确认您有此字体的使用权限。**
    
    **或者，你也可以在扩展包的配置文件中将字体替换为有使用权限的字体。**
    
    > 注：站酷文艺体的使用范围：免费授权全社会使用（包括商用）。详见官网：[https://www.zcool.com.cn/special/zcoolfonts](https://www.zcool.com.cn/special/zcoolfonts)
 
## 安装

### 安装条件

**无论是否在框架中使用，只要能使用 `intervention/image` 库的地方，均可安装使用本扩展包。**

- `intervention/image` >= 2

### 使用 Composer 安装

```shell
composer require ixianming/image-captcha
```

## 使用

### 配置文件

配置文件 `image_captcha.php` 存放在扩展包的 `config` 目录下。您可在配置文件中自定义图片验证码的设置。

如果你希望自定义验证码设置，且 `composer update` 时，自定义配置不受影响，请在自定义配置后及时备份，否则，当扩展包更新时，你的自定义配置就会被覆盖。

### 引用扩展包

```php
use Ixianming\ImageCaptcha\Captcha;
```

### 创建验证码实例

```php
$captcha = new Captcha();
```

如果你需要使用自定义配置，在配置文件中定义自定义配置后，创建实例时，传入自定义配置的名称既可。

```php
$customConfigName = 'myCaptcha'；
$captcha = new Captcha($customConfigName);
```

### 获取验证码字符串

一个实例无论调用 `code` 方法几次，返回的验证码都是一致的，直至销毁此验证码。

```php
$code = $captcha->code();
```

### 获取验证码图片的 Base64 编码

```php
$base64 = $captcha->base64();

//如需修改图片质量，传入 `$quality` 参数，参数范围 0 - 100，默认为 90
$quality = 75;
$base64 = $captcha->base64($quality);
```

### 获取验证码图片的编码数据

`$imgType` 参数表示图片格式，支持的图片格式与 `intervention/image` 库的 `encode` 方法支持的格式一致。默认返回 `base64` 编码数据。

```php
$encode = $captcha->encode(); //返回图片的 base64 编码数据

//如需指定图片的格式，传入 `$imgType` 参数。
$imgType = 'png';
$encode = $captcha->encode($imgType);

//如需修改图片质量，传入 `$quality` 参数，参数范围 0 - 100，默认为 90。
$imgType = 'png';
$quality = 75;
$encode = $captcha->encode($imgType, $quality);
```

### 销毁验证码

```php
$captcha->destroy();
```

### 响应验证码图片

`$imgType` 参数表示图片格式，支持的图片格式与 `intervention/image` 库的 `response` 方法支持的格式一致。默认响应为 `jpg` 格式。

```php
return $captcha->response(); //响应为 jpg 格式的图片

//如需指定图片的格式，传入 `$imgType` 参数。
$imgType = 'png';
return $captcha->response($imgType);

//如需修改图片质量，传入 `$quality` 参数，参数范围 0 - 100，默认为 90。
$imgType = 'png';
$quality = 75;
return $captcha->response($imgType, $quality);
```

## 许可证

本扩展包基于 [MIT license](https://opensource.org/licenses/MIT) 开源。
