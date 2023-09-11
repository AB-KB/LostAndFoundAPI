<?php

namespace App\Http\Controllers\API;

use App\Exceptions\ItemNotFoundException;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\RespondToMessage;
use App\Models\ItemMessageThread;
use App\Models\Message;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends AppBaseController
{

    public function adminsThreads()
    {

        $allThreads = ItemMessageThread::with(["item", "user", "lastMessage"])
            ->latest()
            ->get()
            ->map(function (ItemMessageThread $thread) {

            return [
                "id" => $thread->id,
                "item" => $thread->item->only(["id", "name", "type"]),
                "user" => $thread->user->only(["id", "name"]),
                "lastMessage" => $thread->lastMessage->only(["id", "text"]),
            ];
        });


        return $this->sendResponse($allThreads, __("Message threads"));
    }


    public function threadMessage(int $thread_id)
    {

        $thread = ItemMessageThread::find($thread_id);
        if (is_null($thread)) {

            throw new ItemNotFoundException(__("Thread not found"));
        }


        $messages = $thread->messages()->paginate();

        $messages->through(function (Message $message) {

            return [
                "id" => $message->id,
                "is_from_admin" => $message->is_from_admin,
                "text" => $message->text,
                "date" => $message->created_at->diffForHumans()
            ];
        });

        return $this->sendPaginatedResponse($messages, __("Messages on thread"));
    }


    public function respondToMessage(RespondToMessage $request, $thread_id)
    {

        try {
            /** @var User */
            $user = Auth::user();

            $thread = ItemMessageThread::find($thread_id);
            if (is_null($thread)) {

                throw new ItemNotFoundException(__("Message not found"));
            }


            $thread->messages()->create([
                "text" => $request->message,
                "is_from_admin" => $user->isAdmin(),
                "admin_read"=> $user->isAdmin(),
                "normal_user_read"=> !$user->isAdmin(),
            ]);


            return $this->sendSuccess(__("Reply was sent"));
        } catch (Exception $th) {

            return $this->sendExceptionError($th);
        }
    }
}
