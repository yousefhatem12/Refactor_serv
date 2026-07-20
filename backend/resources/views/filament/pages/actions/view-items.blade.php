<div dir="{{ $isAr ? 'rtl' : 'ltr' }}" style="color:#e5e7eb; font-size:14px;">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; padding:16px 18px; margin-bottom:14px; border:1px solid rgba(148,163,184,.18); border-radius:14px; background:linear-gradient(135deg, rgba(37,99,235,.14), rgba(30,41,59,.18));">
        <div style="min-width:0;">
            <div style="font-size:16px; font-weight:700; color:#f8fafc;">{{ $isAr ? 'المقالات المُنشأة' : 'Generated articles' }}</div>
            <div style="margin-top:5px; color:#94a3b8; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $request->main_keyword }}</div>
        </div>
        <div style="flex:none; padding:6px 10px; border-radius:999px; background:rgba(148,163,184,.14); color:#cbd5e1; font-size:12px; font-weight:600;">{{ $request->items->count() }} {{ $isAr ? 'مقال' : 'items' }}</div>
    </div>

    <div style="display:grid; gap:12px;">
        @forelse($request->items as $item)
            <section style="overflow:hidden; border:1px solid rgba(148,163,184,.18); border-radius:14px; background:rgba(15,23,42,.42);">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:14px; padding:14px 16px; border-bottom:1px solid rgba(148,163,184,.14);">
                    <div style="min-width:0;">
                        <div style="font-weight:700; color:#f8fafc; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $item->keyword }}</div>
                        <div style="margin-top:4px; color:#94a3b8; font-size:12px;">{{ $isAr ? 'المحاولة' : 'Attempt' }} #{{ $item->attempts }}</div>
                    </div>
                    @if($item->status === 'completed')
                        <span style="flex:none; padding:5px 10px; border:1px solid rgba(52,211,153,.28); border-radius:999px; background:rgba(16,185,129,.12); color:#6ee7b7; font-size:12px; font-weight:700;">{{ $isAr ? 'مكتمل' : 'Completed' }}</span>
                    @elseif($item->status === 'failed')
                        <span style="flex:none; padding:5px 10px; border:1px solid rgba(248,113,113,.28); border-radius:999px; background:rgba(239,68,68,.12); color:#fca5a5; font-size:12px; font-weight:700;">{{ $isAr ? 'فشل' : 'Failed' }}</span>
                    @elseif($item->status === 'generating')
                        <span style="flex:none; padding:5px 10px; border:1px solid rgba(251,191,36,.28); border-radius:999px; background:rgba(245,158,11,.12); color:#fcd34d; font-size:12px; font-weight:700;">{{ $isAr ? 'جارٍ التوليد' : 'Generating' }}</span>
                    @elseif($item->status === 'repairing')
                        <span style="flex:none; padding:5px 10px; border:1px solid rgba(251,191,36,.28); border-radius:999px; background:rgba(245,158,11,.12); color:#fcd34d; font-size:12px; font-weight:700;">{{ $isAr ? 'جارٍ إصلاح التحذيرات' : 'Fixing warnings' }}</span>
                    @else
                        <span style="flex:none; padding:5px 10px; border:1px solid rgba(148,163,184,.22); border-radius:999px; background:rgba(148,163,184,.1); color:#cbd5e1; font-size:12px; font-weight:700;">{{ $isAr ? 'قيد الانتظار' : 'Pending' }}</span>
                    @endif
                </div>

                <div style="padding:16px;">
                    @if($item->status === 'completed' && $item->quality_report)
                        <div style="display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:10px;">
                            <div style="padding:11px 12px; border-radius:10px; background:rgba(148,163,184,.09);"><div style="color:#94a3b8; font-size:11px;">{{ $isAr ? 'الكلمات' : 'Words' }}</div><strong style="display:block; margin-top:4px; color:#f8fafc;">{{ $item->quality_report['word_count'] ?? 0 }}</strong></div>
                            <div style="padding:11px 12px; border-radius:10px; background:rgba(148,163,184,.09);"><div style="color:#94a3b8; font-size:11px;">{{ $isAr ? 'الكثافة' : 'Density' }}</div><strong style="display:block; margin-top:4px; color:#f8fafc;">{{ isset($item->quality_report['keyword_density']) && $item->quality_report['keyword_density'] !== null ? $item->quality_report['keyword_density'] . '%' : 'N/A' }}</strong></div>
                            <div style="padding:11px 12px; border-radius:10px; background:rgba(148,163,184,.09);"><div style="color:#94a3b8; font-size:11px;">{{ $isAr ? 'سهولة القراءة' : 'Readability' }}</div><strong style="display:block; margin-top:4px; color:#f8fafc;">{{ $item->quality_report['readability_score'] ?? 0 }}/100</strong></div>
                            <div style="padding:11px 12px; border-radius:10px; background:rgba(148,163,184,.09);"><div style="color:#94a3b8; font-size:11px;">{{ $isAr ? 'العناوين' : 'Headings' }}</div><strong style="display:block; margin-top:4px; color:#f8fafc;">H2 {{ $item->quality_report['h2_count'] ?? 0 }} · H3 {{ $item->quality_report['h3_count'] ?? 0 }}</strong></div>
                        </div>
                        @if($item->error_message)
                            <div style="margin-top:12px; padding:11px 13px; border:1px solid rgba(248,113,113,.25); border-radius:10px; background:rgba(239,68,68,.08); color:#fca5a5; font-size:12px;">{{ $item->error_message }}</div>
                        @endif
                        @if(!empty($item->quality_report['warnings']))
                            <div style="margin-top:12px; padding:11px 13px; border:1px solid rgba(251,191,36,.24); border-radius:10px; background:rgba(245,158,11,.08); color:#fcd34d; font-size:12px;">
                                <div style="font-weight:700;">{{ $isAr ? 'تنبيهات الجودة' : 'Quality warnings' }} ({{ count($item->quality_report['warnings']) }})</div>
                                <ul style="margin:9px 0 0; padding-inline-start:18px; color:#fde68a; line-height:1.7;">@foreach($item->quality_report['warnings'] as $warning)<li>{{ $warning }}</li>@endforeach</ul>
                            </div>
                        @endif
                    @elseif($item->status === 'failed')
                        <div style="padding:12px 14px; border:1px solid rgba(248,113,113,.25); border-radius:10px; background:rgba(239,68,68,.08); color:#fca5a5; font-size:13px; line-height:1.6;">{{ $item->error_message }}</div>
                    @else
                        <div style="color:#94a3b8; font-size:13px;">{{ $isAr ? 'لم تُنتج نتيجة بعد.' : 'No result yet.' }}</div>
                    @endif
                </div>

                @if($item->status === 'completed')
                    <div style="display:flex; flex-wrap:wrap; gap:9px; padding:0 16px 16px;">
                        @if(!empty($item->quality_report['warnings'] ?? []))
                            <button type="button" wire:click="fixWarnings({{ $item->id }})" wire:confirm="{{ $isAr ? 'هل تريد إصلاح تحذيرات هذا المقال فقط؟' : 'Repair warnings for this article only?' }}" style="border:0; border-radius:9px; padding:9px 13px; background:#f97316; color:#fff; font-weight:700; cursor:pointer;">
                                {{ $isAr ? 'إصلاح التحذيرات' : 'Fix warnings' }}
                            </button>
                        @endif
                        @if($item->post_id)
                            @if(!$item->post?->is_published)
                                <button type="button" wire:click="publishArticle({{ $item->id }})" wire:confirm="{{ $isAr ? 'هل تريد نشر هذا المقال الآن؟' : 'Publish this article now?' }}" style="border:0; border-radius:9px; padding:9px 13px; background:#16a34a; color:#fff; font-weight:700; cursor:pointer;">
                                    {{ $isAr ? 'نشر المقال' : 'Publish' }}
                                </button>
                            @else
                                <span style="display:inline-flex; align-items:center; border-radius:9px; padding:9px 13px; background:rgba(22,163,74,.14); color:#86efac; font-weight:700;">
                                    {{ $isAr ? 'منشور' : 'Published' }}
                                </span>
                            @endif
                            <a href="{{ url('/admin/posts/' . $item->post_id . '/edit') }}" style="display:inline-flex; align-items:center; border-radius:9px; padding:9px 13px; background:#d97706; color:#fff; font-weight:700; text-decoration:none;">
                                {{ $isAr ? 'تعديل المقال' : 'Edit draft' }}
                            </a>
                        @endif
                    </div>
                @endif
            </section>
        @empty
            <div style="padding:32px; border:1px dashed rgba(148,163,184,.25); border-radius:14px; text-align:center; color:#94a3b8;">{{ $isAr ? 'لا توجد مقالات.' : 'No articles found.' }}</div>
        @endforelse
    </div>

    <style>
        @media (max-width: 640px) {
            .fi-modal-window [style*="grid-template-columns:repeat(4"] { grid-template-columns:repeat(2,minmax(0,1fr)) !important; }
        }
    </style>
</div>
