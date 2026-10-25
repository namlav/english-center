<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SiteSetting extends Model
{
    protected $fillable = ['key','value'];
    protected function casts(): array { return ['value'=>'array']; }
    public static function home(): array
    {
        return array_replace(self::defaults(), Schema::hasTable('site_settings') ? (self::where('key','home')->value('value') ?? []) : []);
    }
    public static function defaults(): array
    {
        return ['title'=>'Học tiếng Anh, cùng lộ trình phù hợp.', 'description'=>'Khám phá chương trình học và luyện tập mỗi ngày cùng English Center.', 'image'=>'images/catalog/study4/study-banner.jpg', 'teacher_title'=>'Gặp gỡ đội ngũ giáo viên', 'teacher_description'=>'Tìm hiểu hồ sơ và chuyên môn của các giáo viên được giới thiệu từ PREP.'];
    }
}
