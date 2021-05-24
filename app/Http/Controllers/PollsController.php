<?php

namespace App\Http\Controllers;

use App\Http\Resources\PollResource;
use App\Jobs\SendInvitations;
use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Lumen\Routing\Controller;
use Validator;

class PollsController extends Controller
{
    public function get(Request $request, $id)
    {
        return new PollResource(Poll::where(['id' => $id, 'creator_id' => $request->user()->id])->firstOrFail());
    }

    public function list(Request $request)
    {
        $this->validate($request, [
            'page' => 'numeric',
            'perPage' => 'numeric',
        ]);

        $page = (int)$request->get('page', 1);
        $perPage = (int)$request->get('perPage', 10);

        $query = Poll::where(['creator_id' => $request->user()->id]);
        $polls = $query->forPage($page, $perPage)->get();
        $total = $query->count();

        return PollResource::collection($polls)
            ->additional(['pagination' => [
                'page' => $page,
                'lastPage' => ceil($total / $perPage),
                'perPage' => $perPage,
                'total' => $total,
            ]]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'max:1500',
            'shortDescription' => 'max:350',
            'question.title' => 'max:500',
            'question.options' => 'min:2',
        ]);

        $validator->sometimes('startDate', 'date|before:endDate', function ($input) {
            return !empty($input->endDate);
        });

        $validator->sometimes('endDate', 'date|after:startDate', function ($input) {
            return !empty($input->startDate);
        });

        $validator->sometimes('emailListId', 'integer|exists:emails_lists,id,owner_id,' . $request->user()->id, function ($input) {
            return !empty($input->emailListId);
        });

        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }

        /** @var Poll $poll */
        $poll = Poll::create([
            'creator_id' => $request->user()->id,
            'title' => $request->get('title'),
            'description' => $request->get('description', ''),
            'short_description' => $request->get('shortDescription', ''),
            'started_at' => $request->get('startDate', null),
            'ended_at' => $request->get('endedDate', null),
            'question' => $request->get('question', []),
            'emails_list_id' => $request->get('emailListId', null),
        ]);

        return new PollResource($poll);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'max:1500',
            'shortDescription' => 'max:350',
            'question.title' => 'max:500',
            'question.options' => 'min:2',
        ]);

        $validator->sometimes('startDate', 'date|before:endDate', function ($input) {
            return !empty($input->endDate);
        });

        $validator->sometimes('endDate', 'date|after:startDate', function ($input) {
            return !empty($input->startDate);
        });

        $validator->sometimes('emailListId', 'integer|exists:emails_lists,id,owner_id,' . $request->user()->id, function ($input) {
            return !empty($input->emailListId);
        });

        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }

        /** @var Poll $poll */
        $poll = Poll::where([
            'id' => $id,
            'creator_id' => $request->user()->id,
            'published_at' => null,
        ])->firstOrFail();

        $poll->title = $request->get('title');
        $poll->description = $request->get('description', $poll->description);
        $poll->short_description = $request->get('shortDescription', $poll->short_description);
        $poll->started_at = $request->get('startDate', $poll->started_at);
        $poll->ended_at = $request->get('endDate', $poll->ended_at);
        $poll->question = $request->get('question', $poll->question);
        $poll->emails_list_id = $request->get('emailListId', $poll->emails_list_id);
        $poll->save();

        return (new PollResource($poll))->response()->setStatusCode(201);
    }

    public function delete(Request $request, int $id)
    {
        $poll = Poll::where([
            'id' => $id,
            'creator_id' => $request->user()->id,
            'published_at' => null,
        ])->firstOrFail();

        $poll->delete();

        return response('', 204);
    }

    public function publish(Request $request, int $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required|max:1500',
            'shortDescription' => 'required|max:350',
            'startDate' => 'required|date|before:endDate',
            'endDate' => 'required|date|after:startDate',
            'question.title' => 'required|max:500',
            'question.options' => 'required|min:2',
            'emailListId' => 'required|integer|exists:emails_lists,id,owner_id,' . $request->user()->id,
        ]);

        /** @var Poll $poll */
        $poll = Poll::where([
            'id' => $id,
            'creator_id' => $request->user()->id,
            'published_at' => null,
        ])->firstOrFail();

        $poll->title = $request->title;
        $poll->description = $request->description;
        $poll->short_description = $request->shortDescription;
        $poll->started_at = $request->startDate;
        $poll->ended_at = $request->endDate;
        $poll->question = $request->question;
        $poll->emails_list_id = $request->emailListId;
        $poll->published_at = Carbon::now();
        $poll->save();

        dispatch(new SendInvitations($poll));

        return (new PollResource($poll))->response()->setStatusCode(201);
    }
}
