<?php

namespace App\Tests\Service;

use App\Entity\Review;
use App\Model\Review as ReviewModel;
use App\Model\ReviewPage;
use App\Repository\ReviewRepository;
use App\Service\RatingService;
use App\Service\ReviewService;
use App\Tests\AbstractTestCase;
use PHPUnit\Framework\MockObject\Exception;

final class ReviewServiceTest extends AbstractTestCase
{
    private ReviewRepository $reviewRepository;
    private RatingService $ratingService;
    private const BOOK_ID = 1;

    private const PER_PAGE = 5;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->reviewRepository = $this->createMock(ReviewRepository::class);
        $this->ratingService = $this->createMock(RatingService::class);
    }

    public static function dataProvider(): array
    {
        return [
            [0, 0],
            [-1, 0],
            [-20, 0],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetReviewPageByBookIdInvalidPage(int $page, int $offset): void
    {
        $this->ratingService
            ->expects($this->once())
            ->method('calcReviewRatingFroBook')
            ->with(self::BOOK_ID, 0)
            ->willReturn(0.0)
        ;

        $this->reviewRepository
            ->expects($this->once())
            ->method('getPageByBookId')
            ->with(self::BOOK_ID, $offset, self::PER_PAGE)
            ->willReturn(new \ArrayIterator())
        ;

        $service = new ReviewService($this->reviewRepository, $this->ratingService);

        $expected = (new ReviewPage())
            ->setTotal(0)
            ->setPage(0)
            ->setRating(0)
            ->setPage($page)
            ->setPages(0)
            ->setPerPage(self::PER_PAGE)
            ->setItems([])
        ;

        $this->assertEquals($expected, $service->getReviewPageByBookId(self::BOOK_ID, $page));
    }

    public function testGetReviewPageByBookId(): void
    {
        $this->ratingService
            ->expects($this->once())
            ->method('calcReviewRatingFroBook')
            ->with(self::BOOK_ID, 1)
            ->willReturn(4.0)
        ;

        $entity = (new Review())
            ->setAuthors('tester')
            ->setContent('test Content')
            ->setCreatedAt(new \DateTimeImmutable('2020-10-10'))
            ->setRating(4.0)
        ;

        $this->setEntityId($entity, 1);

        $this->reviewRepository
            ->expects($this->once())
            ->method('getPageByBookId')
            ->with(self::BOOK_ID, 0, self::PER_PAGE)
            ->willReturn(new \ArrayIterator([$entity]))
        ;

        $service = new ReviewService($this->reviewRepository, $this->ratingService);

        $expected = (new ReviewPage())
            ->setTotal(1)
            ->setPage(0)
            ->setRating(4)
            ->setPage(1)
            ->setPages(1)
            ->setPerPage(self::PER_PAGE)
            ->setItems([
                (new ReviewModel())
                    ->setId(1)
                    ->setAuthors('tester')
                    ->setContent('test Content')
                    ->setCreatedAt(1602277200)
                    ->setRating(4.0),
            ])
        ;

        $this->assertEquals($expected, $service->getReviewPageByBookId(self::BOOK_ID, 1));
    }
}
