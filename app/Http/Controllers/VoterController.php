<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models;
use Laravel\Lumen\Routing\Controller;

class VoterController extends Controller
{
    public function castVote(Request $request, int $id)
    {
        // ToDo: Check authorization

        // ToDo: Check if this poll has this option
        $this->validate($request, [
            'option' => 'required',
            'optionIndex' => 'required',
        ]);

        // ToDo: Get poll access token from jwt
        $pollAccessToken = '8cb6d2d327c90db027fb26fb85219f86';

        $invitation = Models\Invitation::where([
            'poll_id' => $id,
            'token' => $pollAccessToken,
        ])
            ->whereNull('voted_at')
            ->firstOrFail();

        $token = bin2hex(random_bytes(16));
        Models\PollResult::create([
            'token' => $token,
            'poll_id' => $id,
            'choice' => $request->option,
            'choice_index' => $request->optionIndex,
        ]);

        $invitation->voted_at = new \DateTime();
        $invitation->save();

        $result['token'] = $token;

        return json_encode($result);
    }
}
