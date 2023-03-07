<?php
declare(strict_types=1);

return array(
    /**
     * 使用注解路由 (true | false)
     * true: 开启
     * false: 关闭
     */
    'enable'    =>  true,

    /**
     * 控制器路径, 多应用时记得配置
     * // think\facade\App::getAppPath(), // 单应用
     * // think\facade\App::getAppPath() . 'admin/',  //多应用
     */
    'controller'=>  [
//        think\facade\App::getAppPath(), // 单应用
    ],
);