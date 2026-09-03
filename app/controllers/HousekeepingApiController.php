<?php

require_once __DIR__ . '/../models/Room.php';

class HousekeepingApiController
{
    public function roomStatus(): void
    {
        header('Content-Type: application/json');

        $roomModel = new Room();

        $rooms = $roomModel->getAllRooms();
        $statusCounts = $roomModel->getRoomStatusCounts();

        echo json_encode([
            'success' => true,
            'rooms' => $rooms,
            'statusCounts' => $statusCounts
        ]);
    }
}