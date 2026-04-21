<x-app-layout>
<x-slot name="title">How It Works — {{ config('app.name') }}</x-slot>
<x-slot name="description">Learn how SCOUT's AI generates football betting tips — from data collection and model training to confidence scoring and daily publication.</x-slot>

<div style="max-width:860px;margin:0 auto;padding:2rem 2rem">

    <div style="font-size:.75rem;color:var(--muted);margin-bottom:1.5rem">
        <a href="{{ route('home') }}" style="color:var(--muted);text-decoration:none">Home</a>
        <span style="margin:0 .4rem">›</span>
        <span style="color:var(--text)">How It Works</span>
    </div>

    <h1 style="font-family:var(--fh);font-size:2.2rem;letter-spacing:.08em;color:var(--text);margin-bottom:.5rem">How It Works</h1>
    <p style="font-size:.85rem;color:var(--muted);margin-bottom:2rem">From raw data to published tip — the {{ config('app.name') }} pipeline explained.</p>

    {{-- Step-by-step --}}
    <div style="display:grid;gap:1px;background:var(--border);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.5rem">
        @php
        $steps = [
            ['n'=>'01','title'=>'Data Collection','icon'=>'📡',
             'body'=>"Every day we pull live fixture data, team form, head-to-head records, injuries, and current odds from multiple sports data providers. We track 200+ football leagues worldwide, with special emphasis on Nigerian, English, Spanish, Italian, German, and French football."],
            ['n'=>'02','title'=>'Statistical Modelling','icon'=>'📊',
             'body'=>"Our models ingest rolling form (last 5, 10, 20 matches), home/away split performance, goal expectation (xG), rest days, travel distance, and market odds as an implied-probability signal. Each feature is weighted by its historical predictive power."],
            ['n'=>'03','title'=>'AI Analysis','icon'=>'🤖',
             'body'=>"A large language model then reasons over the statistical output — evaluating narrative factors like managerial changes, squad depth, and historical rivalry patterns that numbers alone can miss. This produces a human-readable reasoning paragraph alongside each tip."],
            ['n'=>'04','title'=>'Confidence Scoring','icon'=>'🎯',
             'body'=>"Every tip receives a confidence score from 0–100. Scores ≥ 75 are flagged as High Confidence. Scores where our implied probability materially exceeds the market's implied probability are additionally flagged as Value Bets (⭐). We only publish tips that meet minimum quality thresholds."],
            ['n'=>'05','title'=>'Human Review','icon'=>'👁',
             'body'=>"Our editorial team reviews AI-generated tips before publication. Any tip that conflicts with known late news (e.g. a key player injury announced the morning of the match) is withheld or adjusted. This human-in-the-loop step is critical to maintaining tip quality."],
            ['n'=>'06','title'=>'Publication & Tracking','icon'=>'📬',
             'body'=>"Tips go live each morning and are visible on the fixture's betting-tips page. After the match we record results and update our running ROI stats. Transparency in our track record is non-negotiable."],
        ];
        @endphp
        @foreach($steps as $step)
        <div style="display:flex;gap:1.1rem;padding:1.25rem 1.4rem;background:var(--card);transition:background .15s"
             onmouseover="this.style.background='var(--card2)'" onmouseout="this.style.background='var(--card)'">
            <div style="flex-shrink:0;width:42px;height:42px;border-radius:50%;background:rgba(0,229,160,.1);border:1px solid rgba(0,229,160,.25);display:flex;align-items:center;justify-content:center;font-size:1.2rem">{{ $step['icon'] }}</div>
            <div style="flex:1;min-width:0">
                <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.4rem">
                    <span style="font-family:var(--fm);font-size:.65rem;color:var(--accent);letter-spacing:.1em">{{ $step['n'] }}</span>
                    <span style="font-family:var(--fh);font-size:1rem;letter-spacing:.05em;color:var(--text)">{{ $step['title'] }}</span>
                </div>
                <p style="font-size:.85rem;color:var(--muted);line-height:1.85;margin:0">{{ $step['body'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Confidence key --}}
    <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.4rem;margin-bottom:1.5rem">
        <h2 style="font-family:var(--fh);font-size:1.1rem;letter-spacing:.06em;color:var(--text);margin-bottom:1rem">Confidence Score Key</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.75rem">
            @foreach([
                ['range'=>'90 – 100%','label'=>'Elite','color'=>'var(--accent)','desc'=>'Model has very high conviction. Rare.'],
                ['range'=>'75 – 89%', 'label'=>'High','color'=>'#00c87a','desc'=>'Strong statistical edge. Core picks.'],
                ['range'=>'60 – 74%', 'label'=>'Medium','color'=>'var(--accent2)','desc'=>'Decent signal, slightly more variance.'],
                ['range'=>'< 60%',    'label'=>'Low','color'=>'var(--muted)','desc'=>'Speculative — use with caution.'],
            ] as $tier)
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:6px;padding:.85rem">
                <div style="font-family:var(--fm);font-size:.82rem;font-weight:700;color:{{ $tier['color'] }};margin-bottom:.2rem">{{ $tier['range'] }}</div>
                <div style="font-size:.85rem;font-weight:700;color:var(--text);margin-bottom:.2rem">{{ $tier['label'] }}</div>
                <div style="font-size:.75rem;color:var(--muted);line-height:1.6">{{ $tier['desc'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <div style="background:rgba(0,229,160,.05);border:1px solid rgba(0,229,160,.2);border-radius:8px;padding:1.1rem 1.4rem">
        <p style="font-size:.82rem;color:var(--muted);line-height:1.8;margin:0">
            ⚠️ No prediction system is infallible. Past confidence scores do not guarantee future results. Always gamble within your means. See our <a href="{{ route('page.responsible-gambling') }}" style="color:var(--accent);text-decoration:none">Responsible Gambling</a> guide for support resources.
        </p>
    </div>

</div>

<x-slot name="footer">@include('layouts.partials.footer')</x-slot>
</x-app-layout>
