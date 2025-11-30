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
        $rules = [
            'title' => 'required',
            'author' => 'required',
            'published_year' => 'required|digits:4'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $request->only(['title', 'author', 'published_year']);
        $data['is_available'] = 1;

        $book = Book::create($data);

        return new BookResource($book);
    }

    /**
     * =========3===========
     * Tampilkan detail buku tertentu.
     */
    public function show(string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response([
                'message' => 'Book not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return new BookResource($book);
    }

    /**
     * =========4===========
     * Fungsi untuk memperbarui data buku tertentu
     */
    public function update(Request $request, string $id)
    {
        $rules = [
            'title' => 'required',
            'author' => 'required',
            'published_year' => 'required|digits:4',
            'is_available' => 'boolean'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $book = Book::find($id);

        if (!$book) {
            return response([
                'message' => 'Book not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $book->update($request->all());

        return new BookResource($book);
    }

    /**
     * =========5===========
     * Hapus buku tertentu dari penyimpanan.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response([
                'message' => 'Book not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $book->delete();

        return response([
            'message' => 'Book deleted'
        ]);
    }

    /**
     * =========6===========
     * Ubah status ketersediaan buku (ubah field is_available)
     */
    public function borrowReturn(string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response([
                'message' => 'Book not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $book->is_available = $book->is_available ? 0 : 1;
        $book->save();

        return response([
            'message' => $book->is_available ? 'Book returned' : 'Book borrowed'
        ]);
    }
}
