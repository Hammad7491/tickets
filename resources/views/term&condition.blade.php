@php
  $app    = config('app.name', '92 Dream PK');
  $email  = config('mail.from.address', 'support@example.com');
  $wa     = config('app.support_whatsapp', env('SUPPORT_WHATSAPP', '+923124932021'));
  $waLink = 'https://wa.me/' . preg_replace('/\D+/', '', $wa);
@endphp

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Terms & Conditions — {{ $app }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  {{-- Use the same CSS stack you already ship --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --brand:#4f46e5; /* indigo */
      --accent:#06b6d4;/* cyan */
      --ink:#0f172a;
      --muted:#64748b;
      --ring:rgba(79,70,229,.1);
    }
    body{ color:var(--ink); background:#f7f9fc; }

    /* Hero */
    .tc-hero{
      background:
        radial-gradient(1200px 400px at 0% -10%, rgba(79,70,229,.35) 0%, transparent 55%),
        radial-gradient(1200px 400px at 120% 10%, rgba(6,182,212,.28) 0%, transparent 60%),
        linear-gradient(135deg,#0b132b 0%, #0c1930 50%, #0c1f38 100%);
      color:#fff;
    }
    .tc-hero h1{
      font-weight:900; letter-spacing:.3px;
      font-size:clamp(1.6rem, 1.1rem + 2.6vw, 2.6rem);
    }
    .tc-hero .kicker{
      font-weight:700; color:#c7d2fe; letter-spacing:.12em;
      text-transform:uppercase; font-size:.8rem;
    }

    /* Page shell */
    .tc-card{
      background:#fff; border:1px solid #eef1f6; border-radius:18px;
      box-shadow:0 16px 48px rgba(2,8,23,.06);
    }
    .toc{
      position:sticky; top:1.25rem;
    }
    .toc .list-group-item{
      border:0; padding:.5rem .75rem; color:#475569; background:transparent;
    }
    .toc .list-group-item.active{
      background:rgba(79,70,229,.08); color:#4f46e5; font-weight:700;
    }

    .section-title{
      scroll-margin-top:84px; /* nice anchor offset */
      font-weight:800; letter-spacing:.2px; margin-bottom:.5rem;
    }
    .badge-note{
      background:#eef2ff; color:#4338ca; border:1px solid #e2e8f0;
      border-radius:999px; font-weight:700;
    }
    .callout{
      border-left:4px solid var(--brand); background:#f8fafc; border-radius:12px;
      padding:1rem 1rem 1rem 1.25rem;
    }
    .not-refundable{
      background:linear-gradient(90deg,#fef2f2,#fff);
      border:1px solid #fecaca; border-left:6px solid #ef4444;
      border-radius:14px; padding:1rem 1.1rem;
      box-shadow:0 8px 30px rgba(239,68,68,.08);
    }
    .not-refundable strong{ color:#b91c1c; }
    .anchor-link{
      text-decoration:none; color:inherit;
    }
    .anchor-link:hover{ color:#4f46e5; }
  </style>
</head>
<body>

  {{-- HERO --}}
  <header class="tc-hero py-5">
    <div class="container">
      <p class="kicker mb-2">Legal</p>
      <h1 class="mb-2">Terms &amp; Conditions</h1>
      <p class="mb-0 text-white-50">
        Last updated: {{ now()->format('d M Y') }}
      </p>
    </div>
  </header>

  {{-- CONTENT --}}
  <main class="py-4 py-lg-5">
    <div class="container">
      <div class="row g-4 g-lg-5">

        {{-- TOC (sticky) --}}
        <aside class="col-lg-4">
          <div class="tc-card p-3 p-lg-4">
            <div class="toc">
              <h6 class="text-uppercase text-muted fw-bold mb-3">On this page</h6>
              <div class="list-group small">
                <a href="#intro"  class="list-group-item anchor-link">1. Introduction</a>
                <a href="#elig"   class="list-group-item anchor-link">2. Eligibility & Accounts</a>
                <a href="#tickets" class="list-group-item anchor-link">3. Tickets & Purchases</a>
                <a href="#pay"    class="list-group-item anchor-link">4. Pricing & Payments</a>
                <a href="#draws"  class="list-group-item anchor-link">5. Live Draws</a>
                <a href="#winners" class="list-group-item anchor-link">6. Winners & Payouts</a>
                <a href="#conduct" class="list-group-item anchor-link">7. Prohibited Use</a>
                <a href="#ip"     class="list-group-item anchor-link">8. Intellectual Property</a>
                <a href="#privacy" class="list-group-item anchor-link">9. Privacy</a>
                <a href="#disclaimers" class="list-group-item anchor-link">10. Disclaimers</a>
                <a href="#liability" class="list-group-item anchor-link">11. Limitation of Liability</a>
                <a href="#indemnity" class="list-group-item anchor-link">12. Indemnification</a>
                <a href="#termination" class="list-group-item anchor-link">13. Suspension/Termination</a>
                <a href="#changes" class="list-group-item anchor-link">14. Changes to These Terms</a>
                <a href="#contact" class="list-group-item anchor-link">15. Contact</a>
              </div>
            </div>
          </div>
        </aside>

        {{-- BODY --}}
        <section class="col-lg-8">
          <article class="tc-card p-4 p-lg-5">

            {{-- 1 --}}
            <h3 id="intro" class="section-title">1) Introduction</h3>
            <p>
              Welcome to <strong>{{ $app }}</strong> (“we”, “us”, or “our”). These Terms &amp; Conditions
              (“Terms”) govern your access to and use of our website, your participation in ticket purchases,
              and any live-streamed lottery draws hosted by us. By creating an account or purchasing tickets,
              you agree to these Terms.
            </p>

            {{-- 2 --}}
            <h3 id="elig" class="section-title">2) Eligibility &amp; Accounts</h3>
            <ul>
              <li>You must be legally capable of entering into contracts under the laws of your jurisdiction.</li>
              <li>Provide accurate, up-to-date information and keep your login credentials secure.</li>
              <li>We may suspend or close accounts for suspected fraud, misuse, violations of these Terms, or as required by law.</li>
            </ul>

            {{-- 3 --}}
            <h3 id="tickets" class="section-title">3) Tickets &amp; Purchases</h3>
            <ul>
              <li>Users may purchase any number of tickets while sales remain open.</li>
              <li>Each ticket represents one entry in the specified draw. Ticket availability is limited and not guaranteed until payment is confirmed.</li>
              <li>Once all tickets for a draw are sold, we announce the live stream time and conduct the draw on YouTube.</li>
            </ul>

            {{-- 4 --}}
            <h3 id="pay" class="section-title">4) Pricing &amp; Payments</h3>
            <div class="not-refundable mb-3">
              <strong>Important — Payments are Non-Refundable:</strong>
              All ticket purchases are final. <u>We do not offer refunds or cancellations</u> once a payment has been processed,
              regardless of schedule changes, stream delays, or personal circumstances.
            </div>
            <ul>
              <li>All prices are displayed in the currency shown at checkout and may change at any time before purchase.</li>
              <li>You authorize us to charge your selected payment method for the total amount due.</li>
              <li>Chargebacks or payment disputes may result in suspension or termination of your account.</li>
            </ul>

            {{-- 5 --}}
            <h3 id="draws" class="section-title">5) Live Draws</h3>
            <p>
              Every draw is streamed live on YouTube for full transparency. If unforeseen technical issues occur (e.g., power/internet outages),
              we will complete the draw at the earliest reasonable time. Draw outcomes are final and recorded.
            </p>
            <div class="callout mt-2">
              <strong>Transparency note:</strong> The live stream acts as public verification of the draw process and results.
            </div>

            {{-- 6 --}}
            <h3 id="winners" class="section-title">6) Winners &amp; Payouts</h3>
            <ul>
              <li>Winners are selected during the live stream and listed on our website after verification.</li>
              <li>To receive prizes, winners must provide any information needed to verify identity and process payments.</li>
              <li>We aim to pay prizes promptly after verification. Processing times may vary by method and jurisdiction.</li>
            </ul>

            {{-- 7 --}}
            <h3 id="conduct" class="section-title">7) Prohibited Use</h3>
            <ul>
              <li>Do not engage in fraud, manipulate results, or interfere with the draw or platform.</li>
              <li>Do not attempt to access other users’ accounts or compromise our systems.</li>
              <li>Do not post or transmit unlawful, abusive, or infringing content.</li>
            </ul>

            {{-- 8 --}}
            <h3 id="ip" class="section-title">8) Intellectual Property</h3>
            <p>
              All trademarks, logos, graphics, videos, and content on {{ $app }} are our property or licensed to us.
              You may not copy, distribute, or create derivative works without written permission.
            </p>

            {{-- 9 --}}
            <h3 id="privacy" class="section-title">9) Privacy</h3>
            <p>
              We respect your privacy. See our Privacy Policy for how we collect, use, and protect your information.
              By using our services, you consent to those practices.
            </p>

            {{-- 10 --}}
            <h3 id="disclaimers" class="section-title">10) Disclaimers</h3>
            <p>
              Services are provided “as is” and “as available.” We do not guarantee uninterrupted access, error-free streams,
              or that every draw will occur at its originally announced time. We disclaim all warranties to the fullest extent permitted by law.
            </p>

            {{-- 11 --}}
            <h3 id="liability" class="section-title">11) Limitation of Liability</h3>
            <p>
              To the maximum extent permitted by law, {{ $app }} shall not be liable for indirect, incidental, special, or consequential damages,
              lost profits, or data loss, arising from or related to your use of the service, ticket purchases, or live draws.
            </p>

            {{-- 12 --}}
            <h3 id="indemnity" class="section-title">12) Indemnification</h3>
            <p>
              You agree to indemnify and hold {{ $app }} and its officers, directors, employees, and agents harmless from any claims,
              losses, liabilities, and expenses (including legal fees) arising from your use of the service or violation of these Terms.
            </p>

            {{-- 13 --}}
            <h3 id="termination" class="section-title">13) Suspension/Termination</h3>
            <p>
              We may suspend or terminate your account at any time for violations of these Terms, suspected fraud, legal requests,
              or to protect platform integrity. You remain responsible for obligations incurred before termination.
            </p>

            {{-- 14 --}}
            <h3 id="changes" class="section-title">14) Changes to These Terms</h3>
            <p>
              We may update these Terms from time to time. The “Last updated” date above will change accordingly.
              Continued use after an update constitutes acceptance of the revised Terms.
            </p>

            {{-- 15 --}}
            <h3 id="contact" class="section-title">15) Contact</h3>
            <p class="mb-2">Questions about these Terms?</p>
            <ul class="mb-0">
              <li>Email: <a href="mailto:{{ $email }}">{{ $email }}</a></li>
              <li>WhatsApp: <a href="{{ $waLink }}" target="_blank" rel="noopener">{{ $wa }}</a></li>
            </ul>

          </article>
        </section>

      </div>
    </div>
  </main>

  {{-- FOOTER --}}
  <footer class="py-4 text-center text-muted small">
    © {{ date('Y') }} {{ $app }}. All rights reserved.
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // highlight active TOC item on scroll (simple version)
    const links = [...document.querySelectorAll('.toc .list-group-item')];
    const ids   = links.map(a => document.querySelector(a.getAttribute('href')));
    const onScroll = () => {
      let idx = 0, fromTop = window.scrollY + 100;
      ids.forEach((sec, i) => { if (sec.offsetTop <= fromTop) idx = i; });
      links.forEach(l => l.classList.remove('active'));
      links[idx]?.classList.add('active');
    };
    document.addEventListener('scroll', onScroll, { passive:true });
    onScroll();
  </script>
</body>
</html>
