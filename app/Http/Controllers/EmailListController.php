<?php

namespace App\Http\Controllers;

use App\Models;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class EmailListController extends Controller
{
    public function getAllLists()
    {
        // ToDo: Get User After authorization
        $ownerId = 1;

        $emailLists = Models\EmailsList::where('owner_id', $ownerId)->get();

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
        // ToDo: Get User After authorization
        $ownerId = 1;

        $emailList = Models\EmailsList::where([
            'id' => $id,
            'owner_id' => $ownerId,
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

        // ToDo: Get User After authorization
        $ownerId = 1;

        $emailList = Models\EmailsList::create([
            'title' => $request->title,
            'emails' => $request->emails,
            'owner_id' => $ownerId,
        ]);

        $result['id'] = $emailList->id;
        $result['title'] = $emailList->title;
        $result['emails'] = $emailList->emails;

        return json_encode($result);
    }

    public function updateList(Request $request, int $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'emails' => 'required|array',
        ]);

        // ToDo: Get User After authorization
        $ownerId = 1;

        $emailList = Models\EmailsList::where([
            'id' => $id,
            'owner_id' => $ownerId,
        ])->firstOrFail();

        $emailList->title = $request->title;
        $emailList->emails = $request->emails;
        $emailList->save();

        $result['id'] = $emailList->id;
        $result['title'] = $emailList->title;
        $result['emails'] = $emailList->emails;

        return json_encode($result);
    }

    public function deleteList(int $id)
    {
        // ToDo: Get User After authorization
        $ownerId = 1;

        $emailList = Models\EmailsList::where([
            'id' => $id,
            'owner_id' => $ownerId,
        ])->firstOrFail();

        $emailList->delete();

        return response('');
    }
}
