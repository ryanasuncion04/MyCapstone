<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $conversations = auth()->user()->getConversations();

        if ($search) {
            $conversations = $conversations->filter(function ($conversation) use ($search) {
                $otherUser = $conversation->getOtherUser(auth()->id());
                return stripos($otherUser->name, $search) !== false || stripos($otherUser->email, $search) !== false;
            });
        }

        $allUsersQuery = User::where('id', '!=', auth()->id());

        if ($search) {
            $allUsersQuery = $allUsersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }

        $allUsers = $allUsersQuery->get();

        return view('chat.index', [
            'conversations' => $conversations,
            'allUsers' => $allUsers,
            'search' => $search,
        ]);
    }

        public function mindex(Request $request)
    {
        $search = $request->input('search', '');

        $conversations = auth()->user()->getConversations();

        if ($search) {
            $conversations = $conversations->filter(function ($conversation) use ($search) {
                $otherUser = $conversation->getOtherUser(auth()->id());
                return stripos($otherUser->name, $search) !== false || stripos($otherUser->email, $search) !== false;
            });
        }

        $allUsersQuery = User::where('id', '!=', auth()->id());

        if ($search) {
            $allUsersQuery = $allUsersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }

        $allUsers = $allUsersQuery->get();

        return view('chat.mindex', [
            'conversations' => $conversations,
            'allUsers' => $allUsers,
            'search' => $search,
        ]);
    }

    public function show(Conversation $conversation)
    {
        // Check if user is part of this conversation
        if ($conversation->user_one_id !== auth()->id() && $conversation->user_two_id !== auth()->id()) {
            abort(403);
        }

        $conversations = auth()->user()->getConversations();

        return view('chat.show', [
            'conversation' => $conversation,
            'conversations' => $conversations,
        ]);
    }

        public function mshow(Conversation $conversation)
    {
        // Check if user is part of this conversation
        if ($conversation->user_one_id !== auth()->id() && $conversation->user_two_id !== auth()->id()) {
            abort(403);
        }

        $conversations = auth()->user()->getConversations();

        return view('chat.mshow', [
            'conversation' => $conversation,
            'conversations' => $conversations,
        ]);
    }

    public function startConversation(Request $request)
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);

        if (!$user || $user->id === auth()->id()) {
            return redirect()->route('chat.index')->with('error', 'Invalid user');
        }

        // Find or create conversation
        $conversation = Conversation::where(function ($query) use ($userId) {
            $query->where('user_one_id', auth()->id())
                ->where('user_two_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user_one_id', $userId)
                ->where('user_two_id', auth()->id());
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => auth()->id(),
                'user_two_id' => $userId,
            ]);
        }

        return redirect()->route('chat.show', $conversation);
    }

       public function mstartConversation(Request $request)
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);

        if (!$user || $user->id === auth()->id()) {
            return redirect()->route('chat.mindex')->with('error', 'Invalid user');
        }

        // Find or create conversation
        $conversation = Conversation::where(function ($query) use ($userId) {
            $query->where('user_one_id', auth()->id())
                ->where('user_two_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user_one_id', $userId)
                ->where('user_two_id', auth()->id());
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => auth()->id(),
                'user_two_id' => $userId,
            ]);
        }

        return redirect()->route('chat.mshow', $conversation);
    }
}
