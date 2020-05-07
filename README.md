## pmir2

Mir2传奇模拟游戏服务器,项目采用PHP开发,TCP基于SWOOLE扩展,底层框架基于Hyperf

=====

~~~
The current system is: Linux


        MMMMMMMM       MMMM    MMMM     MMM     MMMMMMMMM        MMMMM
        MMM   MMM      MMMM   MMMMM     MMM     MMM   MMMM      MMM MMM
        MMM    MMM     MMMM   MMMMM     MMM     MMM    MMM     MMM   MMM
        MMM    MMM     MMMMM  MMMMM     MMM     MMM    MMM           MMM
        MMM    MMM     MMMMM MMMMMM     MMM     MMM    MMM          MMMM
        MMM   MMM      MMMMM MMMMMM     MMM     MMM   MMMM          MMM
        MMMMMMMM       MMMMMMMM MMM     MMM     MMMMMMMM           MMMM
        MMM            MMMMMMMM MMM     MMM     MMM  MMMM         MMMM
        MMM            MMM MMMM MMM     MMM     MMM   MMM        MMMM
        MMM            MMM MMM  MMM     MMM     MMM    MMM      MMM
        MMM            MMM      MMM     MMM     MMM    MMM     MMMM
        MMM            MMM      MMM     MMM     MMM    MMMM    MMMMMMMMM
            
Server version 1.2.0
author by.fan <fan3750060@163.com>
[INFO] Worker#2 started.
[INFO] TaskWorker#5 started.
[INFO] TaskWorker#4 started.
[INFO] Worker#3 started.
[INFO] Worker#1 started.
[INFO] Worker#0 started.
[INFO] TCP Server listening at 0.0.0.0:7000
[INFO] Process[crontab-dispatcher.0] start.
[INFO] Process[queue.default.0] start.
加载地图 /home/2020_pmir/storage/Maps/GA2.mapp
加载完成 19.826506853104
数据初始化加载 商品:81 物品:1348 技能:105 地图:389 怪物:521 怪物巡逻:1838 NPC:301 任务:157 重新生成:5639 安全区:19
[2020-05-01 8:44:50 ERROR  ] 掉落分子错误 content: ﻿1/5 Gold 500; /home/2020_pmir/storage/Envir/Drops/蝎蛇3.txt line: 1; 

~~~

## Introduction
This is an online game simulator written in PHP.

The game client is based on online circulating code.

Can enter the game normally

The follow-up process is under development ...

The database file is in the root directory: sql / ..

Limited energy, welcome to submit a version, QQ group: 186510932 welcome to study together ~

Please download the game client in the group.

## 介绍
这是用PHP编写的网络游戏模拟器。

游戏客户端基于网上流传代码。

可以正常进入游戏

后续进程正在开发中......

数据库文件在根目录: sql/..

精力有限,欢迎提交版本,QQ群:186510932 欢迎一起研究~

游戏客户端请在群里下载(仅限于研究,禁止进行任何商业行为,任何不遵守我们规定的商业行为都与我们无关)


## 申明
注: 本模拟器为私人研究项目,以学习为目的,不进行任何商业项目的活动,任何人非法用于商业目的都与本项目无关

Pmir2 is an online game object server that has undergone extensive changes over time to optimize.
Improve and clean up codebase mechanics and functionality while improving the game.
It is completely open source; it encourages community involvement.
If you want to provide ideas or code, please visit the join group or send a pull request to our [Github Repository]

Https://github.com/fan3750060/pmir2

pmir2是一款网络游戏对象服务器,随着时间的推移而进行大量更改以进行优化,
在改进游戏的同时改进和清理代码库机制和功能。
它是完全开源的; 非常鼓励社区参与。
如果您想提供想法或代码，请访问加入交流群或向我们的[Github存储库]发出拉取请求

https://github.com/fan3750060/pmir2

## 安装及依赖 Installation dependency

git clone https://github.com/fan3750060/pmir2.git

    Php version >= 7.3.0

    Swoole version >= 4.4.0

    redis version >= 3.2.0

## 运行 Run
Linux:

运行游戏模拟器:

./cmd.sh restart

查看帮助:

./cmd.sh


## 操作指南
    1.环境依赖请自行安装

    2.服务器初始化占用内存较大,强烈建议设置php.ini中memory_limit大小,建议1024及以上
    memory_limit = 1024M

    3.安装数据库
    新建pmir2数据库,并将根目录中sql里的文件导入到数据库

    4.解压数据文件 ./storage/需要解压.7z
    解压后文件目录如下
    Configs
    Envir
    Maps

    5.配置ENV
    将根目录中.env.example复制为.env并更改其中配置

    6.下载游戏客户端
    链接：https://pan.baidu.com/s/1odTWKcOgLecFrDcOJnI-1w 提取码：0vos 
    
    7.绑定hosts,将mir.impyq.com绑定为你的ip
    xxx.xxx.xxx.xx mir.impyq.com
    
    也可以自己编译客户端,将客户端目录下Client/Settings.cs中76行 
    public static string IPAddress = "47.95.206.70" 中的ip修改为你服务器的ip
    (C#客户端及服务端源代码:https://github.com/fan3750060/mir2)

    8.启动服务器
    ./cmd.sh restart

    9.进行游戏
    
    注: 测试账户fanfan 密码fanfan

## 链接 Links

* [PHP](https://www.php.net/)
* [Swoole](https://www.swoole.com/)
* [Hyperf](https://github.com/hyperf/hyperf)
* [Mir2](https://github.com/Suprcode/mir2)
* [Mir2](https://github.com/cjlaaa/mir2)
* [Mirgo](https://github.com/yenkeia/mirgo)

感谢上面这些传奇开源游戏框架及扩展

## Demonstration 演示

![image](https://pictureblog.oss-cn-beijing.aliyuncs.com/15886104475eb0458fb0b80%20(1).png)










  



