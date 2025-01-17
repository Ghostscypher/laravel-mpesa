---
title: Initiating Mpesa API
weight: 1
---

## Creating an authentication token

We assume that you have already installed the package and have the necessary credentials from Safaricom. If you don't have the credentials, you can get them by registering on the [Safaricom Developer Portal](https://developer.safaricom.co.ke/).

To create an authentication token, you need to call the `generateToken` method from the `Mpesa` facade. The method returns an array with the token and the expiry time.

```php
use Ghost\LaravelMpesa\Facades\Mpes

$token = Mpesa::generateToken();
```
