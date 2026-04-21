{{--
  Tipster layout — uses the main app shell (navigation, bottom nav, footer).
  CSS is pushed to the <head> via @push so it lands before </head>.
  All ts-* classes are preserved so existing tipster views work unchanged.
--}}
@push('head')
<style>
    .ts-stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .ts-stat {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 1rem 1.1rem;
    }
    .ts-stat-value { font-family:var(--fm); font-size:1.75rem; font-weight:700; color:var(--text); line-height:1; }
    .ts-stat-value.accent  { color: var(--accent); }
    .ts-stat-value.accent2 { color: var(--accent2); }
    .ts-stat-value.red     { color: #ef4444; }
    .ts-stat-label { font-size:.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; margin-top:.3rem; }

    .ts-table { width:100%; border-collapse:collapse; }
    .ts-table th {
        text-align:left; font-size:.68rem; color:var(--muted);
        text-transform:uppercase; letter-spacing:.07em; font-weight:500;
        padding:.5rem .85rem; border-bottom:1px solid var(--border);
        background:var(--card2,#0f172a);
    }
    .ts-table td {
        padding:.65rem .85rem; font-size:.85rem; color:var(--text);
        border-bottom:1px solid var(--border); vertical-align:middle;
    }
    .ts-table tr:hover td { background:var(--card2,#0f172a); }

    .badge { display:inline-block; font-size:.67rem; padding:.15rem .45rem; border-radius:4px; font-weight:600; letter-spacing:.04em; }
    .badge-green  { background:rgba(0,229,160,.12);   color:var(--accent);  border:1px solid rgba(0,229,160,.25); }
    .badge-yellow { background:rgba(245,197,24,.12);  color:var(--accent2); border:1px solid rgba(245,197,24,.25); }
    .badge-red    { background:rgba(239,68,68,.12);   color:#ef4444;        border:1px solid rgba(239,68,68,.25); }
    .badge-gray   { background:rgba(100,116,139,.12); color:var(--muted);   border:1px solid var(--border); }

    .ts-input, .ts-select, .ts-textarea {
        width:100%; background:var(--surface); border:1px solid var(--border);
        color:var(--text); padding:.5rem .75rem; border-radius:6px;
        font-size:.88rem; font-family:var(--fp); transition:border-color .15s;
    }
    .ts-input:focus, .ts-select:focus, .ts-textarea:focus { outline:none; border-color:var(--accent2); }
    .ts-textarea { resize:vertical; min-height:120px; }
    .ts-label { display:block; font-size:.75rem; color:var(--muted); margin-bottom:.35rem; text-transform:uppercase; letter-spacing:.06em; }
    .ts-form-group { margin-bottom:1.1rem; }

    .btn-submit    { background:var(--accent2); color:#07090e; font-family:var(--fh); font-size:.85rem; letter-spacing:.06em; padding:.5rem 1.1rem; border-radius:5px; border:none; cursor:pointer; text-decoration:none; display:inline-block; }
    .btn-submit:hover { opacity:.85; }
    .btn-secondary { background:transparent; color:var(--text); border:1px solid var(--border); font-size:.85rem; padding:.5rem 1rem; border-radius:5px; cursor:pointer; text-decoration:none; display:inline-block; }
    .btn-secondary:hover { border-color:var(--dim); }
    .btn-danger    { background:rgba(239,68,68,.1); color:#ef4444; border:1px solid rgba(239,68,68,.3); font-size:.82rem; padding:.35rem .7rem; border-radius:4px; cursor:pointer; text-decoration:none; display:inline-block; }
    .btn-danger:hover { background:rgba(239,68,68,.2); }
    .btn-sm { padding:.3rem .65rem; font-size:.78rem; }

    .conf-bar  { height:4px; border-radius:2px; background:var(--border); overflow:hidden; margin-top:.35rem; }
    .conf-fill { height:100%; border-radius:2px; }

    .ts-content { max-width:1280px; margin:0 auto; padding:1.5rem 2rem; }
    @media (max-width: 767px) {
        .ts-content { padding:1rem .85rem; }
        .ts-stat-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); }
        .ts-table { display:block; overflow-x:auto; -webkit-overflow-scrolling:touch; white-space:nowrap; }
    }
</style>
@endpush

<x-app-layout>

{{-- Page heading bar --}}
<div style="background:var(--surface);border-bottom:1px solid var(--border)">
<div style="max-width:1280px;margin:0 auto;padding:.75rem 2rem;display:flex;align-items:center;justify-content:space-between;gap:1rem">
    <div>
        <div style="font-family:var(--fh);font-size:1.1rem;letter-spacing:.07em;color:var(--text)">{{ $title ?? 'Tipster Portal' }}</div>
        @isset($breadcrumb)
        <div style="font-size:.72rem;color:var(--muted);margin-top:.1rem">{{ $breadcrumb }}</div>
        @endisset
    </div>
    @isset($actions)
    <div style="display:flex;align-items:center;gap:.75rem;flex-shrink:0">
        {{ $actions }}
    </div>
    @endisset
</div>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div style="background:rgba(0,229,160,.08);border-bottom:1px solid rgba(0,229,160,.2);padding:.65rem 1.5rem;font-size:.83rem;color:var(--accent)">
    ✓ {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:rgba(239,68,68,.08);border-bottom:1px solid rgba(239,68,68,.2);padding:.65rem 1.5rem;font-size:.83rem;color:#ef4444">
    ✗ {{ session('error') }}
</div>
@endif
@if($errors->any())
<div style="background:rgba(239,68,68,.08);border-bottom:1px solid rgba(239,68,68,.2);padding:.65rem 1.5rem;font-size:.83rem;color:#ef4444">
    @foreach($errors->all() as $error)<div>✗ {{ $error }}</div>@endforeach
</div>
@endif

<div class="ts-content">
    {{ $slot }}
</div>

</x-app-layout>
