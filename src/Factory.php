<?php declare(strict_types=1);

namespace BrunoNatali\Inotify;

// use BrunoNatali\Tools\OutSystemInterface;

use React\EventLoop\LoopInterface;
use Evenement\EventEmitter;

/**
 * Basically post database to webserver 
*/
class Factory extends EventEmitter implements FactoryInterface
{
    /**
     * @var LoopInterface
    */
    private $loop;

    /**
     * @var resource|false
    */
    private $mainResource = false;

    /**
     * @var array
    */
    private $watchingList = [];

    /**
     * @var string stores last error
    */
    private $lastError = null;

    /**
     * Constructor automatic initialize extension if need and instantiate
     * Inotify
     * 
     * Note. This construction may throw exceptions, initialize this class
     * by placing an try-cath and use exception constants to automatic track errors
     * 
     * @param LoopInterface $loop React PHP EventLoop
    */
    function __construct(LoopInterface &$loop)
    {
        $this->loop = $loop;

        if (\extension_loaded('inotify') === false) 
            if (\function_exists('dl'))
                try {
                    if (!\dl('inotify') || !\function_exists('inotify_init'))
                        throw new \Exception(
                            'Unable to automatic load inotify extension, enable it manually', 
                            self::EXCEPTION_EXTENSION_LOAD
                        );
                } catch (\Exception $e) {
                    throw new \Exception('inotify extension autoload reports error: ' . 
                        $e->getMessage() . '. Enable it manually', self::EXCEPTION_EXTENSION_LOAD);
                }
            else
                throw new \Exception('Enable inotify extension first', self::EXCEPTION_EXTENSION_LOAD);

        try {
            $this->startup();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Adds a file on a inotify watch instance
     * 
     * @param string $fileName File path and name to watch
     * @param int $flags Mask events to rack
     * 
     * @return bool Returns false is something is wrong. Catch error by getLastError()
    */
    public function add(string $fileName, int $flags): bool
    {
        $myWatchName = \str_replace('/', '', $fileName);

        if (isset($this->watchingList[$myWatchName]))
            // Instead function expects file name, passing an parsed string
            //  make even faster as reparse
            $this->remove($myWatchName); 

        try {
            $this->watchingList[$myWatchName] = \inotify_add_watch($this->mainResource, $fileName, $flags);
            return true;
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
        }

        return false;
    }

    /**
     * Removes filename from inotify watch instance
     * 
     * @param string $fileName File path and name
     * @return bool Removed or not
    */
    public function remove(string $fileName): bool
    {
        $myWatchName = \str_replace('/', '', $fileName);

        if (!isset($this->watchingList[$myWatchName]))
            return true;

        return \inotify_rm_watch($this->mainResource, $this->watchingList[$myWatchName]);
    }

    /**
     * Returns last unthrowed error message
     * 
     * @return string
    */
    public function getLastError(): string
    {
        return $this->lastError;
    }

    /**
     * Used to initialize inotify during contruction or weakup
     * 
     * Note. Function may may throw exceptions, use try-catch
     * 
     * @return void
    */
    private function startup(): void
    {
        try {
            if (!\is_resource($this->mainResource = \inotify_init()))
                throw new \Exception('Something went wrong with inotify initialization', self::EXCEPTION_EXTENSION_INIT);

            if (!\stream_set_blocking($this->mainResource, false))
                throw new \Exception('Main resource could not get into no-block', self::EXCEPTION_EXTENSION_INIT);

            $this->loop->addReadStream($this->mainResource, [$this, 'onReadEvent']);

        } catch (\Exception $e) {
            throw new \Exception('inotify initialization reports error: ' . $e->getMessage(), self::EXCEPTION_EXTENSION_LOAD);
        }
    }

    private function onReadEvent()
    {
        if (($events = \inotify_read($this->mainResource)) !== false) {
            foreach ($events as $event) {
                var_dump($event);
                // $this->emit($event['mask'], array($path . $event['name']));
            }
        }
    }

}