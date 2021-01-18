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
}