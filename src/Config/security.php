<?php
declare(strict_types=1);

return array(
    /**
     * 使用安全守卫
     * - true: 开启
     * - false: 关闭
     */
    'enable'    =>  true,

    /**
     * 模块 ( 模块 => 用户提供者 )
     */
    'module'    =>  [
//        'admin' =>  NiceYu\ThinkRouteSecurity\AdminUserProvider::class // 示例
    ]
);