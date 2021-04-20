<?php

namespace App\Http\Controllers;

use App\Models\EmailsList;
use App\Models\Invitation;
use App\Models\Poll;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Lumen\Routing\Controller;

class PollsController extends Controller
{
    public function getAllPolls()
    {
        /** @var User $creator */
        $creator = Auth::user();

        $polls = Poll::where('creator_id', $creator->id)->get();

        $result = [];
        foreach ($polls as $poll) {
            $resultPoll['id'] = $poll->id;
            $resultPoll['title'] = $poll->title;
            $resultPoll['startDate'] = $poll->startedAt;
            $resultPoll['endDate'] = $poll->endedAt;
            $resultPoll['emailListTitle'] = $poll->emailsList->title ?? '';
            $resultPoll['status'] = $poll->status();

            $result[] = $resultPoll;
        }

        return json_encode($result);
    }

    public function getPoll(int $id)
    {
        /** @var User $creator */
        $creator = Auth::user();

        $poll = Poll::where([
            'id' => $id,
            'creator_id' => $creator->id,
        ])->firstOrFail();

        $result['id'] = $poll->id;
        $result['title'] = $poll->title;
        $result['description'] = $poll->description;
        $result['shortDescription'] = $poll->short_description;
        $result['startDate'] = $poll->started_at;
        $result['endDate'] = $poll->ended_at;
        $result['question'] = $poll->question;
        $result['emailListId'] = $poll->emailsList->id ?? '';
        $result['status'] = $poll->status();

        return json_encode($result);
    }

    public function createDraftPoll(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'max:1500',
            'shortDescription' => 'max:350',
            'question.options' => 'min:2',
        ]);

        /** @var User $creator */
        $creator = Auth::user();

        $emailList = EmailsList::where([
            'id' => $request->emailListId,
            'owner_id' => $creator->id,
        ])->firstOrFail();

        $poll = Poll::create([
            'title' => $request->title,
            'description' => $request->description,
            'short_description' => $request->shortDescription,
            'started_at' => $request->startDate,
            'ended_at' => $request->endDate,
            'question' => $request->question,
            'emails_list_id' => $emailList->id,
            'creator_id' => $creator->id,
        ]);

        $result['id'] = $poll->id;
        $result['title'] = $poll->title;
        $result['description'] = $poll->description;
        $result['shortDescription'] = $poll->short_description;
        $result['startDate'] = $poll->started_at;
        $result['endDate'] = $poll->ended_at;
        $result['question'] = $poll->question;
        $result['emailListId'] = $poll->emails_list_id;
        $result['status'] = $poll->status();

        return response(json_encode($result), 201);
    }

    public function updateDraftPoll(Request $request, int $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'max:1500',
            'shortDescription' => 'max:350',
            'question.options' => 'min:2',
        ]);

        /** @var User $creator */
        $creator = Auth::user();

        $poll = Poll::where([
            'id' => $id,
            'creator_id' => $creator->id,
            'published_at' => null,
        ])->firstOrFail();

        $emailList = EmailsList::where([
            'id' => $request->emailListId,
            'owner_id' => $creator->id,
        ])->firstOrFail();

        $poll->title = $request->title;
        $poll->description = $request->description;
        $poll->short_description = $request->shortDescription;
        $poll->started_at = $request->startDate;
        $poll->ended_at = $request->endDate;
        $poll->question = $request->question;
        $poll->emails_list_id = $emailList->id;
        $poll->save();

        $result['id'] = $poll->id;
        $result['title'] = $poll->title;
        $result['description'] = $poll->description;
        $result['shortDescription'] = $poll->short_description;
        $result['startDate'] = $poll->started_at;
        $result['endDate'] = $poll->ended_at;
        $result['question'] = $poll->question;
        $result['emailListId'] = $poll->emails_list_id;
        $result['status'] = $poll->status();

        return response(json_encode($result), 201);
    }

    public function deleteDraftPoll(int $id)
    {
        /** @var User $creator */
        $creator = Auth::user();

        $poll = Poll::where([
            'id' => $id,
            'creator_id' => $creator->id,
            'published_at' => null,
        ])->firstOrFail();

        $poll->delete();

        return response('');
    }

    public function publishPoll(Request $request, int $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required|max:1500',
            'shortDescription' => 'max:350',
            'startDate' => 'required',
            'endDate' => 'required',
            'question' => 'required',
            'question.options' => 'min:2',
            'emailListId' => 'required',
        ]);

        /** @var User $creator */
        $creator = Auth::user();

        $poll = Poll::where([
            'id' => $id,
            'creator_id' => $creator->id,
            'published_at' => null,
        ])->firstOrFail();

        $emailList = EmailsList::where([
            'id' => $request->emailListId,
            'owner_id' => $creator->id,
        ])->firstOrFail();

        $poll->title = $request->title;
        $poll->description = $request->description;
        $poll->short_description = $request->shortDescription;
        $poll->started_at = $request->startDate;
        $poll->ended_at = $request->endDate;
        $poll->question = $request->question;
        $poll->emails_list_id = $emailList->id;
        $poll->published_at = new \DateTime();
        $poll->save();

        foreach ($emailList->emails as $email) {
            $token = bin2hex(random_bytes(16));
            $invitation = Invitation::create([
                'token' => $token,
                'email' => $email,
                'poll_id' => $poll->id,
            ]);

            Mail::send([], [], function ($message) use($invitation) {
                $message->to($invitation->email)
                    ->subject('Invitation')
                    ->setBody("Hi! This is your token: {$invitation->token}");
            });
        }

        $result['id'] = $poll->id;
        $result['title'] = $poll->title;
        $result['description'] = $poll->description;
        $result['shortDescription'] = $poll->short_description;
        $result['startDate'] = $poll->started_at;
        $result['endDate'] = $poll->ended_at;
        $result['question'] = $poll->question;
        $result['emailListId'] = $poll->emails_list_id;
        $result['status'] = $poll->status();

        return response(json_encode($result), 201);
    }
}
