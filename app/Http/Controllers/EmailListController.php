<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmailListResource;
use App\Models\EmailsList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller;

class EmailListController extends Controller
{
    public function get(int $id)
    {
        /** @var User $user */
        $user = Auth::user();
        $emailList = EmailsList::where(['id' => $id, 'owner_id' => $user->id])->firstOrFail();

        return new EmailListResource($emailList);
    }

    public function list()
    {
        /** @var User $user */
        $user = Auth::user();
        $emailLists = EmailsList::where('owner_id', $user->id)->get();

        return EmailListResource::collection($emailLists);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'emails' => 'required|array',
            'emails.*' => 'email:filter',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $emailList = EmailsList::create([
            'title' => $request->title,
            'emails' => $request->emails,
            'owner_id' => $user->id,
        ]);

        return (new EmailListResource($emailList))->response()->setStatusCode(201);
    }

    public function update(Request $request, int $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'emails' => 'required|array',
            'emails.*' => 'email:filter',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $emailList = EmailsList::where([
            'id' => $id,
            'owner_id' => $user->id,
        ])->firstOrFail();

        $emailList->title = $request->title;
        $emailList->emails = $request->emails;
        $emailList->save();

        return (new EmailListResource($emailList))->response()->setStatusCode(201);
    }

    public function delete(int $id)
    {
        /** @var User $user */
        $user = Auth::user();

        $emailList = EmailsList::where([
            'id' => $id,
            'owner_id' => $user->id,
        ])->firstOrFail();

        $emailList->delete();

        return response('', 204);
    }
}
