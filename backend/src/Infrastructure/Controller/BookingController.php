<?php

namespace App\Infrastructure\Controller;

use App\Application\Service\BookingService;
use App\Domain\Model\CommonSpace;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/api/spaces', name: 'get_spaces', methods: ['GET'])]
    public function getSpaces(): JsonResponse
    {
        // Hardcodeado para la prueba, en producción vendría de base de datos
        $spaces = [
            ['id' => 'padel', 'name' => 'Padel Court'],
            ['id' => 'pool', 'name' => 'Swimming Pool'],
            ['id' => 'gym', 'name' => 'Gym']
        ];

        return $this->json($spaces);
    }

    #[Route('/api/availability', name: 'get_availability', methods: ['GET'])]
    public function getAvailability(
        Request $request,
        BookingService $bookingService
    ): JsonResponse {
        $spaceId = $request->query->get('space');
        $dateString = $request->query->get('date');

        try {
            $date = new \DateTimeImmutable($dateString);
            $space = new CommonSpace($spaceId);

            $availability = $bookingService->checkAvailability($space, $date);

            return $this->json($availability);
        } catch (\Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route('/api/bookings', name: 'create_booking', methods: ['POST'])]
    public function createBooking(
        Request $request,
        BookingService $bookingService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        try {
            $space = new CommonSpace($data['space']);
            $date = new \DateTimeImmutable($data['date']);
            $startTime = new \DateTimeImmutable($data['date'] . ' ' . $data['time'] . ':00');
            $endTime = $startTime->modify('+1 hour');

            $booking = $bookingService->createBooking($space, $date, $startTime, $endTime);

            return $this->json([
                'status' => 'success',
                'bookingId' => $booking->getId()
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
