# Laravel Debug Console

Lets you keep an eye on your laravel application without intefering with your application response.
Perfect if you are working with apis.

## Installation instructions

This package is still in development and not on packagist. To test it you can install it by adding it
has a repository to your composer.json file.

```json
"repositories": [
    {
        "type": "path",
        "url": "../laravel-debug-console-package"
    }
],
"require-dev": {
    "madewithlove/laravel-debug-console": "*"
},
"minimum-stability": "dev",
"prefer-stable": true
```

and then run `composer update`.

## Usage

Open your console and run `php artisan app:debug` it will update automatically on the next request.
