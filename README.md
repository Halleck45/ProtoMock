ProtoMock
=====

![License](https://poser.pugx.org/protomock/protomock/license.svg)
![Build Status](https://github.com/Halleck45/ProtoMock/actions/workflows/ci.yml/badge.svg)

Allow to mock your requests.

```php
$protomock = new ProtoMock();
$protomock->enable('file');
$protomock->enable('http');

$protomock
    ->with('http://my-website.fr/hello')
    ->will('I am a mocked response');

// ...

echo file_get_contents('http://my-website.fr/hello');
// I am a mocked response
```

**That's only a POC**. Feel free to contribute if you are interested.

Why ?
-----

Because legacy code exists, and generally needs unit tests...

(and because my train is late (yes, I'm french), and I have one hour to kill...)

Usage
-----

**Installation**

```bash
composer require protomock/protomock
```

**Enabling / disabling mocking for given protocol**

```php
$protomock->enable('http'); // will capture all http://... requests

// disabling
$protomock->disable('http');
```
    
**Mocking a resource**

```php
$protomock
    ->with(<path>)
    ->will('wanted response');

// disabling
$mocked = $protomock->with(<path>)->will('wanted response');
$protomock->without($mocked)
```

**Mocking a resource by regex**

```php
$protomock
    ->matching(<regex>)
    ->will('wanted response');

// example
$protomock
    ->matching('!.*\.txt!')
    ->will('wanted response');
```

**Mocking a resource by path (case insensitive)**

```php
$protomock
    ->with($path, Mock::MATCHING_EXACT_CASE_INSENSITIVE)
    ->will('wanted response');
```


**Using a function for response**

```php
// you can use any callable

$protomock
    ->with('/my/file1.txt')
    ->will(function($path) {
        return 'I a a mock. Current path is ' . $path;
    });
```

**Expecting a failure as response**

```php
// will trigger a WARNING
$protomock
    ->with('/my/file1.txt')
    ->willFail();
```


**Expecting a failure as response (due to DNS resolution)**

```php
// will trigger a WARNING and wait for default_socket_timeout delay
$protomock
    ->with('/my/file1.txt')
    ->willFail();
```

**Cancelling all**

```php
$protomock->reset();
```


FAQ
-----

_ *"That's look magic ! This project must be so complex !"*

**nope**. It needs only 200 lines of code, including comments... I just use the `stream_wrapper_register` PHP function.

_ *"Can I use it for my unit tests?"*

**I don't know**. That's just a poc, but why not ?
 

Requirements
-----

- `PHP >= 7`

License
-----

MIT. See the LICENSE file
