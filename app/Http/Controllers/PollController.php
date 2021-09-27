<?php

namespace App\Http\Controllers;

use App\Http\Requests\PollCreateRequest;
use App\Http\Requests\PollUpdateRequest;
use App\Http\Resources\PollResource;
use App\Http\Resources\PollResultResource;
use App\Jobs\InvitationsSend;
use App\Models\Poll;
use App\Models\PollResult;
use App\Models\PollVoter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PollController extends Controller
{
    /**
     * Create a new EmailsListController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['view', 'result', 'results', 'statistic']]);
    }

    /**
     * Get a single Poll
     *
     * @param $id
     * @return PollResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function get($id): PollResource
    {
        $poll = Poll::findOrFail($id);

        $this->authorize('get', $poll);

        return new PollResource($poll);
    }

    /**
     * Get a list of Poll
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function list(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $this->authorize('list', Poll::class);

        $this->validate($request, [
            'page' => 'numeric',
            'perPage' => 'numeric',
            'with' => 'array',
            'sort' => 'array',
        ]);

        $page = (int)$request->get('page', 1);
        $perPage = (int)$request->get('perPage', 10);

        $query = Poll::whereOwnerId($request->user()->id);

        if ($request->has('with')) {
            if (in_array('emailsList', $request->get('with', []))) {
                $query->with('emailsList');
            }
        }

        if ($request->has('sort')) {
            foreach ($request->get('sort') as $key => $direction) {
                $query->orderBy(Str::snake($key), $direction);
            }
        }

        $list = $query->paginate($perPage, ['*'], 'page', $page);

        return PollResource::collection($list->items())
            ->additional(['pagination' => [
                'page' => $page,
                'perPage' => $list->perPage(),
                'lastPage' => $list->lastPage(),
                'total' => $list->total(),
            ]]);
    }

    /**
     * Create Poll
     *
     * @param PollCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable
     */
    public function create(PollCreateRequest $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('create', Poll::class);

        $validated = collect($request->validated());

        try {
            DB::beginTransaction();

            $poll = new Poll();
            $poll->title = $validated->get('title');
            $poll->short_description = $validated->get('shortDescription');
            $poll->description = $validated->get('description');
            $poll->start = $validated->get('start');
            $poll->end = $validated->get('end');
            $poll->question = $validated->get('question');
            $poll->owner_id = $request->user()->id;
            $poll->emails_list_id = $validated->get('emailsListId');

            if ($validated->get('publish')) {
                $poll->published_at = now();
            }

            $poll->save();

            // send invitation if poll is published
            if ($validated->get('publish')) {
                dispatch(new InvitationsSend($poll));
            }
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }

        DB::commit();

        return (new PollResource($poll))->response()->setStatusCode(201);
    }

    /**
     * Update Poll
     *
     * @param PollUpdateRequest $request
     * @param $id
     * @return PollResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable
     */
    public function update(PollUpdateRequest $request, $id): PollResource
    {
        $poll = Poll::findOrFail($id);

        $this->authorize('update', $poll);

        $validated = collect($request->validated());

        try {
            DB::beginTransaction();

            $poll->title = $validated->get('title', $poll->title);
            $poll->description = $validated->get('description', $poll->description);
            $poll->short_description = $validated->get('shortDescription', $poll->short_description);
            $poll->start = $validated->get('start', $poll->start);
            $poll->end = $validated->get('end', $poll->end);
            $poll->question = $validated->get('question', $poll->question);
            $poll->emails_list_id = $validated->get('emailsListId', $poll->emails_list_id);

            if ($validated->get('publish')) {
                $poll->published_at = now();
            }

            $poll->save();

            if ($validated->get('publish')) {
                dispatch(new InvitationsSend($poll));
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return new PollResource($poll);
    }

    /**
     * Delete Poll
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function delete($id): \Illuminate\Http\Response
    {
        $poll = Poll::findOrFail($id);

        if ($poll->published_at) {
            $this->authorize('delete', $poll);

            $poll->delete();
        } else {
            $this->authorize('forceDelete', $poll);

            $poll->forceDelete();
        }

        return response('', 204);
    }

    /**
     * Check if user can vote
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function canVote($id): \Illuminate\Http\Response
    {
        return response(['can' => request()->user()->can('vote', Poll::withTrashed()->findOrFail($id))]);
    }

    /**
     * Vote
     *
     * Poll id
     * @param Request $request
     * @param $id
     * @return PollResultResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function vote(Request $request, $id): PollResultResource
    {
        $poll = Poll::withTrashed()->findOrFail($id);

        $this->authorize('vote', $poll);

        $this->validate($request, [
            'choice' => 'required|in:' . implode(',', $poll->question['options']),
        ]);

        try {
            DB::beginTransaction();

            $voter = new PollVoter();
            $voter->voter_id = $request->user()->id;
            $voter->poll_id = $poll->id;
            $voter->save();

            $pollResult = new PollResult();
            $pollResult->poll_id = $poll->id;
            $pollResult->choice = $request->get('choice');
            $pollResult->save();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return new PollResultResource($pollResult);
    }

    /**
     * View a Poll
     *
     * @param $id
     * @return PollResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function view($id): PollResource
    {
        $poll = Poll::withTrashed()->published()->findOrFail($id);

        return new PollResource($poll);
    }

    /**
     * Get a PollResult by token
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function result($id, $token): PollResultResource
    {
        return new PollResultResource(PollResult::wherePollId($id)->whereToken($token)->firstOrFail());
    }

    /**
     * Get a list of PollResults
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function results(Request $request, $id): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $poll = Poll::withTrashed()->published()->findOrFail($id);

        if ($poll->isInVoting()) {
            throw new \Exception('Results are available only after the end of voting');
        }

        $this->validate($request, [
            'page' => 'numeric',
            'perPage' => 'numeric',
            'sort' => 'array',
        ]);

        $page = (int)$request->get('page', 1);
        $perPage = (int)$request->get('perPage', 10);

        $query = PollResult::wherePollId($poll->id);
        $total = $query->count();

        if ($request->has('sort')) {
            foreach ($request->get('sort') as $key => $direction) {
                $query->orderBy(Str::snake($key), $direction);
            }
        }

        $results = $query->forPage($page, $perPage)->get();
        $pagination = [
            'page' => $page,
            'lastPage' => ceil($total / $perPage),
            'perPage' => $perPage,
            'total' => $total,
        ];

        return PollResultResource::collection($results)->additional(['pagination' => $pagination]);
    }

    /**
     * @throws \Exception
     */
    public function statistic($id)
    {
        $poll = Poll::withTrashed()->published()->findOrFail($id);

        if ($poll->isInVoting()) {
            throw new \Exception('Statistic are available only after the end of voting');
        }
    }
}
