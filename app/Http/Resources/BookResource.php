<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * =========1=========
     * Transformasikan resource menjadi array.
     * Pastikan untuk menyertakan semua atribut model Book.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author_id' => $this->author_id,
            'isbn' => $this->isbn,
            'published_year' => $this->published_year,
            'description' => $this->description,
            'is_available' => $this->is_available, 
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
