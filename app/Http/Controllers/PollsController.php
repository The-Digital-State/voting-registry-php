<?php

namespace App\Http\Controllers;

use App\Models;
use Illuminate\Http\Request;

class PollsController extends Controller
{
    public function getAllPolls()
    {
        // ToDo: Get User After authorization
        $creatorId = 1;

        $polls = Models\Poll::where('creator_id', $creatorId)->get();

        $result = [];
        foreach ($polls as $poll) {
            $resultPoll['id'] = $poll->id;
            $resultPoll['title'] = $poll->title;
            $resultPoll['startDate'] = $poll->startedAt;
            $resultPoll['endDate'] = $poll->endedAt;
            $resultPoll['emailListTitle'] = $poll->emailList;
            $resultPoll['previewLink'] = 'http://albo.vote/preview/23';
            $resultPoll['status'] = 'draft';

            $result[] = $resultPoll;
        }

        return json_encode($result);
    }

    public function getPoll(int $id)
    {
        // ToDo: Get User After authorization
        $creatorId = 1;

        $poll = Models\Poll::where([
            'id' => $id,
            'creator_id' => $creatorId,
        ])->firstOrFail();

        $result['id'] = $poll->id;
        $result['title'] = $poll->title;
        $result['description'] = $poll->description;
        $result['shortDescription'] = $poll->short_description;
        $result['startDate'] = $poll->started_at;
        $result['endDate'] = $poll->ended_at;
        $result['question'] = $poll->question;
        $result['emailListId'] = $poll->emails_list_id;
        $result['status'] = null;

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

        // ToDo: Get User After authorization
        $creatorId = 1;

        $poll = Models\Poll::create([
            'title' => $request->title,
            'description' => $request->description,
            'short_description' => $request->shortDescription,
            'started_at' => $request->startDate,
            'ended_at' => $request->endDate,
            'question' => $request->question,
            'emails_list_id' => $request->emailListId, //ToDo: check the ownership of this list
            'creator_id' => $creatorId,
        ]);

        $result['id'] = $poll->id;
        $result['title'] = $poll->title;
        $result['description'] = $poll->description;
        $result['shortDescription'] = $poll->short_description;
        $result['startDate'] = $poll->started_at;
        $result['endDate'] = $poll->ended_at;
        $result['question'] = $poll->question;
        $result['emailListId'] = $poll->emails_list_id;

        return json_encode($result);
    }

    public function updateDraftPoll(Request $request, int $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'max:1500',
            'shortDescription' => 'max:350',
            'question.options' => 'min:2',
        ]);

        // ToDo: Get User After authorization
        $creatorId = 1;

        $poll = Models\Poll::where([
            'id' => $id,
            'creator_id' => $creatorId,
            'published_at' => null,
        ])->firstOrFail();

        $poll->title = $request->title;
        $poll->description = $request->description;
        $poll->short_description = $request->shortDescription;
        $poll->started_at = $request->startDate;
        $poll->ended_at = $request->endDate;
        $poll->question = $request->question;
        $poll->emails_list_id = $request->emailListId; //ToDo: check the ownership of this list
        $poll->save();

        $result['id'] = $poll->id;
        $result['title'] = $poll->title;
        $result['description'] = $poll->description;
        $result['shortDescription'] = $poll->short_description;
        $result['startDate'] = $poll->started_at;
        $result['endDate'] = $poll->ended_at;
        $result['question'] = $poll->question;
        $result['emailListId'] = $poll->emails_list_id;

        return json_encode($result);
    }

    public function deleteDraftPoll(int $id)
    {
        // ToDo: Get User After authorization
        $creatorId = 1;

        $poll = Models\Poll::where([
            'id' => $id,
            'creator_id' => $creatorId,
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

        // ToDo: Get User After authorization
        $creatorId = 1;

        $poll = Models\Poll::where([
            'id' => $id,
            'creator_id' => $creatorId,
            'published_at' => null,
        ])->firstOrFail();

        $poll->title = $request->title;
        $poll->description = $request->description;
        $poll->short_description = $request->shortDescription;
        $poll->started_at = $request->startDate;
        $poll->ended_at = $request->endDate;
        $poll->question = $request->question;
        $poll->emails_list_id = $request->emailListId; //ToDo: check the ownership of this list
        $poll->published_at = new \DateTime();
        $poll->save();

        $result['id'] = $poll->id;
        $result['title'] = $poll->title;
        $result['description'] = $poll->description;
        $result['shortDescription'] = $poll->short_description;
        $result['startDate'] = $poll->started_at;
        $result['endDate'] = $poll->ended_at;
        $result['question'] = $poll->question;
        $result['emailListId'] = $poll->emails_list_id;
        $result['previewLink'] = 'http://albo.vote/preview/23';

        return json_encode($result);
    }
}
