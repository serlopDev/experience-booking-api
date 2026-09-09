<?php

namespace App\Reservation\Application\CancelReservation;

use App\Reservation\Application\Notification\ReservationEmailSender;
use App\Reservation\Domain\Exception\ReservationNotFound;
use App\Reservation\Domain\Repository\ReservationRepository;
use App\Reservation\Domain\Reservation;
use App\Session\Domain\Exception\SessionNotFound;
use App\Session\Domain\Repository\SessionRepository;
use App\Shared\Application\TransactionManager;
use DateTimeImmutable;

final readonly class CancelReservation
{
    public function __construct(
        private TransactionManager $transactionManager,
        private ReservationRepository $reservationRepository,
        private SessionRepository $sessionRepository,
        private ReservationEmailSender $emailSender,
    ) {}

    public function execute(
        string $reservationId,
        DateTimeImmutable $now,
    ): Reservation {
        $reservation = $this->transactionManager->run(
            function () use ($reservationId, $now): Reservation {
                $reservation = $this->reservationRepository
                    ->findByIdForUpdate($reservationId);

                if ($reservation === null) {
                    throw new ReservationNotFound('Reservation not found.');
                }

                $session = $this->sessionRepository
                    ->findByIdForUpdate($reservation->sessionId());

                if ($session === null) {
                    throw new SessionNotFound('Session not found.');
                }

                $reservation->cancel(
                    sessionStartsAt: $session->startsAt(),
                    now: $now,
                );

                $session->releaseSeats($reservation->seats());

                $this->reservationRepository->save($reservation);
                $this->sessionRepository->save($session);

                return $reservation;
            },
        );
        $this->emailSender->sendReservationCancelled($reservation);

        return $reservation;
    }
}
