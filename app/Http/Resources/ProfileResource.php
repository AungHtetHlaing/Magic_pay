<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $unread_notifications_count = $this->unreadNotifications()->count();

        return [
            "name" => $this->name,
            "email" => $this->email,
            "phone" => $this->phone,
            "account_number" => $this->wallet ? $this->wallet->account_number : "",
            "balance" => $this->wallet ? number_format($this->wallet->amount) : 0,
            "receive_qr_value" => $this->phone,
            "unread_notifications_count" => $unread_notifications_count
        ];
    }
}
