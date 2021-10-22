<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailsListCreateRequest;
use App\Http\Requests\EmailsListUpdateRequest;
use App\Http\Resources\EmailsListResource;
use App\Models\EmailsList;
use App\Models\Poll;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class EmailsListController extends Controller
{
    /**
     * Create a new EmailsListController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get a single EmailsList
     *
     * @param $id
     * @return EmailsListResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function get($id): EmailsListResource
    {
        $emailsList = EmailsList::findOrFail($id);

        $this->authorize('get', $emailsList);

        return new EmailsListResource($emailsList);
    }

    /**
     * Get a list of EmailsList
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function list(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $this->authorize('list', EmailsList::class);

        $this->validate($request, [
            'page' => 'numeric',
            'perPage' => 'numeric',
            'with' => 'array',
            'sort' => 'array',
        ]);

        $page = (int)$request->get('page', 1);
        $perPage = (int)$request->get('perPage', 10);

        $query = EmailsList::whereOwnerId(request()->user()->id);

        if ($request->has('sort')) {
            foreach ($request->get('sort') as $key => $direction) {
                $query->orderBy(Str::snake($key), $direction);
            }
        }

        $list = $query->paginate($perPage, ['*'], 'page', $page);

        return EmailsListResource::collection($list->items())
            ->additional(['pagination' => [
                'page' => $page,
                'perPage' => $list->perPage(),
                'lastPage' => $list->lastPage(),
                'total' => $list->total(),
            ]]);
    }

    /**
     * Create EmailsList
     *
     * @param EmailsListCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(EmailsListCreateRequest $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('create', EmailsList::class);

        $validated = collect($request->validated());

        $emailsList = new EmailsList();
        $emailsList->title = $validated->get('title');
        $emailsList->emails = array_unique($validated->get('emails'));
        $emailsList->owner_id = $request->user()->id;
        $emailsList->save();

        return (new EmailsListResource($emailsList))->response()->setStatusCode(201);
    }

    /**
     * Update EmailsList
     *
     * @param EmailsListUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(EmailsListUpdateRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        $emailsList = EmailsList::findOrFail($id);

        $this->authorize('update', $emailsList);

        $validated = collect($request->validated());

        $emailsList->title = $validated->get('title', $emailsList->title);

        if ($validated->has('emails')) {
            $emailsList->emails = array_unique($validated->get('emails'));
        }

        $emailsList->save();

        return (new EmailsListResource($emailsList))->response()->setStatusCode(201);
    }

    /**
     * Delete EmailsList
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(int $id): \Illuminate\Http\Response
    {
        $emailList = EmailsList::find($id);

        $this->authorize('delete', $emailList);

        $emailList->delete();

        return response('', 204);
    }
}
