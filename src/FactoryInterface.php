<?php

namespace BrunoNatali\Inotify;

interface FactoryInterface
{
    /**
     * @var int used on thrown exception when extension is not loaded
    */
    const EXCEPTION_EXTENSION_LOAD = 0xFF10;

    /**
     * @var int general inotify initialization error
    */
    const EXCEPTION_EXTENSION_INIT = 0xFF11;

    /**
     * Adds a file on a inotify watch instance
     * 
     * @param string $fileName File path and name to watch
     * @param int $flags Mask events to rack
     * 
     * @return bool Returns false is something is wrong. Catch error by getLastError()
    */
    public function add(string $fileName, int $flags): bool;

    /**
     * Removes filename from inotify watch instance
     * 
     * @param string $fileName File path and name
     * @return bool Removed or not
    */
    public function remove(string $fileName): bool;

    /**
     * @return array All watched files as ["index_name" => (int) watch instance]
    */
    public function getAll(): array;

    /**
     * Stop all wathcing instances
    */
    public function stopAll(): void;

    /**
     * Returns last unthrowed error message
     * 
     * @return string
    */
    public function getLastError(): string;

    /**
     * This function was designed to be called only by the flow / LoopEvent
     * Will emit event MASK received
     * 
     * Data example
     * array(4) {
     *      ["wd"]=>
     *      int(1)
     *      ["mask"]=>
     *      int(4)
     *      ["cookie"]=>
     *      int(0)
     *      ["name"]=>
     *      string(0) ""
     * }
    */
    public function onReadEvent(): void;
}