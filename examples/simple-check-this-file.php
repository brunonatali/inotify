<?php declare(strict_types=1);

use BrunoNatali\Inotify\Factory;

use React\EventLoop\Factory as LoopFactory;

// Include autoload
try {
    if (!@include_once(__DIR__ . '/../vendor/autoload.php'))
        // Case running directelly from this examples folder
		if (!@include_once(__DIR__ . '/../../../autoload.php')) 
			throw new Exception('Could not find autoload.php');
} catch (Exception $e) {
	echo $e->getMessage();
}

$loop = LoopFactory::create();

$notify = new Factory($loop);

$notify->add(__FILE__, IN_ATTRIB);

$loop->run();