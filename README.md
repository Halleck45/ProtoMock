ProtoMock
=====

Allow to mock your requests.

```php
$mock = new ProtoMock();
$mock->enable('file');
$mock->enable('http');

$mock
    ->with('http://my-website.fr')
    ->will('I am a mocked response');

// ...

echo file_get_contents('http://my-website.fr');
// I am a mocked response
```

That's only a POC for the moment. Feel free to contribute if you are interested.

Why ?
-----

Because legacy code exists...

(and also because my train is late, and I have one hour to kill...)

Usage
-----

**Enabling / disabling mocking on protocol**

```php
$mock->enable('http');
// disabling
$mock->disable('http');
```
    
**Mocking a resource**

```php
$mock->with(<path>)->will('wanted response');
// diabling
$mock->without(<path>)
```

Requirements
-----

PHP >= 7

License
-----

MIT. See the LICENSE file