<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exceptopn\BookCategoryNotFoundException;
use App\Model\BookListResponse;
use App\Service\BookService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    public function __construct(private readonly BookService $bookService)
    {
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Return Books by Categories",
     *
     *     @Model(type=BookListResponse::class)
     * )
     */
    #[Route(path: '/api/v1/category/{id}/books', methods: 'GET')]
    public function booksByCategory(int $id): Response
    {
        try {
            return $this->json($this->bookService->getBookByCategory($id));
        } catch (BookCategoryNotFoundException $exception) {
            throw new \HttpException($exception->getMessage(), $exception->getCode());
        }
    }
}
