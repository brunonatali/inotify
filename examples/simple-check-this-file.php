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

$notify->add(__FILE__, IN_ATTRIB | IN_MODIFY | IN_ACCESS);

$touchCounter = 3;
$loopTimer = null;
$loopTimer = $loop->addPeriodicTimer(5.0, function () use ($loop, $notify, &$touchCounter, &$loopTimer) {
	\touch(__FILE__);

	if (!--$touchCounter) {
		$loop->cancelTimer($loopTimer);
		$notify->remove(__FILE__);
	}
});

$loop->run();