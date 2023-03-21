<?php

declare(strict_types=1);

namespace App\Exceptopn;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class BookCategoryNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('book category not found', Response::HTTP_NOT_FOUND, null);
    }
}
