<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);

        // Other json reponse structure (optional)
        // return [
        //     'type' => 'users',
        //     'id' => $this->id,
        //     'attributes' => [
        //         'id' => $this->id,
        //         'name' => $this->name,
        //         'email' => $this->email,
        //         'created' => [
        //             'string' => $this->created_at->toDateString(),
        //             'human' => $this->created_at->diffForHumans(),
        //         ]
        //     ],
        //     'relationships' => [],
        //     'links' => [
        //         'self' => '/'
        //     ]
        // ];
    }
}
