<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ResultController extends Controller
{
    public function getBySBD($sbd) {
        $result = User::where('sbd', $sbd)->first();
        if(!$result){
            return response()->json(['message' => 'No results found'], 404);  
        }
        return response()->json($result)->header('Access-Control-Allow-Origin', '*');
    }
    public function getTop10A() {
        $top10 = User::orderByRaw('(toan + vat_li + hoa_hoc) DESC')->limit(10)->get();
        return response()->json($top10)->header('Access-Control-Allow-Origin', '*');
    
    }
    // public function classifyScores(Request $request) {
    //     $perPage = $request->input('per_page', 100);
    //     $subject = $request->input('subject'); // Môn học cần lọc
    //     $level = $request->input('level'); // Mức điểm cần lọc
    
    //     // Xác định điều kiện lọc theo mức điểm
    //     $query = User::select('sbd', 'toan', 'ngu_van', 'ngoai_ngu', 'vat_li', 'hoa_hoc', 'sinh_hoc', 'lich_su', 'dia_li', 'gdcd');
    
    //     if ($subject && in_array($subject, ['toan', 'ngu_van', 'ngoai_ngu', 'vat_li', 'hoa_hoc', 'sinh_hoc', 'lich_su', 'dia_li', 'gdcd'])) {
    //         if ($level == 'gioi') {
    //             $query->where($subject, '>=', 8);
    //         } elseif ($level == 'kha') {
    //             $query->whereBetween($subject, [6, 7.99]);
    //         } elseif ($level == 'tb') {
    //             $query->whereBetween($subject, [4, 5.99]);
    //         } elseif ($level == 'yeu') {
    //             $query->where($subject, '<', 4);
    //         }
    //     }
    
    //     // Thực hiện phân trang
    //     $students = $query->paginate($perPage);
    
    //     // Trả về dữ liệu JSON cho frontend
    //     return response()->json([
    //         'data' => $students->items(),
    //         'pagination' => [
    //             'current_page' => $students->currentPage(),
    //             'per_page' => $students->perPage(),
    //             'total' => $students->total(),
    //             'last_page' => $students->lastPage()
    //         ]
    //     ])->header('Access-Control-Allow-Origin', '*');
    // }

    public function classifyScores(Request $request) {
        $subject = $request->input('subject'); 
    
        $valid_subjects = ['toan', 'ngu_van', 'ngoai_ngu', 'vat_li', 'hoa_hoc', 'sinh_hoc', 'lich_su', 'dia_li', 'gdcd'];
    
        if ($subject && in_array($subject, $valid_subjects)) {

            $total_students = User::count();

            $count_8_or_more = User::where($subject, '>=', 8)->count();
            $count_6_to_8 = User::whereBetween($subject, [6, 7.99])->count();
            $count_4_to_6 = User::whereBetween($subject, [4, 5.99])->count();
            $count_below_4 = User::where($subject, '<', 4)->count();
    
            $average_score = User::avg($subject);
    
            $percent_8_or_more = round(($count_8_or_more / $total_students) * 100, 2);
            $percent_6_to_8 = round(($count_6_to_8 / $total_students) * 100, 2);
            $percent_4_to_6 = round(($count_4_to_6 / $total_students) * 100, 2);
            $percent_below_4 = round(($count_below_4 / $total_students) * 100, 2);
    
            return response()->json([
                'total_students' => $total_students,
                'average_score' => round($average_score, 2), 
                'score_distribution' => [
                    'count_8_or_more' => $count_8_or_more,
                    'count_6_to_8' => $count_6_to_8,
                    'count_4_to_6' => $count_4_to_6,
                    'count_below_4' => $count_below_4,
                ],
                'percentage_distribution' => [
                    'percent_8_or_more' => $percent_8_or_more,
                    'percent_6_to_8' => $percent_6_to_8,
                    'percent_4_to_6' => $percent_4_to_6,
                    'percent_below_4' => $percent_below_4,
                ],
            ])->header('Access-Control-Allow-Origin', '*');
        }
    
        return response()->json(['error' => 'Invalid subject'], 400);
    }
}
