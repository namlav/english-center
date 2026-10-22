<?php
namespace Database\Seeders;
use App\Models\PublicTeacher;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class WebsiteContentSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->call(PublicCourseCatalogSeeder::class);
            foreach(json_decode(file_get_contents(database_path('data/prep-teachers.json')),true,512,JSON_THROW_ON_ERROR) as $record) {
                PublicTeacher::firstOrCreate(['slug'=>$record['slug']],$record);
            }
            SiteSetting::firstOrCreate(['key'=>'home'],['value'=>SiteSetting::defaults()]);
        });
    }
}
