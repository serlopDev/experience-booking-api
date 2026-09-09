<?php

use App\Experience\Domain\Exception\ExperienceNotFound;
use App\Reservation\Domain\Exception\InvalidReservation;
use App\Session\Domain\Exception\InvalidSession;
use App\Session\Domain\Exception\SessionAlreadyExistsForDate;
use App\Session\Domain\Exception\SessionNotFound;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (
            ExperienceNotFound $exception,
            Request $request,
        ) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        });

        $exceptions->render(function (
            SessionNotFound $exception,
            Request $request,
        ) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        });

        $exceptions->render(function (
            SessionAlreadyExistsForDate $exception,
            Request $request,
        ) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_CONFLICT);
        });

        $exceptions->render(function (
            InvalidSession $exception,
            Request $request,
        ) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $exceptions->render(function (
            InvalidReservation $exception,
            Request $request,
        ) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });
    })->create();
