# Laravel Debug Console

Use as an alternative to view collected information from [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar) package.
Usefull if you are building apis or running console commands.

## Installation

You can install the latest version via  [composer](https://getcomposer.org/):

```bash
composer require --dev madewithlove/laravel-debug-console
```

## Usage

Open your console and run `php artisan app:debug {screen}` you can choose from the following options (same there would be available by default on laravel debug bar):

- messages
- timeline
- exceptions
- route
- queries
- request
