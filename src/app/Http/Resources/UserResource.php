<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use Nette\Utils\DateTime;

class UserResource extends JsonResource
{

    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->adminProfile->first_name,
            'last_name' => $this->adminProfile->last_name,
            'first_kana' => $this->adminProfile->first_kana,
            'last_kana' => $this->adminProfile->last_kana,
            'email' => $this->email,
            'updated_at' => (new DateTime($this->adminProfile->updated_at))->format('Y-m-d H:i:s'),
        ];
    }
}
