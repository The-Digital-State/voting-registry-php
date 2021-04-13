<?php

namespace App\Http\Controllers;

use App\Models\EmailsList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller;

class EmailListController extends Controller
{
    public function getAllLists()
    {
        /** @var User $owner */
        $owner = Auth::user();

        $emailLists = EmailsList::where('owner_id', $owner->id)->get();

        $result = [];
        foreach ($emailLists as $emailList) {
            $resultList['id'] = $emailList->id;
            $resultList['title'] = $emailList->title;
            $resultList['emailsCount'] = count($emailList->emails);
            $result[] = $resultList;
        }

        return json_encode($result);
    }

    public function getList(int $id)
    {
        /** @var User $owner */
        $owner = Auth::user();

        $emailList = EmailsList::where([
            'id' => $id,
            'owner_id' => $owner->id,
        ])->firstOrFail();

        $result['id'] = $emailList->id;
        $result['title'] = $emailList->title;
        $result['emails'] = $emailList->emails;

        return json_encode($result);
    }

    public function createNewList(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'emails' => 'required|array',
        ]);

        /** @var User $owner */
        $owner = Auth::user();

        $emailList = EmailsList::create([
            'title' => $request->title,
            'emails' => $request->emails,
            'owner_id' => $owner->id,
        ]);

        $result['id'] = $emailList->id;
        $result['title'] = $emailList->title;
        $result['emails'] = $emailList->emails;

        return response(json_encode($result), 201);
    }

    public function updateList(Request $request, int $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'emails' => 'required|array',
        ]);

        /** @var User $owner */
        $owner = Auth::user();

        $emailList = EmailsList::where([
            'id' => $id,
            'owner_id' => $owner->id,
        ])->firstOrFail();

        $emailList->title = $request->title;
        $emailList->emails = $request->emails;
        $emailList->save();

        $result['id'] = $emailList->id;
        $result['title'] = $emailList->title;
        $result['emails'] = $emailList->emails;

        return response(json_encode($result), 201);
    }

    public function deleteList(int $id)
    {
        /** @var User $owner */
        $owner = Auth::user();

        $emailList = EmailsList::where([
            'id' => $id,
            'owner_id' => $owner->id,
        ])->firstOrFail();

        $emailList->delete();

        return response('');
    }
}
