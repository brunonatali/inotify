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
$fileContent = null;
$loopTimer = $loop->addPeriodicTimer(5.0, function () use ($loop, $notify, &$touchCounter, &$loopTimer, &$fileContent) {
	switch ($touchCounter) {
		case 1:
			if ($fileContent)
				@\file_put_contents(__FILE__, $fileContent);
			else
				\touch(__FILE__); // Just touch again
			break;
		case 2:
			$fileContent = @\file_get_contents(__FILE__);
			break;
		case 3:
			\touch(__FILE__);
			break;
		default:
			$loop->cancelTimer($loopTimer);
			$notify->remove(__FILE__);
			$loop->futureTick(function () use ($loop) {
				$loop->stop();
			});
		break;
	}

	if (!--$touchCounter) {
		$loop->cancelTimer($loopTimer);
		$notify->remove(__FILE__);
		$loop->futureTick(function () use ($loop) {
			$loop->stop();
		});
	}
});

$notify->on(IN_ATTRIB, function () {
	var_dump("File (IN_ATTRIB) " . __FILE__ . " touched!");
});

$notify->on(IN_MODIFY, function () {
	var_dump("File (IN_MODIFY) " . __FILE__ . " rewrited!");
});

$notify->on(IN_ACCESS, function () {
	var_dump("File (IN_ACCESS) " . __FILE__ . " readed!");
});

$loop->run();