<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class SubscriberAlreadyException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('subscriber already exists');
    }
}
