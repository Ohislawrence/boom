<x-app-layout>
<x-slot name="title">About Us — {{ config('app.name') }}</x-slot>
<x-slot name="description">Learn about SCOUT — the AI-powered football betting tips platform built for serious bettors in Nigeria and beyond.</x-slot>

<div style="max-width:860px;margin:0 auto;padding:2rem 2rem">

    <div style="font-size:.75rem;color:var(--muted);margin-bottom:1.5rem">
        <a href="{{ route('home') }}" style="color:var(--muted);text-decoration:none">Home</a>
        <span style="margin:0 .4rem">›</span>
        <span style="color:var(--text)">About Us</span>
    </div>

    <h1 style="font-family:var(--fh);font-size:2.2rem;letter-spacing:.08em;color:var(--text);margin-bottom:.5rem">About {{ config('app.name') }}</h1>
    <p style="font-size:.85rem;color:var(--muted);margin-bottom:2rem">AI-powered football analysis for the modern bettor.</p>

    <div style="display:grid;gap:1.25rem">

        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <h2 style="font-family:var(--fh);font-size:1.15rem;letter-spacing:.06em;color:var(--accent);margin-bottom:.75rem">Who We Are</h2>
            <p style="font-size:.88rem;color:var(--muted);line-height:1.9;margin:0">
                {{ config('app.name') }} is a data-driven sports intelligence platform built to help football bettors make smarter, more informed decisions. We combine real-time fixture data, historical statistics, and advanced AI models to generate high-confidence betting tips — published fresh every day.
            </p>
        </div>

        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <h2 style="font-family:var(--fh);font-size:1.15rem;letter-spacing:.06em;color:var(--accent);margin-bottom:.75rem">Our Mission</h2>
            <p style="font-size:.88rem;color:var(--muted);line-height:1.9;margin:0">
                Betting is won or lost in the quality of your research. Our mission is to level the playing field — giving everyday bettors access to the same quality of statistical analysis that professional trading desks use. We don't chase tips from social media; we let the data speak.
            </p>
        </div>

        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <h2 style="font-family:var(--fh);font-size:1.15rem;letter-spacing:.06em;color:var(--accent);margin-bottom:.75rem">What We Cover</h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:.75rem;margin-top:.5rem">
                @foreach([
                    ['icon'=>'⚽','title'=>'Football','desc'=>'Premier League, NPFL, La Liga, UCL and 200+ more leagues'],
                    ['icon'=>'📊','title'=>'AI Analysis','desc'=>'Machine-learning confidence scores on every pick'],
                    ['icon'=>'💰','title'=>'Value Bets','desc'=>'Odds where our model detects positive expected value'],
                    ['icon'=>'📱','title'=>'Daily Tips','desc'=>'Fresh predictions published every morning'],
                ] as $item)
                <div style="background:var(--surface);border:1px solid var(--border);border-radius:6px;padding:.85rem">
                    <div style="font-size:1.4rem;margin-bottom:.35rem">{{ $item['icon'] }}</div>
                    <div style="font-size:.85rem;font-weight:700;color:var(--text);margin-bottom:.2rem">{{ $item['title'] }}</div>
                    <div style="font-size:.75rem;color:var(--muted);line-height:1.6">{{ $item['desc'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <h2 style="font-family:var(--fh);font-size:1.15rem;letter-spacing:.06em;color:var(--accent);margin-bottom:.75rem">Affiliate Disclosure</h2>
            <p style="font-size:.88rem;color:var(--muted);line-height:1.9;margin:0">
                {{ config('app.name') }} earns a commission when you sign up to a bookmaker through our links. This is at <strong style="color:var(--text)">no extra cost to you</strong> and never influences the tips we publish. Our editorial team operates independently of our commercial partnerships. See our <a href="{{ route('page.editorial-policy') }}" style="color:var(--accent);text-decoration:none">Editorial Policy</a> for full details.
            </p>
        </div>

    </div>

</div>

<x-slot name="footer">@include('layouts.partials.footer')</x-slot>
</x-app-layout>
