# Laravel Debug Console

Use as an alternative to view collected information from [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar) package.
Useful if you are building apis or running console commands.

![php artisan app:debug queries](https://user-images.githubusercontent.com/3688705/41133243-9a32d442-6abd-11e8-9600-18c089440967.png)

## Installation

You can install the latest version via [composer](https://getcomposer.org/):

```bash
composer require --dev madewithlove/laravel-debug-console
```

## Usage

Open your console and run `php artisan app:debug [messages|timeline|exceptions|route|queries|request]` same options that are available by default on Laravel Debugbar.
