<?php
/**
 * @copyright © 2019 by GLV 
 * @project Cred GLV Plugin
 * @since 1.0
 */


namespace credglv\core;


use Throwable;

class NotfoundException extends \Exception
{
    /**
     * NotfoundException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        if (credglv()->logger) {
            credglv()->logger->error($message,(array) $this->getTrace());
        }
    }
}