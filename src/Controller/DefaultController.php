<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(private BookRepository $bookRepository)
    {
    }

    #[Route('/root')]
    public function root(): Response
    {
        $books = $this->bookRepository->findAll();

        return $this->json($books);
    }
}
