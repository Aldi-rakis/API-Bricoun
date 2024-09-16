<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class LogoutController extends Controller
{
    /**
     * Handle the logout request.
     */
    public function __invoke(Request $request)
    {
      
        $request->user()->currentAccessToken()->delete();

      

            return response()->json([
                'message' => 'Logout berhasil, token telah dihapus.'
            ], 200);
        }
}
