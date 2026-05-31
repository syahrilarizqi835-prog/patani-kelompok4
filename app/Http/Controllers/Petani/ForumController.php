<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use App\Models\ForumTopic;
use App\Models\ForumReply;
use App\Models\ForumLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $query = ForumTopic::with(['user'])->withCount('replies');

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $topics = $query->orderByDesc('is_pinned')
                        ->orderByDesc('created_at')
                        ->paginate(10)
                        ->withQueryString();

        return view('dashboard.forum', compact('topics'));
    }

    // =========================================================================
    // SHOW — Detail topik + komentar level-1 + nested replies
    // =========================================================================

    public function show($id)
    {
        $topic = ForumTopic::with([
            'user',
            'replies' => function ($q) {
                $q->whereNull('parent_id')
                  ->with(['user', 'children.user'])
                  ->oldest();
            },
        ])->findOrFail($id);

        abort_if($topic->trashed(), 404);
        $topic->increment('views');

        $sudahLike = ForumLike::where('user_id', Auth::id())
            ->where('likeable_type', ForumTopic::class)
            ->where('likeable_id', $id)
            ->exists();

        return view('dashboard.forum-detail', compact('topic', 'sudahLike'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string|min:10',
            'category' => 'required|in:hama_penyakit,varietas_padi,teknik_budidaya,pemupukan,pengairan,umum',
        ]);

        $topic = ForumTopic::create([
            'user_id'  => Auth::id(),
            'title'    => $request->input('title'),
            'content'  => $request->input('content'),
            'category' => $request->input('category'),
        ]);

        return redirect()->route('dashboard.forum.show', $topic->id)
                         ->with('success', 'Topik berhasil dibuat!');
    }

    // =========================================================================
    // REPLY — level-1 ke topik ATAU nested ke komentar lain
    // =========================================================================

    public function reply(Request $request, $id)
    {
        $request->validate([
            'content'   => 'required|string|min:3|max:2000',
            'parent_id' => 'nullable|integer|exists:forum_replies,id',
        ], [
            'content.required' => 'Isi balasan tidak boleh kosong.',
            'content.min'      => 'Balasan minimal 3 karakter.',
        ]);

        $topic = ForumTopic::findOrFail($id);

        if ($topic->is_locked) {
            return redirect()->back()->with('error', 'Topik ini dikunci oleh admin.');
        }

        ForumReply::create([
            'topic_id'  => $topic->id,
            'parent_id' => $request->input('parent_id') ?: null,
            'user_id'   => Auth::id(),
            'content'   => $request->input('content'),
        ]);

        return redirect()->route('dashboard.forum.show', $id)
                         ->with('success', 'Balasan berhasil dikirim!')
                         ->withFragment('komentar');
    }

    public function like($id)
    {
        $topic = ForumTopic::findOrFail($id);

        $existing = ForumLike::where('user_id', Auth::id())
            ->where('likeable_type', ForumTopic::class)
            ->where('likeable_id', $id)
            ->first();

        if ($existing) {
            $existing->delete();
            $topic->decrement('likes');
        } else {
            ForumLike::create([
                'user_id'       => Auth::id(),
                'likeable_type' => ForumTopic::class,
                'likeable_id'   => $id,
            ]);
            $topic->increment('likes');
        }

        return redirect()->back();
    }
}