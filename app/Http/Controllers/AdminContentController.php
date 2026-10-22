<?php
namespace App\Http\Controllers;

use App\Models\PublicTeacher;
use App\Models\ReferencePage;
use App\Models\SiteSetting;
use App\Services\Study4CourseCatalog;
use Database\Seeders\WebsiteContentSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdminContentController extends Controller
{
    public function index()
    {
        if(!Schema::hasTable('public_teachers') || !Schema::hasTable('site_settings')) return view('learning.admin.setup');
        return view('learning.admin.index', ['courses'=>ReferencePage::where('provider','study4')->where('content_type','course')->orderBy('id')->get(), 'teachers'=>PublicTeacher::orderBy('position')->get()]);
    }
    public function import()
    {
        if(!Schema::hasTable('public_teachers') || !Schema::hasTable('site_settings')) return redirect()->route('website.index')->with('error','Cần cập nhật hai bảng nội dung trước khi nạp dữ liệu.');
        (new WebsiteContentSeeder)->run();
        if(Schema::hasColumn('courses','intro_video_url')) { (new \Database\Seeders\CourseShopSeeder)->run(); (new \Database\Seeders\CoursePresentationSeeder)->run(); }
        return back()->with('success','Đã nạp dữ liệu STUDY4/PREP còn thiếu. Các chỉnh sửa đã có được giữ nguyên.');
    }
    public function upgradeSql() { return response()->download(database_path('sql/CAP_NHAT_WEBSITE_41_BANG.sql')); }
    private function courseOnly(ReferencePage $page): void { abort_unless($page->provider==='study4' && $page->content_type==='course',404); }
    public function editCourse(ReferencePage $referencePage)
    {
        $this->courseOnly($referencePage);
        return view('learning.admin.course',['page'=>$referencePage,'labels'=>Study4CourseCatalog::GROUPS]);
    }
    public function updateCourse(Request $request, ReferencePage $referencePage)
    {
        $this->courseOnly($referencePage);
        $data=$request->validate(['title'=>['required','string','max:255'],'summary'=>['required','string','max:5000'], 'catalog_group'=>['required',Rule::in(array_keys(Study4CourseCatalog::GROUPS))], 'price'=>['required','integer','min:0','max:1000000000'], 'list_price'=>['required','integer','gte:price','max:1000000000'], 'position'=>['required','integer','min:0','max:9999'], 'highlights'=>['nullable','string','max:8000'], 'facts'=>['nullable','string','max:8000'], 'curriculum_topics'=>['nullable','string','max:12000'], 'is_visible'=>['nullable','boolean'], 'image'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:4096']]);
        $image=$this->upload($request);
        DB::transaction(function () use ($referencePage,$request,$data,$image): void {
            $metadata=array_replace($referencePage->metadata??[], ['catalog_group'=>$data['catalog_group'],'catalog_group_label'=>Study4CourseCatalog::GROUPS[$data['catalog_group']], 'price'=>(int)$data['price'],'list_price'=>(int)$data['list_price'],'position'=>(int)$data['position'],'is_visible'=>$request->boolean('is_visible')]);
            foreach(['highlights','facts','curriculum_topics'] as $key) $metadata[$key]=$this->lines($data[$key]??'');
            $referencePage->update(['title'=>$data['title'],'summary'=>$data['summary'],'metadata'=>$metadata]);
            if($image){
                $cover=$referencePage->images()->where('role','cover')->first();
                if($cover) $cover->update($image+['alt_text'=>$data['title']]);
                else $referencePage->images()->create($image+['alt_text'=>$data['title'],'role'=>'cover','position'=>1,'asset_key'=>hash('sha256',$image['local_path'])]);
            }
        });
        return redirect()->route('website.index')->with('success','Đã lưu bản ghi nguồn. Trang công khai được chỉnh sửa tại mục Khóa học & video.');
    }
    public function createTeacher() { return view('learning.admin.teacher',['teacher'=>new PublicTeacher(['provider'=>'English Center','is_active'=>true,'position'=>0])]); }
    public function editTeacher(PublicTeacher $publicTeacher) { return view('learning.admin.teacher',['teacher'=>$publicTeacher]); }
    public function storeTeacher(Request $request) { return $this->saveTeacher($request,new PublicTeacher); }
    public function updateTeacher(Request $request, PublicTeacher $publicTeacher) { return $this->saveTeacher($request,$publicTeacher); }
    private function saveTeacher(Request $request, PublicTeacher $teacher)
    {
        $data=$request->validate(['name'=>['required','string','max:255'], 'slug'=>['required','string','max:150','regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',Rule::unique('public_teachers','slug')->ignore($teacher->id)], 'bio'=>['required','string','max:5000'], 'provider'=>['required','string','max:50'], 'source_url'=>['nullable','url:https','max:500'], 'qualifications'=>['nullable','string','max:8000'], 'position'=>['required','integer','min:0','max:9999'], 'is_active'=>['nullable','boolean'], 'image'=>[$teacher->exists?'nullable':'required','image','mimes:jpg,jpeg,png,webp','max:4096']]);
        $image=$this->upload($request);
        $teacher->fill(collect($data)->except(['image','qualifications','is_active'])->all());
        $teacher->qualifications=$this->lines($data['qualifications']??''); $teacher->is_active=$request->boolean('is_active');
        if($image) $teacher->photo_path=$image['local_path'];
        $teacher->save();
        return redirect()->route('website.index')->with('success','Đã lưu hồ sơ giáo viên và cập nhật banner.');
    }
    public function editHome() { return view('learning.admin.home',['home'=>SiteSetting::home()]); }
    public function updateHome(Request $request)
    {
        $data=$request->validate(['title'=>['required','string','max:200'],'description'=>['required','string','max:1000'],'teacher_title'=>['required','string','max:200'],'teacher_description'=>['required','string','max:1000'],'image'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:4096']]);
        $home=array_replace(SiteSetting::home(),collect($data)->except('image')->all());
        if($image=$this->upload($request)) $home['image']=$image['local_path'];
        SiteSetting::updateOrCreate(['key'=>'home'],['value'=>$home]);
        return redirect()->route('website.index')->with('success','Đã cập nhật nội dung trang chủ.');
    }
    private function lines(string $text): array { return array_slice(array_values(array_filter(array_map('trim',preg_split('/\R/u',$text)))),0,50); }
    private function upload(Request $request): ?array
    {
        if(!$request->hasFile('image')) return null;
        $file=$request->file('image'); $directory=public_path('images/uploads'); File::ensureDirectoryExists($directory);
        $name=bin2hex(random_bytes(16)).'.'.$file->extension(); $path=$directory.DIRECTORY_SEPARATOR.$name;
        // OneDrive directory attributes can make is_writable() report false on Windows.
        // Write the already-validated image directly rather than relying on that pre-check.
        if(File::put($path,File::get($file->getRealPath()))===false) throw \Illuminate\Validation\ValidationException::withMessages(['image'=>'Không lưu được ảnh. Kiểm tra quyền ghi thư mục public/images/uploads.']);
        $size=getimagesize($path);
        return ['local_path'=>'images/uploads/'.$name, 'source_url'=>'local-upload', 'mime_type'=>$size['mime'], 'width'=>$size[0], 'height'=>$size[1], 'file_size'=>filesize($path), 'content_sha256'=>hash_file('sha256',$path)];
    }
}

