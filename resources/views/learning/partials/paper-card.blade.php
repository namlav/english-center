<article class="practice-card study4-test-card accent-{{ (($paperNumber-1)%3)+1 }}" style="position: relative; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 2px 4px rgba(0,0,0,0.03); transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;">
    <div class="card-top" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; background: #f8fafc; border-bottom: 1px solid #f1f5f9;">
        <span class="card-category study4-test-tag" style="background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 11px; padding: 4px 10px; border-radius: 6px;">
            {{ $paper->learningCategory->name }}
        </span>
        <span class="card-eyebrow" style="font-size: 12px; font-weight: 700; color: #94a3b8; letter-spacing: 0.05em;">
            BÀI LUYỆN {{ str_pad((string)$paperNumber,2,'0',STR_PAD_LEFT) }}
        </span>
    </div>
    <div class="card-body" style="padding: 20px; display: flex; flex-direction: column; flex: 1;">
        <h3 style="font-size: 17px; font-weight: 700; line-height: 1.45; margin: 0 0 14px; min-height: 48px;">
            <a href="{{ route('practice.show',$paper->slug) }}" style="color: #0f172a; text-decoration: none;">
                {{ str_contains($paper->title,' — ') ? \Illuminate\Support\Str::after($paper->title,' — ') : $paper->title }}
            </a>
        </h3>
        <div class="card-meta" style="display: flex; align-items: center; gap: 14px; font-size: 13px; color: #64748b; margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9;">
            <span style="display: inline-flex; align-items: center; gap: 5px;"><x-study-icon name="clock"/> {{ (int)ceil($paper->duration_seconds/60) }} phút</span>
            <span>·</span>
            <span style="display: inline-flex; align-items: center; gap: 5px;"><x-study-icon name="file"/> {{ $paper->questions_count }} câu hỏi</span>
        </div>
        <div class="card-bottom" style="margin-top: auto; display: flex; align-items: center; justify-content: space-between;">
            <span style="font-size: 12px; color: #64748b;">{{ $paper->sections_count }} phần luyện tập</span>
            <a href="{{ route('practice.show',$paper->slug) }}" class="btn-study4-test" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-size: 13px; font-weight: 700; padding: 7px 14px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                Mở bài luyện <x-study-icon name="arrow"/>
            </a>
        </div>
    </div>
</article>
