<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookCollection;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): BookCollection
    {
        return new BookCollection(Book::all());
    }

    /**
     * Search by filter
     */
    public function index_filtered(string $criteria, string $query): JsonResponse|BookCollection
    {
        if (! in_array($criteria, ['author', 'publisher', 'title']) )
        {
            return response()->json('Invalid criteria', 422);
        }

        return new BookCollection(Book::where($criteria, $query)->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated_data = $request->validate([
            'title' => 'required',
            'author' => 'required',
            'publisher' => 'required'
        ]);

        $book = Book::create($validated_data);

        return response()->json($book);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $book = Book::find($id);

        return response()->json($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $book = Book::find($id);

        if (!$book)
        {
            return response()->json('No Book found.', 404);
        }

        $book->update($request->all());

        return response()->json($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $book = Book::find($id);

        if (!$book)
        {
            return response()->json('No Book found.', 404);
        }

        $book->delete();

        return response()->json('Successfully deleted.');
    }

    /**
     * Give book to user
     */
    public function give(string $book_id, string $user_id): JsonResponse
    {
        $book = Book::find($book_id);

        if (!$book)
        {
            return response()->json('No Book found.', 404);
        }

        if (!($book->in_stock && $book->reserved && $book->reserver_id == $user_id))
        {
            return response()->json('Book cannot be given.', 403);
        }

        $book->update([
            'reserved' => False,
            'reserver_id' => null,
            'in_stock' => False,
            'last_owner_id' => $user_id
        ]);

        return response()->json($book);
    }

    /**
     * Take book from User by Librarian
     */
    public function take(string $book_id, string $user_id): JsonResponse
    {
        $book = Book::find($book_id);

        if (!$book)
        {
            return response()->json('No Book found.', 404);
        }

        if ($book->in_stock || $book->last_owner_id != $user_id)
        {
            return response()->json('Book cannot be taken.', 403);
        }

        $book->update(['in_stock' => True]);

        return response()->json($book);
    }

    /**
     * Reserve book by User
     */
    public function reserve(string $book_id): JsonResponse
    {
        $book = Book::find($book_id);
        $user_id = Auth::id();

        if (!$book)
        {
            return response()->json('No Book found.', 404);
        }

        if ($book->reserved)
        {
            return response()->json('Book cannot be reserved', 403);
        }

        $book->update(["reserved" => True, "reserver_id" => $user_id]);

        return response()->json($book);
    }

    /**
     * Unreserve book by User
     */
    public function unreserve(string $book_id): JsonResponse
    {
        $book = Book::find($book_id);
        $user_id = Auth::id();

        if (!$book)
        {
            return response()->json('No Book found.', 404);
        }

        if (!($book->reserved && $book->reserver_id == $user_id))
        {
            return response()->json('Book cannot be unreserved', 403);
        }

        $book->update(['reserved' => False, 'reserver_id' => null]);

        return response()->json($book);
    }

}
