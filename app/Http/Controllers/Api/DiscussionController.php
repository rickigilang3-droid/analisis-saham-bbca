<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use App\Models\DiscussionComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    // GET /api/discussions?symbol=BBCA
    public function index(Request $request)
    {
        $discussions = Discussion::with(['user', 'comments.user'])
            ->where('stock_symbol', $request->symbol ?? 'BBCA')
            ->latest()
            ->take(20)
            ->get();
        try {
            $discussions = Discussion::with(['user', 'comments.user'])
                ->where('stock_symbol', $request->symbol ?? 'BBCA')
                ->latest()
                ->take(20)
                ->get();

        return response()->json(['discussions' => $discussions]);
            return response()->json(['discussions' => $discussions]);
        } catch (\Exception $e) {
            // Fallback aman jika tabel discussions belum ada / migration belum dijalankan
            return response()->json(['discussions' => []]);
        }
    }

    // POST /api/discussions
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'body'  => 'required|string|max:2000',
        ]);

        $discussion = Discussion::create([
            'user_id'      => Auth::id(),
            'title'        => $request->title,
            'body'         => $request->body,
            'stock_symbol' => $request->stock_symbol ?? 'BBCA',
        ]);

        return response()->json(['discussion' => $discussion->load('user')], 201);
    }

    // POST /api/discussions/{id}/comments
    public function addComment(Request $request, $id)
    {
        $request->validate(['body' => 'required|string|max:1000']);

        $comment = DiscussionComment::create([
            'discussion_id' => $id,
            'user_id'       => Auth::id(),
            'body'          => $request->body,
        ]);

        return response()->json(['comment' => $comment->load('user')], 201);
    }

    // POST /api/discussions/{id}/like
    public function like($id)
    {
        $discussion = Discussion::findOrFail($id);
        $discussion->increment('likes');
        return response()->json(['likes' => $discussion->likes]);
    }

    // DELETE /api/discussions/{id}
    public function destroy($id)
    {
        $discussion = Discussion::findOrFail($id);
        if ($discussion->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $discussion->delete();
        return response()->json(['message' => 'Dihapus']);
    }
}