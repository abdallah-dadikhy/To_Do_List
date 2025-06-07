<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'is_completed'=> $this->is_completed,
            'due_date' => $this->due_date,
            'category_name' => optional($this->category)->name,
            'priority_name' => optional($this->priority)->name,
        ];
    }
}
