<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\BookListResponse;
use App\Service\BookService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\ErrorResponse;
use App\Model\BookDetails;

final class BookController extends AbstractController
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
     *
     * @OA\Response(
     *     response=404,
     *     description="book category not found",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\Tag(
     *     name="books"
     * )
     */
    #[Route(path: '/api/v1/category/{id}/books', methods: 'GET')]
    public function booksByCategory(int $id): Response
    {
        return $this->json($this->bookService->getBookByCategory($id));
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Return book deatil informations",
     *
     *     @Model(type=BookDetails::class)
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="book not found",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\Tag(
     *     name="books"
     * )
     */
    #[Route(path: '/api/v1/book/{id}', methods: 'GET')]
    public function booksById(int $id): Response
    {
        return $this->json($this->bookService->getBookById($id));
    }
}
