<?php

namespace App\Tests\Service;

use App\Repository\ReviewRepository;
use App\Service\RatingService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

final class RatingServiceTest extends TestCase
{
    private ReviewRepository $reviewRepository;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->reviewRepository = $this->createMock(ReviewRepository::class);
    }

    public static function provider(): array
    {
        return [
            [25, 20, 1.25],
            [0, 5, 0],
        ];
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     *
     * @dataProvider provider
     */
    public function testCalcReviewRatingFroBook(int $repositoryRatingSum, int $total, float $expectedRating): void
    {
        $this->reviewRepository
            ->expects($this->once())
            ->method('getBookTotalRatingSum')
            ->with(1)
            ->willReturn($repositoryRatingSum)
        ;

        $actual = (new RatingService($this->reviewRepository))->calcReviewRatingFroBook(1, $total);

        $this->assertEquals($expectedRating, $actual);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function testCalcReviewRatingFroBookZeroTotal(): void
    {
        $this->reviewRepository
            ->expects($this->never())
            ->method('getBookTotalRatingSum')
        ;

        $actual = (new RatingService($this->reviewRepository))->calcReviewRatingFroBook(1, 0);

        $this->assertEquals(0, $actual);
    }
}
