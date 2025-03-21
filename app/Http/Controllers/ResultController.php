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
        return response()->json($result);
    }
    public function getTop10A($name) {
        if($name == 'A'){
            $top10 = User::orderByRaw('(toan + vat_li + hoa_hoc) DESC')->limit(10)->get();
            return response()->json($top10);
        }
        if($name == 'B'){
            $top10 = User::orderByRaw('(toan + sinh_hoc + hoa_hoc) DESC')->limit(10)->get();
            return response()->json($top10);
        }
        if($name == 'C'){
            $top10 = User::orderByRaw('(ngu_van + lich_su + dia_li) DESC')->limit(10)->get();
            return response()->json($top10);
        }
        if($name == 'D'){
            $top10 = User::orderByRaw('(toan + ngu_van + ngoai_ngu) DESC')->limit(10)->get();
            return response()->json($top10);
        }
        if($name == 'A1'){
            $top10 = User::orderByRaw('(toan + vat_li + ngoai_ngu) DESC')->limit(10)->get();
            return response()->json($top10);
        }
    
    }

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
            ]);
        }
    
        return response()->json(['error' => 'Invalid subject'], 400);
    }
    public function classifyScoresDetail(Request $request) {
        $subject = $request->input('subject');
        
        $valid_subjects = ['toan', 'ngu_van', 'ngoai_ngu', 'vat_li', 'hoa_hoc', 'sinh_hoc', 'lich_su', 'dia_li', 'gdcd'];
        
        if ($subject && in_array($subject, $valid_subjects)) {
            $scores_distribution = [];
            $step = ($subject == "toan" || $subject == "ngoai_ngu") ? 0.2 : 0.25;
            
            for ($i = 0; $i <= 10; $i += $step) {
                $count = User::where($subject, '=', $i)->count();
                $scores_distribution[number_format($i, 2)] = $count;
            }
            $top_students = User::orderBy($subject, 'DESC')->limit(10)->get();
            // return response()->json($scores_distribution);
            return response()->json([
                'score_distribution' => $scores_distribution,
                'top_students' => $top_students
            ]);
        }
        
        return response()->json(['error' => 'Invalid subject'], 400);
    }
}
