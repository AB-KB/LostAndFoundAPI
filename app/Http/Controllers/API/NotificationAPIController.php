<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateNotificationAPIRequest;
use App\Http\Requests\API\UpdateNotificationAPIRequest;
use App\Models\Notification;
use App\Repositories\NotificationRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\NotificationResource;

/**
 * Class NotificationAPIController
 */
class NotificationAPIController extends AppBaseController
{
    /** @var  NotificationRepository */
    private $notificationRepository;

    public function __construct(NotificationRepository $notificationRepo)
    {
        $this->notificationRepository = $notificationRepo;
    }

    /**
     * Display a listing of the Notifications.
     * GET|HEAD /notifications
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $this->notificationRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            NotificationResource::collection($notifications),
            __('messages.retrieved', ['model' => __('models/notifications.plural')])
        );
    }

    /**
     * Store a newly created Notification in storage.
     * POST /notifications
     */
    public function store(CreateNotificationAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $notification = $this->notificationRepository->create($input);

        return $this->sendResponse(
            new NotificationResource($notification),
            __('messages.saved', ['model' => __('models/notifications.singular')])
        );
    }

    /**
     * Display the specified Notification.
     * GET|HEAD /notifications/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Notification $notification */
        $notification = $this->notificationRepository->find($id);

        if (empty($notification)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/notifications.singular')])
            );
        }

        return $this->sendResponse(
            new NotificationResource($notification),
            __('messages.retrieved', ['model' => __('models/notifications.singular')])
        );
    }

    /**
     * Update the specified Notification in storage.
     * PUT/PATCH /notifications/{id}
     */
    public function update($id, UpdateNotificationAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Notification $notification */
        $notification = $this->notificationRepository->find($id);

        if (empty($notification)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/notifications.singular')])
            );
        }

        $notification = $this->notificationRepository->update($input, $id);

        return $this->sendResponse(
            new NotificationResource($notification),
            __('messages.updated', ['model' => __('models/notifications.singular')])
        );
    }

    /**
     * Remove the specified Notification from storage.
     * DELETE /notifications/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Notification $notification */
        $notification = $this->notificationRepository->find($id);

        if (empty($notification)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/notifications.singular')])
            );
        }

        $notification->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/notifications.singular')])
        );
    }
}
