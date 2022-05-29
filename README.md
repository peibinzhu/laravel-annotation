Laravel DI组件
=======

在 Laravel 容器的基础上，实现注解收集等功能。建议在常驻内存环境下使用

## 安装

运行下面命令进行安装

```sh
composer require peibin/laravel-di
```

## 注解

参照 [hyperf/di](https://github.com/hyperf/di) 框架的注解功能，提供一个注解收集器，建议配合 [laravel/octane](https://github.com/laravel/octane) 使用

### 配置文件

```php
<?php

declare(strict_types=1);

return [
    'scan'           => [
        'paths'              => [
            base_path('app'),
        ],
        'ignore_annotations' => [
            'mixin',
        ],
    ],
    'scan_cacheable' => env('SCAN_CACHEABLE', false),
];
```

如配置文件不存在可执行 `php artisan vendor:publish --tag annotation-config` 命令来生成。

### 使用方法

```php
<?php

use Illuminate\Contracts\Foundation\Application;
use PeibinLaravel\Di\Annotation\ScanConfig;
use PeibinLaravel\Di\Annotation\Scanner;
use PeibinLaravel\Di\ScanHandler\PcntlScanHandler;

$config = ScanConfig::instance($this->app);
$handler = new PcntlScanHandler();
(new Scanner($config, $handler))->scan();
```

建议上面代码放置在 `AppServiceProvider` 或其他只加载运行一次地方，避免重复扫描，影响性能。

扫描器扫描完毕后会将注解数据收集到 `AnnotationCollector`，关于注解的用法可参考 [hyperf注解](https://hyperf.wiki/2.2/#/zh-cn/annotation)。

注：目前注解组件只支持php8的注解方式

### 目前功能进度

- [x] 类注解
- [x] 方法注解
- [ ] 属性注解
- [x] 扫描器缓存
