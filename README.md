## Supabase PHP Client

<div align="center">
	
[![Tests](https://github.com/codebysushil/supabase-php/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/codebysushil/supabase-php/actions/workflows/tests.yml)
![PHP](https://img.shields.io/badge/PHP-8.4%2B-777BB4?style=for-the-badge&logo=php)
![Supabase](https://img.shields.io/badge/Supabase-PostgreSQL-3ECF8E?style=for-the-badge&logo=supabase)

[![Latest Version](https://img.shields.io/packagist/v/sushilk/supabase.svg?style=flat-square)](https://packagist.org/packages/sushilk/supabase)
[![Total Downloads](https://img.shields.io/packagist/dt/sushilk/supabase.svg?style=flat-square)](https://packagist.org/packages/sushilk/supabase)
[![License](https://img.shields.io/packagist/l/sushilk/supabase.svg?style=flat-square)](LICENSE)

Modern, lightweight, fluent Supabase client for PHP.

</div>

---

### Features

- [x] Fluent Query Builder
- [x] Supabase REST API Support
- [x] Async Requests with Guzzle
- [x] Select, Insert, Update, Delete
- [x] Filters & Query Operators
- [ ] Authentication Support
- [x] Typed PHP 8.4+ Codebase
- [x] PSR Standards
- [x] Promise-based Async Support
- [x] Easy Integration with Laravel & Vanilla PHP

---

### Installation

Install via Composer:

```bash
composer require sushilk/supabase
```

---

### Quick start

```php
<?php

require_once(__DIR__ . '/vendor/autoload.php');

use Sushilk\Supabase\Client;
           
$client = new Client(
    'https://<project-id>.supabase.co',
    'apikey'
);

$result = $client->from('users')->select('name, email')->get();

var_dump($result);

```

### Author
**Sushil Kumar**
