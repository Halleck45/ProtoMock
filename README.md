ProtoMock
=====

Allow to mock your requests.

```php
$mock = new ProtoMock();
$mock->enable('file');
$mock->enable('http');

$mock
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
$mocked = $mock->with(<path>)->will('wanted response');
$mock->without($mocked)
```

**Mocking a resource by regex**

```php
$mock->matching(<regex>)->will('wanted response');

// example
$mock->matching('!.*\.txt!')->will('wanted response');
```

**Mocking a resource by patch (case insensitive)**

```php
$mock->with($path, Mock::MATCHING_EXACT_CASE_INSENSITIVE)->will('wanted response');
```


**Using a function for response**

```php
// you can use any callable

$mock->with('/my/file1.txt')->will(function($path) {
    return 'I a a mock. Current path is ' . $path;
});
```

**Cancelling all**

```php
// you can use any callable

$mock->reset();
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