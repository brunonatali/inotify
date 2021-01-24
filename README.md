# Inotify

Inotify is a filesystem monitor event based. This lib aim to be file change notification within PHP React.

**Table of Contents**
* [Before start](#before-start)
* [Quickstart example](#quickstart-example)
* [Register / Deregister](#de-register) 
    * [add()](#add-f)
    * [remove()](#remove-f)
    * [stopAll()](#stopall-f)
    * [Events](#events)
* [General](#general)
    * [getAll()](#getall-f)
    * [getLastError()](#lasterror-f)
* [Supported flags](#flags)
* [Install](#install)
* [License](#license)

## Before start
BEFORE BEGIN - You must install [inotify extension](https://www.php.net/manual/en/inotify.install.php) to make this library work.  
After installation, you must place new extension config to your php.ini file, if extension was not declared, system will try to load automatically (using [dl()](https://www.php.net/manual/en/function.dl.php)) and throws an exception if was unable to proceed.  
Follow quickstart example and use try-catch method to build this class.

## Quickstart example
```php
use BrunoNatali\Inotify\Factory;
use React\EventLoop\Factory as LoopFactory;

$loop = LoopFactory::create();
try {
    $notify = new Factory($loop);
} catch ($e \Exception) {
    /**
     * Exception codes:
     * EXCEPTION_EXTENSION_LOAD -> Extension is not loaded
     * EXCEPTION_EXTENSION_INIT -> inotify initialization error
    */
}

$filePathName = __FILE__;

$notify->add($filePathName, IN_ATTRIB);
$notify->on(IN_ATTRIB, function () {
	echo "File touched";
});
```

## Register / Deregister
This lib is driven by Événement and so after add() you must register an [event](#events) to catch.  
Follow to the next functions and examples.

### add()
To start watch some file, just call add(string $fileName, int [$flags](#flags)) :
```php
$notify->add('/some/path/file.name', IN_ATTRIB | IN_MODIFY | IN_ACCESS);
```
Note. You can concatenate flags as exaple above.  
Returns true if sucessfully added

### remove()
The remove way is simple as add
```php
$notify->remove('/some/path/file.name');
```
Returns true if removed or not exists

### stopAll()
This will remove() all watchers.  
This function needs no parmans an returns nothing  
Note. Additionaly you can call [getAll()](#getall-f) and stop one by one.

### Events
To receive any registered event you must register usin on([FLAG_NAME](#flags), callable):
```php
$notify->on(FLAG_NAME, function () {
	// Do some stuff
});
```
If you plan to get noticed when any mask was triggered, use "all" (lowercase) as FLAG_NAME.  
  
After registered, when watcher fires an event program will call your function. Ex.
```php
$notify->add('/some/path/file.name', IN_ATTRIB | IN_MODIFY | IN_ACCESS);
$notify->on(IN_ACCESS, function () {
	echo "My file was accessed"
});
```
And this will fire access event causing your code to telling you that file was accessed:
```shell
cat /some/path/file.name > /dev/null
```

## General
### getAll()
Returns an array containing all registered files to watch in this structure:
```php
[
    "somepathfile.name" => (int) unique inotify instance wide watch descriptor,
    "otherpathfile.ext" => (int) another unique inotify instance
]
```

### getLastError()
In most of cases, this functions, will not throw an exception, but will return false. To let you best track the reson why fail to do something this lib will provide textual error, just call getLastError() right after failure.

## Supported flags
Some function explained above needs flag to tell them when trigger an event or how to handle triggered event.  
This flags was called by developer as constants and originally posted [here](https://www.php.net/manual/en/inotify.constants.php).    
If you want to know its value, follow this list:  
IN_ACCESS = 1  
IN_MODIFY = 2  
IN_ATTRIB = 4  
IN_CLOSE_WRITE = 8  
IN_CLOSE_NOWRITE = 16  
IN_OPEN = 32  
IN_MOVED_FROM = 64  
IN_MOVED_TO = 128  
IN_CREATE = 256  
IN_DELETE = 512  
IN_DELETE_SELF = 1024  
IN_MOVE_SELF = 2048  
IN_UNMOUNT = 8192  
IN_Q_OVERFLOW = 16384  
IN_IGNORED = 32768  
IN_CLOSE = 24  
IN_MOVE = 192  
IN_ALL_EVENTS = 4095  
IN_ONLYDIR = 16777216  
IN_DONT_FOLLOW = 33554432  
IN_MASK_ADD = 536870912  
IN_ISDIR = 1073741824  
IN_ONESHOT = 4294967295  

## Install

The recommended way to install this library is [through Composer](https://getcomposer.org).
[New to Composer?](https://getcomposer.org/doc/00-intro.md)

This project follows [SemVer](https://semver.org/).
This will install the latest supported version:

```bash
$ composer require brunonatali/inotify:^0.1
```

This project aims to run on Linux and require [inotify PHP extensions](https://pecl.php.net/package/inotify), but actually not tested in all environments. If you find a bug, please report.


## License

MIT, see [LICENSE file](LICENSE).
