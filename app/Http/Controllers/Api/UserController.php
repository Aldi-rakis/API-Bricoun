<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Answer;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Update status untuk semua user.
     */
    public function updateStatusForAllUsers()
    {
        // Ambil semua user
        $users = User::all();

        foreach ($users as $user) {
            // Ambil jawaban terakhir dari user ini
            $lastAnswer = Answer::where('user_id', $user->id)
                ->orderBy('created_at', 'desc') // Ambil yang paling baru
                ->first();

            // Jika user belum pernah mengisi jawaban, lewati user ini
            if (!$lastAnswer) {
                continue; // Lanjutkan ke user berikutnya
            }

            // Cek apakah jawaban terakhir lebih dari 2 minggu yang lalu
            $lastAnswerDate = Carbon::parse($lastAnswer->created_at);
            $twoWeeksAgo = Carbon::now()->subWeeks(2);

            if ($lastAnswerDate->lessThan($twoWeeksAgo)) {
                // Update status user menjadi "belum menjawab" jika lebih dari 2 minggu
                $user->update(['status' => 'belum menjawab']);
            } else {
                // Jika belum lebih dari 2 minggu, status tetap "sudah menjawab"
                $user->update(['status' => 'sudah menjawab']);
            }
        }

        return response()->json([
            'message' => 'Status semua user telah diperbarui',
        ], 200);
    }
}
