# Fortnite-PHP Wrapper
Interact with the official Fortnite API using PHP.

[![Packagist](https://img.shields.io/packagist/l/doctrine/orm.svg)]()
[![Packagist](https://img.shields.io/packagist/v/Tustin/fortnite-php.svg)]()

## Installation
Pull in the project using composer:
`composer require tustin/fortnite-php`

## Usage
Create a basic test script to ensure everything was installed properly
```php
<?php

require_once 'vendor/autoload.php';

use Fortnite\Auth;

$auth = Auth::login('epic_email@domain.com','password');
var_dump($auth->profile->stats);

// or grab someone's stats

$sandy = $auth->profile->stats->lookup('sandalzrevenge');
echo 'Sandy Ravage has won ' . $sandy->pc->solo->wins . ' solo games and ' . $sandy->pc->squad->wins . ' squad games!';
```

This should output your Fortnite stats as a PHP object.
