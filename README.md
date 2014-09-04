Snoobi API PHP SDK

Installing via Composer
-----------------------

The recommended way to install Snoobi API PHP SDK is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, update your project's composer.json file to include Snoobi API PHP SDK:

```javascript
{
    "require": {
        "snoobi/snoobi-sdk-php": "dev-master"
    }
}
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

Usage
-----

Authenticate with your Snoobi api OAuth credentials:
```php
$api = new Snoobi\Client([
    'consumer_key' => 'YOUR_CONSUMER_KEY',
    'consumer_secret' => 'YOUR_CONSUMER_SECRET',
    'token' => 'YOUR_TOKEN',
    'token_secret' => 'YOUR_TOKEN_SECRET'
]);

```

Make API calls:
```php
// Get api status
$result = $api->get('health');

// Query data from api
$payload = array(
    "account" => "YOUR_ACCOUNT",
    "criteria" => false,
    "metrics" => array(
        "visitors",
        "conversion"
    ),
    "start_date" => "2013-05-01",
    "end_date" => "2013-06-07T23:59:59",
    "group_by" => array(
        "day"
    ),
    "filters" => array(
        array(
            "name" => "search_engine",
            "value" => "Google"
        )
    ),
    "limit" => array(
        "from" => 10,
        "to" => 20
    )
);
$api->post('data', $payload);
```

Tests
-----
To run tests execute:
```
vendor/bin/phpunit tests/tests.php
```
