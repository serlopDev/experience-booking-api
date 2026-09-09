<?php

namespace App\Reservation\Application\CreateReservation;

use App\Reservation\Application\Notification\ReservationEmailSender;
use App\Reservation\Domain\Repository\ReservationRepository;
use App\Reservation\Domain\Reservation;
use App\Session\Domain\Exception\SessionNotFound;
use App\Session\Domain\Repository\SessionRepository;
use App\Shared\Application\TransactionManager;
use DateTimeImmutable;

final readonly class CreateReservation
{
    public function __construct(
        private TransactionManager $transactionManager,
        private SessionRepository $sessionRepository,
        private ReservationRepository $reservationRepository,
        private ReservationEmailSender $emailSender,
    ) {}

    public function execute(
        string $id,
        string $sessionId,
        string $userId,
        string $contactEmail,
        DateTimeImmutable $now,
        int $seats
    ): Reservation {
        $reservation = $this->transactionManager->run(
            function () use (
                $id,
                $sessionId,
                $userId,
                $contactEmail,
                $now,
                $seats
            ): Reservation {
                $session = $this->sessionRepository->findByIdForUpdate($sessionId);

                if ($session === null) {
                    throw new SessionNotFound('Session not found');
                }

                $session->reserveSeats($seats, $now);

                $reservation = Reservation::create(
                    id: $id,
                    sessionId: $sessionId,
                    userId: $userId,
                    contactEmail: $contactEmail,
                    seats: $seats,
                    unitPriceInCents: $session->priceInCents(),
                );

                $this->sessionRepository->save($session);

                $this->reservationRepository->save($reservation);

                return $reservation;
            }
        );

        $this->emailSender->sendReservationCreated($reservation);

        return $reservation;
    }
}
