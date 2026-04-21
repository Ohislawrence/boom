<x-app-layout>
<x-slot name="title">Privacy Notice — {{ config('app.name') }}</x-slot>
<x-slot name="description">Read the {{ config('app.name') }} privacy notice — how we collect, use, and protect your personal data.</x-slot>

<div style="max-width:860px;margin:0 auto;padding:2rem 2rem">

    <div style="font-size:.75rem;color:var(--muted);margin-bottom:1.5rem">
        <a href="{{ route('home') }}" style="color:var(--muted);text-decoration:none">Home</a>
        <span style="margin:0 .4rem">›</span>
        <span style="color:var(--text)">Privacy Notice</span>
    </div>

    <h1 style="font-family:var(--fh);font-size:2.2rem;letter-spacing:.08em;color:var(--text);margin-bottom:.5rem">Privacy Notice</h1>
    <p style="font-size:.82rem;color:var(--muted);margin-bottom:2rem">Last updated: {{ \Carbon\Carbon::now()->format('d F Y') }}</p>

    <div style="display:grid;gap:1.25rem">

        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <h2 style="font-family:var(--fh);font-size:1.1rem;letter-spacing:.06em;color:var(--accent);margin-bottom:.75rem">1. Who We Are</h2>
            <p style="font-size:.87rem;color:var(--muted);line-height:1.9;margin:0">
                {{ config('app.name') }} operates this website and is responsible for your personal data. References to "we", "us" or "our" in this notice refer to {{ config('app.name') }}. If you have any questions about how we use your data, please contact us.
            </p>
        </div>

        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <h2 style="font-family:var(--fh);font-size:1.1rem;letter-spacing:.06em;color:var(--accent);margin-bottom:.75rem">2. Data We Collect</h2>
            <p style="font-size:.87rem;color:var(--muted);line-height:1.9;margin:0 0 .75rem">We collect the following categories of personal data:</p>
            <ul style="font-size:.87rem;color:var(--muted);line-height:2;margin:0;padding-left:1.25rem">
                <li><strong style="color:var(--text)">Account data</strong> — email address, username, and password (hashed) when you register.</li>
                <li><strong style="color:var(--text)">Usage data</strong> — pages visited, tips viewed, and features used, collected via server logs and analytics.</li>
                <li><strong style="color:var(--text)">Device data</strong> — browser type, operating system, and IP address.</li>
                <li><strong style="color:var(--text)">Communication data</strong> — any messages you send us directly.</li>
            </ul>
        </div>

        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <h2 style="font-family:var(--fh);font-size:1.1rem;letter-spacing:.06em;color:var(--accent);margin-bottom:.75rem">3. How We Use Your Data</h2>
            <ul style="font-size:.87rem;color:var(--muted);line-height:2;margin:0;padding-left:1.25rem">
                <li>To provide and improve the {{ config('app.name') }} service.</li>
                <li>To send you notifications you have opted into (e.g. daily tips email).</li>
                <li>To detect and prevent fraud or abuse.</li>
                <li>To comply with legal obligations.</li>
                <li>To analyse aggregate usage patterns and improve our AI models (non-personally-identifiable only).</li>
            </ul>
        </div>

        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <h2 style="font-family:var(--fh);font-size:1.1rem;letter-spacing:.06em;color:var(--accent);margin-bottom:.75rem">4. Cookies</h2>
            <p style="font-size:.87rem;color:var(--muted);line-height:1.9;margin:0">
                We use essential session cookies required for the platform to function, and optional analytics cookies to understand how our site is used. You can disable non-essential cookies in your browser settings at any time. Bookmaker links may set third-party cookies on the bookmaker's own domain; we are not responsible for third-party cookie policies.
            </p>
        </div>

        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <h2 style="font-family:var(--fh);font-size:1.1rem;letter-spacing:.06em;color:var(--accent);margin-bottom:.75rem">5. Data Sharing</h2>
            <p style="font-size:.87rem;color:var(--muted);line-height:1.9;margin:0">
                We do not sell your personal data. We may share data with trusted service providers (e.g. hosting, email delivery) under strict data processing agreements. We may also share data where required by Nigerian law or court order.
            </p>
        </div>

        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <h2 style="font-family:var(--fh);font-size:1.1rem;letter-spacing:.06em;color:var(--accent);margin-bottom:.75rem">6. Data Retention</h2>
            <p style="font-size:.87rem;color:var(--muted);line-height:1.9;margin:0">
                We retain your account data for as long as your account is active. If you delete your account, your personal data is removed within 30 days, except where retention is required for legal or fraud-prevention purposes.
            </p>
        </div>

        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <h2 style="font-family:var(--fh);font-size:1.1rem;letter-spacing:.06em;color:var(--accent);margin-bottom:.75rem">7. Your Rights</h2>
            <p style="font-size:.87rem;color:var(--muted);line-height:1.9;margin:0 0 .5rem">You have the right to:</p>
            <ul style="font-size:.87rem;color:var(--muted);line-height:2;margin:0;padding-left:1.25rem">
                <li>Access the personal data we hold about you.</li>
                <li>Request correction of inaccurate data.</li>
                <li>Request deletion of your account and data.</li>
                <li>Withdraw consent where processing is consent-based.</li>
                <li>Object to processing for direct marketing.</li>
            </ul>
        </div>

        <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1.5rem">
            <h2 style="font-family:var(--fh);font-size:1.1rem;letter-spacing:.06em;color:var(--accent);margin-bottom:.75rem">8. Changes to This Notice</h2>
            <p style="font-size:.87rem;color:var(--muted);line-height:1.9;margin:0">
                We may update this Privacy Notice from time to time. The updated date at the top of this page will reflect the most recent revision. Continued use of the platform after a revision constitutes acceptance of the updated notice.
            </p>
        </div>

    </div>

</div>

<x-slot name="footer">@include('layouts.partials.footer')</x-slot>
</x-app-layout>
