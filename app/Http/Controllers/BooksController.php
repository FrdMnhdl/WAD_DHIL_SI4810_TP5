<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Book;
use App\Http\Resources\BookResource;

class BooksController extends Controller
{
    /**
     * ==========1===========
     * Tampilkan daftar semua buku
     */
    public function index()
    {
          $books = Book::all();
        return BookResource::collection($books);
    }

    /**
     * ==========2===========
     * Simpan buku baru ke dalam penyimpanan.
     */
    public function store(Request $request)
    {
          $validator = Validator::make($request->all(), [
            'title'          => 'required|string|max:255',
            'author'         => 'required|string|max:255',
            'published_year' => 'required|digits:4|integer|min:1000|max:' . (date('Y')),
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }
        $book = Book::create([
            'title'          => $request->title,
            'author'         => $request->author,
            'published_year' => $request->published_year,
            'is_available'   => true,
        ]);
        return (new BookResource($book))
                ->additional(['message' => 'Book created successfully'])
                ->response()
                ->setStatusCode(201);
    }

    /**
     * =========3===========
     * Tampilkan detail buku tertentu.
     */
    public function show(string $id)
    {
         $book = Book::find($id);
        if (!$book) {
            return response()->json([
                'message' => 'Book not found'
            ], 404);
        }
        return new BookResource($book);
    }

    /**
     * =========4===========
     * Fungsi untuk memperbarui data buku tertentu
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title'          => 'sometimes|required|string|max:255',
            'author'         => 'sometimes|required|string|max:255',
            'published_year' => 'sometimes|required|digits:4|integer|min:1000|max:' . (date('Y')),
        ]);
        $book = Book::find($id);
        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
            }
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }
        $book->update($request->only(['title', 'author', 'published_year']));
        return (new BookResource($book))
                ->additional(['message' => 'Book updated successfully'])
                ->response()
                ->setStatusCode(200);
    }

    /**
     * =========5===========
     * Hapus buku tertentu dari penyimpanan.
     */
    public function destroy(string $id)
    {
          $book = Book::find($id);
        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }
        $book->delete();
        return response()->json(['message' => 'Book deleted successfully'], 200);
    }

    /**
     * =========6===========
     * Ubah status ketersediaan buku (ubah field is_available)
     */
    public function borrowReturn(string $id)
    {
           $book = Book::find($id);
        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }
        $book->is_available = !$book->is_available;
        $book->save();
        $status = $book->is_available ? 'returned' : 'borrowed';
        return response()->json([
            'message' => "Book successfully {$status}",
            'data'    => new BookResource($book)
        ], 200);

    }
}
