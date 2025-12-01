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
            'title' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,id',
            'isbn' => 'required|string|unique:books,isbn|max:20',
            'published_year' => 'nullable|integer|digits:4',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $book = Book::create($validator->validated());
        return new BookResource($book, Response::HTTP_CREATED);
    }

    /**
     * =========3===========
     * Tampilkan detail buku tertentu.
     */
    public function show(string $id)
    {
        $book = Book::find($id);
        if (!$book) {
            return response()->json(['message' => 'Buku tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }
        return new BookResource($book);
    }

    /**
     * =========4===========
     * Fungsi untuk memperbarui data buku tertentu
     */
    public function update(Request $request, string $id)
    {
        $book = Book::find($id);

        if (!$book){
            return response()->json(['message' => 'Buku tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }
         $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'author_id' => 'sometimes|exists:authors,id',
            'isbn' => 'sometimes|string|unique:books,isbn,' . $book->id . '|max:20', // Unique check, excluding current book
            'published_year' => 'nullable|integer|digits:4',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $book->update($validator->validated());

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
            return response()->json(['message' => 'Buku tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }
        $book->delete();
        return response()->json(['message' => 'Buku berhasil dihapus'], Response::HTTP_NO_CONTENT);
    }

    /**
     * =========6===========
     * Ubah status ketersediaan buku (ubah field is_available)
     */
    public function borrowReturn(string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Buku tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }

        
        $newStatus = !$book->is_available;
        $book->is_available = $newStatus;
        $book->save();

        $message = $newStatus ? 'Buku berhasil dikembalikan dan tersedia.' : 'Buku berhasil dipinjam dan tidak tersedia.';

        return response()->json([
            'message' => $message,
            'book' => new BookResource($book)
        ]);
    }

}
