### 作用
使用php在命令行输出点阵字

### 效果图
![img.png](img.png)

### 使用
```shell
composer require leychan/cli-mesasge
```
`index.php`
```php
require __DIR__ . '/vendor/autoload.php';

$message = '123';

$cli_message = new \cliMessage\CliMessage();
$cli_message->setMessage($message);
$cli_message->setPerLineFontQuantity(6);
$cli_message->run();
```
```shell
php index.php
```

### 备注
务必使用等宽字体以及空心和实心字体的宽度请保持一致