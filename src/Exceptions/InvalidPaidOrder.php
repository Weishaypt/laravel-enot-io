<?php

namespace Weishaypt\EnotIo\Exceptions;

use Throwable;

class InvalidPaidOrder extends \Exception
{
    /**
     * InvalidPaidOrder constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, $previous = null)
    {
        if (empty($message)) {
            $message = 'EnotIo config: paidOrder callback not set';
        }

        parent::__construct($message, $code, $previous);
    }
}
