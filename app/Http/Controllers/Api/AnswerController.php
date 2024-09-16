<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use app\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Jobs\ResetUserStatus;
use Carbon\Carbon;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Dapatkan user yang sedang login
        $user = Auth::user();

        // Lakukan join pada tabel answers, questionnaires, dan users
        $answers = Answer::join('questionnaires', 'questionnaires.id', '=', 'answers.question_id')
            ->join('users', 'users.id', '=', 'answers.user_id')
            ->where('answers.user_id', $user->id)
            ->select('answers.*', 'questionnaires.question as question', 'users.name as user_name')
            ->get();

        // Kelompokkan jawaban berdasarkan tanggal `created_at`
        $groupedAnswers = $answers->groupBy(function ($answer) {
            return $answer->created_at->format('d-m-Y');
        });


        // Siapkan array untuk mengelompokkan jawaban per tanggal dan hitung total skor
        $result = $groupedAnswers->map(function ($answers, $date) {
            $totalScore = $answers->sum('score'); // Hitung total skor per tanggal
            return [

                'date' => $date,
                'total_score' => $totalScore, // Masukkan total skor ke dalam hasil
                'answers' => $answers->map(function ($answer) {
                    return [

                        'question' => $answer->question,
                        'score' => $answer->score,

                    ];
                })
            ];
        });

        // Kembalikan data dalam format JSON
        return response()->json([
            'data' => $result->values(),
        ], 200);
    }



    /**
     * Store answers from the questionnaire.
     */
   /**
 * Store answers from the questionnaire, allowing only once every 2 weeks.
 */
/**
 * Store answers from the questionnaire, allowing only once every 2 weeks.
 */
/**
 * Store answers from the questionnaire, allowing only once every 2 weeks.
 */
public function store(Request $request)
{
    $user = Auth::user();

    // Cek apakah user sudah menjawab dalam 2 minggu terakhir
    $lastAnswer = Answer::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->first();

    // Jika jawaban terakhir ditemukan dan belum lebih dari 2 minggu, kembalikan error
    if ($lastAnswer && $lastAnswer->created_at->diffInDays(now()) < 14) {
        return response()->json([
            'message' => 'Anda hanya dapat mengisi kuisioner setiap 2 minggu.',
        ], 403); // 403 Forbidden
    }

    // Validasi input
    $validator = Validator::make($request->all(), [
        'answers' => 'required|array',
        'answers.*.question_id' => 'required|integer|exists:questionnaires,id',
        'answers.*.score' => 'required|integer|min:0|max:3',
    ]);

    // Jika validasi gagal, kembalikan respon dengan error
    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    // Iterasi dan simpan jawaban ke database
    foreach ($validator->validated()['answers'] as $answer) {
        Answer::create([
            'user_id' => $user->id,
            'question_id' => $answer['question_id'],
            'score' => $answer['score'],
        ]);
    }

       

        // Ubah status user menjadi 'sudah menjawab'
        $user->update(['status' => 'sudah menjawab']);

        // // Kirim Job untuk mengubah status kembali setelah 2 minggu
        // ResetUserStatus::dispatch($user)->delay(Carbon::now()->addMinutes(2));


    return response()->json(['message' => 'Jawaban sudah disimpan dan status telah diubah.'], 200);
}




    /**
     * Get answers grouped by user.
     */

 public function getAllAnswersByUser()
{
    // Ambil semua jawaban dan lakukan join dengan tabel questionnaires dan users
    $answers = Answer::join('questionnaires', 'questionnaires.id', '=', 'answers.question_id')
        ->join('users', 'users.id', '=', 'answers.user_id')
        ->select('answers.*', 'questionnaires.question as question', 'users.name as user_name', 'users.kelas as kelas', 'users.umur as umur')
        ->get()
        ->groupBy('user_id'); // Kelompokkan jawaban berdasarkan user_id

    // Siapkan array untuk mengelompokkan jawaban per user
    $groupedAnswers = $answers->map(function ($userAnswers, $userId) {

        // Dapatkan tanggal terakhir pengisian untuk setiap user dan atur timezone ke Asia/Jakarta
        $lastDate = $userAnswers->max('created_at')->setTimezone('Asia/Jakarta');

        // Format untuk menampilkan hari, tanggal, bulan, tahun dalam bahasa Indonesia
        $formattedDate = $lastDate->translatedFormat('l, d-m-Y'); // Contoh: "Senin, 01-09-2024"

        // Ambil hanya jawaban yang berada pada tanggal terakhir pengisian
        $answersOnLastDate = $userAnswers->filter(function ($answer) use ($lastDate) {
            return $answer->created_at->format('Y-m-d') == $lastDate->format('Y-m-d');
        });

        // Hitung total skor dari jawaban pada tanggal terakhir
        $totalScoreOnLastDate = $answersOnLastDate->sum('score');

        return [
            'user_id' => $userId,
            'username' => $userAnswers->first()->user_name,
            'kelas' => $userAnswers->first()->kelas,
            'umur' => $userAnswers->first()->umur,
            'tanggal_terakhir_pengisian' => $formattedDate, // Tambahkan tanggal terakhir pengisian dengan hari
            'total_score_terbaru' => $totalScoreOnLastDate, // Tambahkan total skor dari tanggal terbaru
            'Jawaban_terbaru' => $answersOnLastDate->map(function ($answer) {
                return [
                    'question' => $answer->question,
                    'score' => $answer->score,
                ];
            })->values() // Jawaban hanya dari tanggal terbaru
        ];
    });

    // Kembalikan data dalam format JSON
    return response()->json([
        'message' => 'data jawaban',
        'data' => $groupedAnswers->values()
    ], 200);
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
{
    // Ambil jawaban terbaru berdasarkan `created_at`
    $latestAnswerDate = Answer::where('user_id', $id)
        ->orderBy('created_at', 'desc')
        ->first()
        ->created_at->format('Y-m-d');

    // Lakukan join pada tabel answers, questionnaires, dan users
    $answers = Answer::join('questionnaires', 'questionnaires.id', '=', 'answers.question_id')
        ->join('users', 'users.id', '=', 'answers.user_id')
        ->where('answers.user_id', $id)
        ->whereDate('answers.created_at', $latestAnswerDate) // Hanya ambil jawaban pada tanggal terbaru
        ->select('answers.*', 'questionnaires.question as question', 'users.name as user_name', 'users.kelas as kelas', 'users.umur as umur')
        ->get();

    // Ambil data user dari jawaban pertama (karena semua jawaban berasal dari user yang sama)
    $user = $answers->first();

    // Kelompokkan jawaban berdasarkan tanggal `created_at` dan urutkan berdasarkan tanggal terbaru
    $groupedAnswers = $answers->groupBy(function ($answer) {
        return $answer->created_at->format('Y-m-d'); // Format tanggal tahun-bulan-hari untuk urutan
    })->sortKeysDesc();

    // Siapkan array untuk mengelompokkan jawaban per tanggal dan hitung total skor
    $result = $groupedAnswers->map(function ($answers, $date) {
        $totalScore = $answers->sum('score'); // Hitung total skor per tanggal
        return [
            'date' => Carbon::parse($date)->translatedFormat('l, d F Y'), // Format tanggal dengan hari dan dalam bahasa Indonesia
            'total_score' => $totalScore, // Masukkan total skor ke dalam hasil
            'answers' => $answers->map(function ($answer) {
                return [
                    'question' => $answer->question,
                    'score' => $answer->score,
                ];
            })
        ];
    });

    // Strukturkan respons JSON agar informasi user hanya muncul sekali
    return response()->json([
        'user' => [
            'username' => $user->user_name,
            'kelas' => $user->kelas,
            'umur' => $user->umur,
        ],
        'data' => $result->values(), // Daftar jawaban yang telah dikelompokkan per tanggal
    ], 200);
}


 

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
