<x-app-layout>

<x-slot name="title">{{ $bookmaker->name }} Review {{ date('Y') }} — Odds, Offers &amp; Rating</x-slot>
<x-slot name="description">{{ Str::limit(($bookmaker->review ?? 'Read our expert ' . $bookmaker->name . ' review for ' . date('Y') . '. We cover odds quality, betting markets, welcome offer, app and overall trustworthiness.'), 160) }}</x-slot>
<x-slot name="canonical">{{ route('bookmakers.show', $bookmaker->slug) }}</x-slot>

@push('head')
@php
$ldBm = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Review',
            'itemReviewed' => [
                '@type' => 'Organization',
                'name'  => $bookmaker->name,
                'url'   => $bookmaker->affiliate_url ?? url('/'),
            ],
            'reviewRating' => [
                '@type'       => 'Rating',
                'ratingValue' => (string) ($bookmaker->rating ?? '8'),
                'bestRating'  => '10',
                'worstRating' => '1',
            ],
            'author'     => ['@type' => 'Organization', 'name' => 'SCOUT', 'url' => url('/')],
            'reviewBody' => Str::limit($bookmaker->review ?? 'Expert review of ' . $bookmaker->name . '.', 500),
            'datePublished' => ($bookmaker->created_at ?? now())->toIso8601String(),
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home',        'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Bookmakers', 'item' => route('bookmakers.index')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $bookmaker->name],
            ],
        ],
    ],
];
@endphp
<script type="application/ld+json">{!! json_encode($ldBm, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

<style>
.bk-stat{display:flex;flex-direction:column;align-items:center;gap:.2rem;text-align:center}
.bk-stat-val{font-family:var(--fm);font-size:1rem;font-weight:700;color:var(--text)}
.bk-stat-lbl{font-size:.62rem;text-transform:uppercase;letter-spacing:.07em;color:var(--dim)}
.bk-pill{font-size:.7rem;background:rgba(0,229,160,.07);color:var(--accent);border:1px solid rgba(0,229,160,.18);padding:.2rem .5rem;border-radius:4px;white-space:nowrap}
.bk-check{display:flex;align-items:center;gap:.45rem;font-size:.82rem;color:var(--muted);padding:.3rem 0}
.bk-check .ico{width:18px;height:18px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;flex-shrink:0}
.bk-check .ico.yes{background:rgba(0,229,160,.15);color:var(--accent)}
.bk-check .ico.no{background:rgba(255,255,255,.05);color:var(--dim)}
</style>

<div class="scout-page-wrap" style="max-width:1280px;margin:0 auto;padding:1.5rem 2rem">

    {{-- Breadcrumb --}}
    <div style="font-size:.75rem;color:var(--muted);margin-bottom:1.25rem">
        <a href="{{ route('home') }}" style="color:var(--muted);text-decoration:none">Home</a>
        <span style="margin:0 .4rem">›</span>
        <a href="{{ route('bookmakers.index') }}" style="color:var(--muted);text-decoration:none">Bookmakers</a>
        <span style="margin:0 .4rem">›</span>
        <span style="color:var(--text)">{{ $bookmaker->name }}</span>
    </div>

    <div class="welcome-grid">

        {{-- ── MAIN ── --}}
        <div>

            {{-- Header card --}}
            <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.4rem;margin-bottom:1.25rem">

                {{-- Logo + name row --}}
                <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:1rem">
                    @if($bookmaker->logo_url)
                    <img src="{{ $bookmaker->logo_url }}" alt="{{ $bookmaker->name }} logo"
                         style="width:52px;height:52px;border-radius:8px;object-fit:contain;background:var(--card2);padding:4px;border:1px solid var(--border);flex-shrink:0" loading="eager">
                    @else
                    <div style="width:52px;height:52px;border-radius:8px;background:var(--accent);color:#07090e;font-family:var(--fh);font-size:1.4rem;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        {{ strtoupper(substr($bookmaker->name, 0, 2)) }}
                    </div>
                    @endif

                    <div style="flex:1;min-width:140px">
                        <h1 style="font-family:var(--fh);font-size:2rem;letter-spacing:.08em;color:var(--text);margin:0 0 .15rem">
                            {{ $bookmaker->name }}
                        </h1>
                        <div style="font-size:.72rem;color:var(--dim)">
                            @if($bookmaker->founded_year) Est. {{ $bookmaker->founded_year }} @endif
                            @if($bookmaker->license) &bull; {{ $bookmaker->license }} Licensed @endif
                        </div>
                    </div>

                    @if($bookmaker->is_featured)
                    <span style="font-size:.62rem;background:var(--accent);color:#07090e;padding:.2rem .55rem;border-radius:3px;letter-spacing:.06em;font-weight:700;align-self:flex-start">TOP PICK</span>
                    @endif
                </div>

                {{-- Rating bar + CTA row --}}
                <div style="display:flex;align-items:flex-start;gap:1rem;flex-wrap:wrap">

                    <div style="flex:1;min-width:200px">

                        {{-- Star rating out of 10 --}}
                        <div style="display:flex;align-items:center;gap:.3rem;margin-bottom:.75rem">
                            @for($s=1;$s<=5;$s++)
                            <span style="color:{{ $s <= round($bookmaker->rating / 2) ? 'var(--accent2)' : 'var(--border)' }};font-size:1.2rem">★</span>
                            @endfor
                            <span style="font-family:var(--fm);font-size:.9rem;color:var(--text);margin-left:.3rem">{{ number_format($bookmaker->rating, 1) }} / 10</span>
                        </div>

                        {{-- Key feature pills --}}
                        @if($bookmaker->key_features && count($bookmaker->key_features))
                        <div style="display:flex;flex-wrap:wrap;gap:.35rem;margin-bottom:.75rem">
                            @foreach($bookmaker->key_features as $feat)
                            <span class="bk-pill">{{ $feat }}</span>
                            @endforeach
                        </div>
                        @elseif($bookmaker->betMarkets->isNotEmpty())
                        <div style="display:flex;flex-wrap:wrap;gap:.35rem;margin-bottom:.75rem">
                            @foreach($bookmaker->betMarkets->take(5) as $market)
                            <span class="bk-pill">{{ $market->name }}</span>
                            @endforeach
                        </div>
                        @endif

                        {{-- Quick stats row --}}
                        <div style="display:flex;gap:1.5rem;flex-wrap:wrap;margin-top:.25rem">
                            @if($bookmaker->min_deposit)
                            <div class="bk-stat">
                                <div class="bk-stat-val">{{ $bookmaker->min_deposit }}</div>
                                <div class="bk-stat-lbl">Min Deposit</div>
                            </div>
                            @endif
                            @if($bookmaker->withdrawal_time)
                            <div class="bk-stat">
                                <div class="bk-stat-val">{{ $bookmaker->withdrawal_time }}</div>
                                <div class="bk-stat-lbl">Withdrawal</div>
                            </div>
                            @endif
                            <div class="bk-stat">
                                <div class="bk-stat-val">{{ $bookmaker->betMarkets->count() }}</div>
                                <div class="bk-stat-lbl">Markets</div>
                            </div>
                        </div>

                    </div>

                    {{-- CTA box --}}
                    <div style="background:var(--surface);border:1px solid var(--accent);border-radius:8px;padding:1rem;min-width:220px;text-align:center">
                        @if($bookmaker->welcome_offer)
                        <div style="font-size:.68rem;color:var(--accent);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem">Welcome Offer</div>
                        <div style="font-family:var(--fh);font-size:1rem;letter-spacing:.04em;color:var(--text);margin-bottom:.5rem">{{ $bookmaker->welcome_offer }}</div>
                        @endif
                        @if($bookmaker->bonus_text)
                        <div style="font-size:.72rem;color:var(--muted);margin-bottom:.75rem">{{ $bookmaker->bonus_text }}</div>
                        @endif
                        <a href="{{ $bookmaker->affiliate_url }}" target="_blank" rel="nofollow noopener"
                           style="display:block;background:var(--accent);color:#07090e;font-family:var(--fh);font-size:.95rem;letter-spacing:.08em;padding:.65rem;border-radius:6px;text-decoration:none">
                            JOIN {{ strtoupper($bookmaker->name) }}
                        </a>
                        <div style="font-size:.6rem;color:var(--muted);margin-top:.4rem">18+ · T&Cs apply · Gamble responsibly</div>
                    </div>

                </div>
            </div>

            {{-- Feature checklist --}}
            <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.1rem 1.4rem;margin-bottom:1.25rem">
                <h2 style="font-family:var(--fh);font-size:1.05rem;letter-spacing:.06em;color:var(--text);margin-bottom:.75rem">At a Glance</h2>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0 1.5rem">
                    @php
                    $checks = [
                        ['label' => 'Fast Withdrawals',  'val' => $bookmaker->fast_withdrawal ?? false],
                        ['label' => 'Live Betting',       'val' => $bookmaker->live_betting ?? true],
                        ['label' => 'Mobile App',         'val' => $bookmaker->mobile_app ?? true],
                        ['label' => 'Welcome Bonus',      'val' => !empty($bookmaker->welcome_offer)],
                        ['label' => 'Nigerian Naira (₦)', 'val' => true],
                        ['label' => 'NLRC Licensed',      'val' => ($bookmaker->license ?? '') === 'NLRC'],
                    ];
                    @endphp
                    @foreach($checks as $c)
                    <div class="bk-check">
                        <span class="ico {{ $c['val'] ? 'yes' : 'no' }}">{{ $c['val'] ? '✓' : '✗' }}</span>
                        <span>{{ $c['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Full review --}}
            @if($bookmaker->review)
            <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.4rem;margin-bottom:1.25rem">
                <h2 style="font-family:var(--fh);font-size:1.2rem;letter-spacing:.06em;color:var(--text);margin-bottom:.85rem">Expert Review</h2>
                <div style="font-size:.88rem;color:var(--muted);line-height:1.9">
                    {!! nl2br(e($bookmaker->review)) !!}
                </div>
            </div>
            @endif

            {{-- Available bet markets --}}
            @if($bookmaker->betMarkets->isNotEmpty())
            <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:1.25rem">
                <div style="padding:.65rem 1.1rem;background:var(--card2);border-bottom:1px solid var(--border)">
                    <span style="font-family:var(--fh);font-size:.95rem;letter-spacing:.06em;color:var(--text)">Markets Covered</span>
                    <span style="font-size:.72rem;color:var(--muted);margin-left:.5rem">({{ $bookmaker->betMarkets->count() }} markets)</span>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1px;background:var(--border)">
                    @foreach($bookmaker->betMarkets as $market)
                    <div style="background:var(--card);padding:.75rem 1rem">
                        <div style="font-size:.85rem;font-weight:600;color:var(--text);margin-bottom:.15rem">{{ $market->name }}</div>
                        @if($market->description)
                        <div style="font-size:.7rem;color:var(--muted)">{{ Str::limit($market->description, 60) }}</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- ── SIDEBAR ── --}}
        <div class="welcome-sidebar">

            {{-- Other bookmakers --}}
            @if($others->isNotEmpty())
            <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden">
                <div style="padding:.65rem 1rem;background:var(--card2);border-bottom:1px solid var(--border)">
                    <span style="font-family:var(--fh);font-size:.9rem;letter-spacing:.06em;color:var(--text)">Compare Other Sites</span>
                </div>
                @foreach($others as $other)
                <a href="{{ route('bookmakers.show', $other->slug) }}"
                   style="display:flex;align-items:center;gap:.65rem;padding:.65rem 1rem;border-bottom:1px solid var(--border);text-decoration:none;transition:background .15s"
                   onmouseover="this.style.background='var(--card2)'" onmouseout="this.style.background='transparent'">
                    {{-- Logo thumb --}}
                    @if($other->logo_url)
                    <img src="{{ $other->logo_url }}" alt="{{ $other->name }}" style="width:28px;height:28px;border-radius:4px;object-fit:contain;background:var(--surface);padding:2px;border:1px solid var(--border);flex-shrink:0" loading="lazy">
                    @else
                    <div style="width:28px;height:28px;border-radius:4px;background:var(--accent);color:#07090e;font-family:var(--fh);font-size:.75rem;display:flex;align-items:center;justify-content:center;flex-shrink:0">{{ strtoupper(substr($other->name,0,2)) }}</div>
                    @endif
                    <div style="flex:1;min-width:0">
                        <div style="font-size:.85rem;font-weight:600;color:var(--text)">{{ $other->name }}</div>
                        <div style="display:flex;gap:.15rem;margin-top:.12rem">
                            @for($s=1;$s<=5;$s++)
                            <span style="color:{{ $s <= round($other->rating / 2) ? 'var(--accent2)' : 'var(--border)' }};font-size:.7rem">★</span>
                            @endfor
                            <span style="font-size:.65rem;color:var(--dim);margin-left:.2rem">{{ number_format($other->rating,1) }}</span>
                        </div>
                    </div>
                    <span style="font-size:.7rem;color:var(--muted);flex-shrink:0">→</span>
                </a>
                @endforeach

                <div style="padding:.75rem 1rem">
                    <a href="{{ route('bookmakers.index') }}"
                       style="display:block;text-align:center;background:transparent;border:1px solid var(--border);color:var(--muted);font-size:.78rem;padding:.5rem;border-radius:5px;text-decoration:none">
                        View All Bookmakers
                    </a>
                </div>
            </div>
            @endif

        </div>

    </div>
</div>

<x-slot name="footer">@include('layouts.partials.footer')</x-slot>
</x-app-layout>

