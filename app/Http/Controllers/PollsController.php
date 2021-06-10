<?php

namespace App\Http\Controllers;

use App\Exceptions\PollHasAlreadyBeenPublished;
use App\Http\Resources\PollResource;
use App\Jobs\SendInvitations;
use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller;

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
        $this->validated($request);

        try {
            DB::beginTransaction();

            $poll = new Poll();
            $poll->creator_id = $request->user()->id;
            $poll->title = $request->input('title');
            $poll->description = $request->input('description');
            $poll->short_description = $request->input('shortDescription');
            $poll->started_at = $request->input('startDate');
            $poll->ended_at = $request->input('endDate');
            $poll->question = $request->input('question');
            $poll->emails_list_id = $request->input('emailListId');

            if ($request->input('publish')) {
                $poll->published_at = Carbon::now();
            }

            $poll->save();

            if ($request->input('publish')) {
                dispatch(new SendInvitations($poll));
            }

            DB::commit();

            return (new PollResource($poll))->response()->setStatusCode(201);
        } catch (\Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws PollHasAlreadyBeenPublished
     */
    public function update(Request $request, int $id)
    {
        $this->validated($request);

        try {
            DB::beginTransaction();

            /** @var Poll $poll */
            $poll = Poll::where([
                'id' => $id,
                'creator_id' => $request->user()->id,
            ])->firstOrFail();

            if ($poll->published_at) {
                throw new PollHasAlreadyBeenPublished();
            }

            $poll->title = $request->input('title', $poll->title);
            $poll->description = $request->get('description', $poll->description);
            $poll->short_description = $request->get('shortDescription', $poll->short_description);
            $poll->started_at = $request->get('startDate', $poll->started_at);
            $poll->ended_at = $request->get('endDate', $poll->ended_at);
            $poll->question = $request->get('question', $poll->question);
            $poll->emails_list_id = $request->get('emailListId', $poll->emails_list_id);

            if ($request->input('publish')) {
                $poll->published_at = Carbon::now();
            }

            $poll->save();

            if ($request->input('publish')) {
                dispatch(new SendInvitations($poll));
            }

            DB::commit();

            return new PollResource($poll);
        } catch (\Exception $exception) {
            DB::rollback();

            throw $exception;
        }
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

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validated(Request $request): array
    {
        $validator = validator($request->all(), [
            'title' => 'required',
            'description' => 'required_if:publish,true|max:1500',
            'shortDescription' => 'required_if:publish,true|max:350',
            'startDate' => 'required_if:publish,true',
            'endDate' => 'required_if:publish,true',
            'question' => 'array',
            'question.title' => 'required_if:publish,true|max:500',
            'question.options' => 'required_if:publish,true|min:2',
            'emailListId' => 'required_if:publish,true',
            'publish' => 'boolean',
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

        return $validator->validated();
    }
}
