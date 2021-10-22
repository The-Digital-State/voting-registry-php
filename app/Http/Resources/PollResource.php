<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PollResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $emailsList = $this->whenLoaded('emailsList');

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'shortDescription' => $this->short_description,
            'start' => $this->start,
            'end' => $this->end,
            'question' => $this->question,
            'emailsListId' => $this->emails_list_id,
            $this->mergeWhen($emailsList, ['emailsList' => $emailsList]),
            'publishedAt' => $this->published_at,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
