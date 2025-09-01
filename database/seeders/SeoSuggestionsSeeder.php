<?php

namespace Database\Seeders; namespace Database\Seeders; use Illuminate\Database\Console\Seeds\WithoutModelEvents; use Illuminate\Database\Seeder; namespace 
Database\Seeders; class SeoSuggestionsSeeder extends Seeder { use Illuminate\Database\Seeder; /** use Illuminate\Support\Facades\DB; * Run the database seeds. */ 
class SeoSuggestionsSeeder extends Seeder public function run(): void { {
    public function run(): void // { } DB::table('seo_suggestions')->insert([} ['text' => 'Add descriptive alt text to all images'], ['text' => 'Use FAQ schema 
            to answer common questions'], ['text' => 'Improve internal linking structure'], ['text' => 'Ensure meta descriptions include target keywords'], 
            ['text' => 'Add more actionable CTAs for users'],
use Illuminate\Database\Seeder; ]); use Illuminate\Support\Facades\DB; }
}
class SeoSuggestionsSeeder extends Seeder { /** * Run the database seeds. */ public function run(): void { DB::table('seo_suggestions')->insert([ ['text' => 'Add 
            descriptive alt text to all images'], ['text' => 'Use FAQ schema to answer common questions'], ['text' => 'Improve internal linking structure'], 
            ['text' => 'Ensure meta descriptions include target keywords'], ['text' => 'Add more actionable CTAs for users'],
        ]);
    }
}
