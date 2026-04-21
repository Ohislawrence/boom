<x-app-layout>

<x-slot name="title">My Dashboard — SCOUT</x-slot>

<div style="max-width:1280px;margin:0 auto;padding:2rem">

    <h1 style="font-family:var(--fh);font-size:2rem;letter-spacing:.08em;color:var(--text);margin-bottom:.35rem">
        Welcome back, {{ auth()->user()->name }}
    </h1>
    <p style="font-size:.85rem;color:var(--muted);margin-bottom:2rem">
        Here are today's top AI-generated tips for you.
    </p>

    @if($featuredTips->isNotEmpty())
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1rem">
        @foreach($featuredTips as $tip)
        <a href="{{ route('tips.show', $tip) }}"
           style="display:block;text-decoration:none;background:var(--card);border:1px solid var(--border);border-radius:10px;padding:1.1rem;transition:border-color .15s"
           onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--border)'">

            <div style="font-size:.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.5rem">
                {{ $tip->fixture->league->name ?? '—' }} &middot; {{ $tip->fixture->match_date->format('d M, H:i') }}
            </div>

            <div style="font-weight:600;color:var(--text);margin-bottom:.4rem">
                {{ $tip->fixture->home_team }} vs {{ $tip->fixture->away_team }}
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:.6rem">
                <span style="background:var(--surface);border:1px solid var(--border);border-radius:4px;padding:.2rem .55rem;font-size:.78rem;color:var(--accent)">
                    {{ $tip->selection }}
                </span>
                <span style="font-family:var(--fm);font-size:.85rem;font-weight:700;color:{{ $tip->confidence >= 75 ? 'var(--accent)' : 'var(--accent2)' }}">
                    {{ $tip->confidence }}%
                </span>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div style="background:var(--card);border:1px solid var(--border);border-radius:10px;padding:2rem;text-align:center;color:var(--muted)">
        No published tips available right now. Check back soon.
    </div>
    @endif

    <div style="margin-top:1.5rem;display:flex;gap:.75rem;flex-wrap:wrap">
        <a href="{{ route('tips.index') }}"
           style="display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1.1rem;background:var(--accent);color:#07090e;font-weight:700;font-size:.82rem;border-radius:6px;text-decoration:none">
            View All Tips →
        </a>
        <a href="{{ route('bookmakers.index') }}"
           style="display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1.1rem;background:var(--card);border:1px solid var(--border);color:var(--text);font-size:.82rem;border-radius:6px;text-decoration:none">
            Compare Bookmakers
        </a>
    </div>
</div>

</x-app-layout>
