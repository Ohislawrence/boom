<x-app-layout>
<x-slot name="title">Responsible Gambling — {{ config('app.name') }}</x-slot>
<x-slot name="description">Gambling should be fun. Read our responsible gambling guide — tools, warning signs, and where to get help in Nigeria and internationally.</x-slot>

<div style="max-width:860px;margin:0 auto;padding:2rem 2rem">

    <div style="font-size:.75rem;color:var(--muted);margin-bottom:1.5rem">
        <a href="{{ route('home') }}" style="color:var(--muted);text-decoration:none">Home</a>
        <span style="margin:0 .4rem">›</span>
        <span style="color:var(--text)">Responsible Gambling</span>
    </div>

    <h1 style="font-family:var(--fh);font-size:2.2rem;letter-spacing:.08em;color:var(--text);margin-bottom:.5rem">Responsible Gambling</h1>
    <p style="font-size:.85rem;color:var(--muted);margin-bottom:2rem">Betting should be entertainment, not a financial strategy. We take this seriously.</p>

    {{-- Banner --}}
    <div style="background:rgba(245,197,24,.07);border:1px solid rgba(245,197,24,.3);border-radius:8px;padding:1.1rem 1.4rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem">
        <span style="font-size:1.8rem;flex-shrink:0">⚠️</span>
        <p style="font-size:.87rem;color:var(--muted);line-height:1.8;margin:0">
            <strong style="color:var(--text)">This platform is for users aged 18 and over only.</strong> If you or someone you know may have a gambling problem, help is available. See the support resources at the bottom of this page.
        </p>
    </div>

    <div style="display:grid;gap:1.25rem">

        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <h2 style="font-family:var(--fh);font-size:1.1rem;letter-spacing:.06em;color:var(--accent);margin-bottom:.75rem">The Golden Rules</h2>
            <ul style="font-size:.87rem;color:var(--muted);line-height:2.1;margin:0;padding-left:1.25rem">
                <li>Only bet with money you can afford to lose.</li>
                <li>Set a budget before you start and stick to it.</li>
                <li>Never chase losses — walk away and come back another day.</li>
                <li>Do not bet under the influence of alcohol or strong emotions.</li>
                <li>Treat betting as entertainment, not as income.</li>
                <li>Keep track of how much time and money you are spending.</li>
            </ul>
        </div>

        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <h2 style="font-family:var(--fh);font-size:1.1rem;letter-spacing:.06em;color:var(--accent);margin-bottom:.75rem">Warning Signs of Problem Gambling</h2>
            <p style="font-size:.87rem;color:var(--muted);line-height:1.9;margin:0 0 .75rem">You may have a problem if you:</p>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:.65rem">
                @foreach([
                    'Bet more than you intended to',
                    'Lie to friends or family about gambling',
                    'Chase losses with bigger bets',
                    'Feel anxious or irritable when not betting',
                    'Borrow money to gamble',
                    'Neglect work, school, or relationships',
                    'Think about gambling constantly',
                    'Bet to escape problems or stress',
                ] as $sign)
                <div style="display:flex;align-items:flex-start;gap:.5rem;font-size:.82rem;color:var(--muted);background:var(--surface);border:1px solid var(--border);border-radius:5px;padding:.6rem .75rem">
                    <span style="color:var(--accent3);flex-shrink:0">●</span>{{ $sign }}
                </div>
                @endforeach
            </div>
        </div>

        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <h2 style="font-family:var(--fh);font-size:1.1rem;letter-spacing:.06em;color:var(--accent);margin-bottom:.75rem">Tools Available to You</h2>
            <p style="font-size:.87rem;color:var(--muted);line-height:1.9;margin:0 0 .75rem">
                All licensed Nigerian bookmakers are required to provide responsible gambling tools. If you feel you need to take a break, contact your bookmaker directly and ask about:
            </p>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:.65rem">
                @foreach([
                    ['icon'=>'⏱️','title'=>'Deposit Limits','desc'=>'Set daily, weekly or monthly deposit caps.'],
                    ['icon'=>'🔒','title'=>'Self-Exclusion','desc'=>'Block yourself from the platform for a set period.'],
                    ['icon'=>'⏸️','title'=>'Cooling-Off Period','desc'=>'Pause your account for 24–72 hours.'],
                    ['icon'=>'📋','title'=>'Activity Statements','desc'=>'Review your full betting history and spend.'],
                ] as $tool)
                <div style="background:var(--surface);border:1px solid var(--border);border-radius:6px;padding:.85rem">
                    <div style="font-size:1.3rem;margin-bottom:.3rem">{{ $tool['icon'] }}</div>
                    <div style="font-size:.85rem;font-weight:700;color:var(--text);margin-bottom:.15rem">{{ $tool['title'] }}</div>
                    <div style="font-size:.75rem;color:var(--muted);line-height:1.6">{{ $tool['desc'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <h2 style="font-family:var(--fh);font-size:1.1rem;letter-spacing:.06em;color:var(--accent);margin-bottom:.75rem">Get Help — Support Resources</h2>
            <div style="display:grid;gap:.65rem">
                @foreach([
                    ['org'=>'Gamcare','url'=>'https://www.gamcare.org.uk','desc'=>'Free support, information and counselling for anyone affected by problem gambling.'],
                    ['org'=>'Gamblers Anonymous Nigeria','url'=>'https://www.gamblersanonymous.org','desc'=>'Peer support group with meetings across Nigeria. Fellowship of people sharing experiences.'],
                    ['org'=>'BeGambleAware','url'=>'https://www.begambleaware.org','desc'=>'Confidential help and advice. 24/7 helpline available.'],
                    ['org'=>'National Council on Problem Gambling','url'=>'https://www.ncpgambling.org','desc'=>'International resources and helpline referrals.'],
                ] as $resource)
                <a href="{{ $resource['url'] }}" target="_blank" rel="noopener"
                   style="display:flex;align-items:flex-start;gap:.75rem;background:var(--surface);border:1px solid var(--border);border-radius:6px;padding:.85rem;text-decoration:none;transition:border-color .15s"
                   onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--border)'">
                    <span style="font-size:1.2rem;flex-shrink:0;margin-top:.1rem">🆘</span>
                    <div>
                        <div style="font-size:.88rem;font-weight:700;color:var(--accent);margin-bottom:.15rem">{{ $resource['org'] }}</div>
                        <div style="font-size:.78rem;color:var(--muted);line-height:1.6">{{ $resource['desc'] }}</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <div style="background:rgba(0,229,160,.04);border:1px solid rgba(0,229,160,.15);border-radius:8px;padding:1.1rem 1.4rem">
            <p style="font-size:.82rem;color:var(--muted);line-height:1.8;margin:0;text-align:center">
                {{ config('app.name') }} supports responsible gambling. We promote only <strong style="color:var(--text)">licensed bookmakers</strong> and always display <strong style="color:var(--text)">18+ warnings</strong>. If you believe a listed bookmaker is not providing adequate responsible gambling tools, please <a href="{{ route('page.editorial-policy') }}" style="color:var(--accent);text-decoration:none">contact our editorial team</a>.
            </p>
        </div>

    </div>

</div>

<x-slot name="footer">@include('layouts.partials.footer')</x-slot>
</x-app-layout>
