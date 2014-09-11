customerio-client
=================
A lightweight client to deal with the REST endpoints of customer.io (which are write-only).

More information: [customer.io rest documentation](http://customer.io/docs/api/rest.html)

## Installation

Use composer.

## Usage

Make sure that you use autoloader and the proper namespace.

```
require 'vendor/autoload.php';
use Shutterstock\CustomerIO\Client;
```

The only parameters to pass in with construct is your site id and secret key. Unless you have global CURL options, than pass that in as an optional third array parameter.

```
$client = new Client($site_id, $site_key);
```

Then just use it. Here is a list of the four main things you can do.

```
$client->createCustomer($user_id, $user_email, (optional) $user_attributes);
$client->updateCustomer($user_id, $user_email, (optional) $user_attributes);
$client->deleteCustomer($user_id);
$client->trackEvent($user_id, $event_name, (optional) $data);
```

These four methods are well documented in the class. Oh, and any failures will throw custom, verbose exceptions, so you may want to wrap any calls.
