# WeChat Collector(微信文章采集)
Tencent WeChat public platform article collector. 微信文章采集，微信公众号采集，自动采集。

## Description 

Tencent WeChat public platform article collector. WeChat article collected, all history articles of WeChat public number(Official Accounts) service account collected, automatic collected.

*   Article collection. Input the url of the WeChat public number's article ,it would be collected into original article, with types of wordpress-style or wechat-style.
*   WeChat public number collection.Input the WeChat number of the service account,it would be searched for all of its historical articles' title and url.
*   Automatic batch collection.  Select multiple articles and automatic batch collect into original article.

腾讯微信公众号文章采集器。微信文章采集，微信公众号服务号全部历史文章采集，批量自动采集

*   文章采集，输入微信文章地址，自动抓取标题和内容，伪原创生成到自己的网站中。可生成两种格式，wordpress文章格式或者微信文章原版格式。
*   微信公众号采集，输入微信公众号服务号，自动抓取全部的发布过的文章的名称和链接。
*   自动批量采集，通过勾选选择多项需要的文章，系统进行自动的批量采集至本地。

**可以采集文章和图片，图片可以本地化。采集后的文章，以普通博文形式自动发表，或者保留原始微信文章格式进行本地化。**

## Installation

1. Upload the plugin files to the `/wp-content/plugins/wxcaiji_i3geek` directory, or install the plugin through the WordPress plugins screen directly.
2. Set the `/` root directory write permission (777). *Or* create folder `/reading` in root directory and set write permission (777).
3. Activate the plugin through the 'Plugins' screen in WordPress
4. Use the Settings->WeChat Collector screen to configure the plugin

安装：

1. 上传插件全部文件到`/wp-content/plugins/wxcaiji_i3geek`目录，或者在wordpress插件中心进行安装。
2. 设置跟目录`/`写权限（777），或者在根目录下创建文件夹`/reading`并设置好该文件夹的写权限（777）。
3. 进入后台开启插件。
4. 通过设置->微信文章采集进行设置或使用插件。

**详细步骤请关注：http://www.i3geek.com/archives/997**

## Frequently Asked Questions 

### 没有写权限 

在网站根目录创建空文件夹`reading`并设置777写权限。或者将网站根目录设置成777写权限。

### 无响应 

一般情况下公众号采集一次在10秒左右。文章采集在3秒左右。若同时采集多篇文章，时间会累积，请不要频繁提交，建议等待。

### 其他 

**更多问题可以到论坛提问：http://bbs.i3geek.com**

## Screenshots 

1. `/assets/screenshot-1.png`
2. `/assets/screenshot-2.png`
3. `/assets/screenshot-3.png`
4. `/assets/screenshot-4.png`
5. `/assets/screenshot-5.png`
6. `/assets/screenshot-6.png`

## Changelog

### 1.1
修改由于超时导致的错误

### 1.0
* 增加文章采集功能
* 增加采集类型
* 增加公众号采集
* 对显示结果进行优化整理
* 对BUG进行修复

## Upgrade Notice

### 1.1 
