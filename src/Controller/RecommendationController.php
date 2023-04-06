<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\RecommendationService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\RecommendedBookListResponse;

final class RecommendationController extends AbstractController
{
    public function __construct(private readonly RecommendationService $recommendationService)
    {
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns recommedations for the book",
     *
     *     @Model(type=RecommendedBookListResponse::class)
     * )
     * @OA\Tag(
     *     name="books"
     * )
     */
    #[Route(path: '/api/v1/book/{id}/recommendations', methods: 'GET')]
    public function recommendationByBookId(int $id): Response
    {
        return $this->json($this->recommendationService->getRecommendationById($id));
    }
}
