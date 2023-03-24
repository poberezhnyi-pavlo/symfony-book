<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Subscriber;
use App\Exception\SubscriberAlreadyException;
use App\Model\SubscriberRequest;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;

class SubscriberService
{
    public function __construct(
        private readonly SubscriberRepository $subscriberRepository,
        private EntityManagerInterface $em,
    ) {
    }

    public function subscribe(SubscriberRequest $subscriberRequest): void
    {
        if ($this->subscriberRepository->existsByEmail($subscriberRequest->getEmail())) {
            throw new SubscriberAlreadyException();
        }

        $subscriber = new Subscriber();
        $subscriber->setEmail($subscriberRequest->getEmail());

        $this->em->persist($subscriber);
        $this->em->flush();
    }
}
