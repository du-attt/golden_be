<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamResultsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ini_set('memory_limit', '-1');

        $filePath = storage_path('app/public/diem_thi_thpt_2024.csv');
        $file = fopen($filePath, 'r');
        fgetcsv($file); 

        $batchSize = 100;
        $data = [];

        while (($row = fgetcsv($file)) !== false) {
            $data[] = [
                'sbd'         => strval($row[0]),
                'toan'        => is_numeric($row[1]) ? floatval($row[1]) : null,
                'ngu_van'     => is_numeric($row[2]) ? floatval($row[2]) : null,
                'ngoai_ngu'   => is_numeric($row[3]) ? floatval($row[3]) : null,
                'vat_li'      => is_numeric($row[4]) ? floatval($row[4]) : null,
                'hoa_hoc'     => is_numeric($row[5]) ? floatval($row[5]) : null,
                'sinh_hoc'    => is_numeric($row[6]) ? floatval($row[6]) : null,
                'lich_su'     => is_numeric($row[7]) ? floatval($row[7]) : null,
                'dia_li'      => is_numeric($row[8]) ? floatval($row[8]) : null,
                'gdcd'        => is_numeric($row[9]) ? floatval($row[9]) : null,
                'ma_ngoai_ngu'=> !empty($row[10]) ? $row[10] : null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];

            if (count($data) >= $batchSize) {
                DB::table('users')->insert($data);
                $data = [];
            }
        }

        if (!empty($data)) {
            DB::table('users')->insert($data);
        }

        fclose($file);
    }
}
