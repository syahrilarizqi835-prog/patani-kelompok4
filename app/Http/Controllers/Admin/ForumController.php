<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumTopic;
use App\Models\ForumReply;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    // =========================================================================
    // INDEX — Daftar semua topik dengan filter & stats
    // =========================================================================

    public function index(Request $request)
    {
        $query = ForumTopic::with('user')->withCount('replies');

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('category', $request->kategori);
        }

        // Filter status
        match ($request->input('status')) {
            'pinned' => $query->where('is_pinned', true),
            'locked' => $query->where('is_locked', true),
            'hot'    => $query->where('is_hot', true),
            default  => null,
        };

        // Urutan: pinned selalu di atas, lalu latest
        $topics = $query->orderByDesc('is_pinned')
                        ->orderByDesc('created_at')
                        ->paginate(20)
                        ->withQueryString();

        // Stats untuk header
        $stats = [
            'total'    => ForumTopic::count(),
            'pinned'   => ForumTopic::where('is_pinned', true)->count(),
            'locked'   => ForumTopic::where('is_locked', true)->count(),
            'hot'      => ForumTopic::where('is_hot', true)->count(),
            'mingguIni' => ForumTopic::where('created_at', '>=', Carbon::now()->subWeek())->count(),
        ];

        return view('admin.forum', compact('topics', 'stats'));
    }

    // =========================================================================
    // SHOW — Detail satu topik beserta semua balasan
    // =========================================================================

    public function show($id)
    {
        $topic = ForumTopic::with(['user', 'replies.user'])->findOrFail($id);

        return view('admin.forum-detail', compact('topic'));
    }

    // =========================================================================
    // PIN / UNPIN — Toggle topik menjadi pinned (tampil paling atas)
    // =========================================================================

    public function togglePin($id)
    {
        $topic = ForumTopic::findOrFail($id);
        $topic->update(['is_pinned' => !$topic->is_pinned]);

        $status = $topic->is_pinned ? 'disematkan' : 'dilepas dari sematan';

        return redirect()->back()->with('success', "Topik berhasil {$status}.");
    }

    // =========================================================================
    // LOCK / UNLOCK — Toggle topik dikunci (petani tidak bisa reply)
    // =========================================================================

    public function toggleLock(Request $request, $id)
    {
        $topic = ForumTopic::findOrFail($id);

        $topic->update([
            'is_locked'  => !$topic->is_locked,
            'admin_note' => $request->filled('catatan')
                ? $request->catatan
                : ($topic->is_locked ? null : $topic->admin_note),
        ]);

        $status = $topic->is_locked ? 'dikunci' : 'dibuka kembali';

        return redirect()->back()->with('success', "Topik berhasil {$status}.");
    }

    // =========================================================================
    // HOT / UNHOT — Tandai topik sebagai "sedang ramai"
    // =========================================================================

    public function toggleHot($id)
    {
        $topic = ForumTopic::findOrFail($id);
        $topic->update(['is_hot' => !$topic->is_hot]);

        $status = $topic->is_hot ? 'ditandai sebagai Hot 🔥' : 'dilepas dari label Hot';

        return redirect()->back()->with('success', "Topik berhasil {$status}.");
    }

    // =========================================================================
    // REPLY AS ADMIN — Admin membalas topik atas nama admin
    // =========================================================================

    public function reply(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|min:5|max:2000',
        ], [
            'content.required' => 'Isi balasan tidak boleh kosong.',
            'content.min'      => 'Balasan minimal 5 karakter.',
        ]);

        $topic = ForumTopic::findOrFail($id);

        // Cegah reply ke topik yang dikunci
        if ($topic->is_locked) {
            return redirect()->back()->with('error', 'Topik ini dikunci — tidak bisa dibalas.');
        }

        ForumReply::create([
            'topic_id' => $topic->id,
            'user_id'  => Auth::id(),
            'content'  => $request->input('content'),
        ]);

        return redirect()->back()->with('success', 'Balasan admin berhasil dikirim.');
    }

    // =========================================================================
    // HAPUS REPLY — Admin menghapus satu balasan tertentu
    // =========================================================================

    public function destroyReply($id)
    {
        $reply = ForumReply::findOrFail($id);
        $topicId = $reply->topic_id;
        $reply->delete();

        return redirect()->back()->with('success', 'Balasan berhasil dihapus.');
    }

    // =========================================================================
    // HAPUS TOPIK — Soft delete topik beserta semua replynya
    // =========================================================================

    public function destroy($id)
    {
        $topic = ForumTopic::findOrFail($id);
        $topic->replies()->delete(); // hapus semua reply dulu
        $topic->delete();

        return redirect()->route('admin.forum')->with('success', 'Topik dan semua balasan berhasil dihapus.');
    }

    // =========================================================================
    // UPDATE CATATAN ADMIN — Admin bisa kasih keterangan pada topik
    // =========================================================================

    public function updateNote(Request $request, $id)
    {
        $request->validate(['admin_note' => 'nullable|string|max:500']);

        ForumTopic::findOrFail($id)->update(['admin_note' => $request->admin_note]);

        return redirect()->back()->with('success', 'Catatan admin berhasil disimpan.');
    }
}