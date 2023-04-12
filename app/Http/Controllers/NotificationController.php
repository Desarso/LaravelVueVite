<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\NotificationRepository;

class NotificationController extends Controller
{
    protected $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function getNotifications(Request $request)
    {
        return $this->notificationRepository->getNotifications($request);
    }

    public function getListNotificationAPP(Request $request)
    {
        return $this->notificationRepository->getListNotificationAPP($request);
    }

    public function getNotificationNotRead(Request $request)
    {
        return $this->notificationRepository->getNotificationNotRead($request);
    }

    public function setNotificationsRead(Request $request)
    {
        return $this->notificationRepository->setNotificationsRead($request);
    }

    public function readNotifications(Request $request)
    {
        return $this->notificationRepository->readNotifications($request);
    }
}