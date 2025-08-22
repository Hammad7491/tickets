@php
  $app    = config('app.name', '92 Dream PK');
  $email  = config('mail.from.address', 'support@example.com');
  $wa     = config('app.support_whatsapp', env('SUPPORT_WHATSAPP', '03066262459'));
  $waLink = 'https://wa.me/' . preg_replace('/\D+/', '', $wa);
@endphp

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Terms & Conditions — {{ $app }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
 <link rel="icon" type="image/png" href="{{ asset('asset/images/Logo_92.png') }}" sizes="32x32" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --brand:#4f46e5;
      --accent:#06b6d4;
      --ink:#0f172a;
      --muted:#64748b;
      --ring:rgba(79,70,229,.1);
    }
    body{ color:var(--ink); background:#f7f9fc; }
    .tc-hero{background:radial-gradient(1200px 400px at 0% -10%, rgba(79,70,229,.35) 0%, transparent 55%),radial-gradient(1200px 400px at 120% 10%, rgba(6,182,212,.28) 0%, transparent 60%),linear-gradient(135deg,#0b132b 0%, #0c1930 50%, #0c1f38 100%);color:#fff;}
    .tc-hero h1{font-weight:900;letter-spacing:.3px;font-size:clamp(1.6rem, 1.1rem + 2.6vw, 2.6rem);}
    .tc-hero .kicker{font-weight:700;color:#c7d2fe;letter-spacing:.12em;text-transform:uppercase;font-size:.8rem;}
    .tc-card{background:#fff;border:1px solid #eef1f6;border-radius:18px;box-shadow:0 16px 48px rgba(2,8,23,.06);}
    .toc{position:sticky;top:1.25rem;}
    .toc .list-group-item{border:0;padding:.5rem .75rem;color:#475569;background:transparent;}
    .toc .list-group-item.active{background:rgba(79,70,229,.08);color:#4f46e5;font-weight:700;}
    .section-title{scroll-margin-top:84px;font-weight:800;letter-spacing:.2px;margin-bottom:.5rem;}
    .badge-note{background:#eef2ff;color:#4338ca;border:1px solid #e2e8f0;border-radius:999px;font-weight:700;}
    .callout{border-left:4px solid var(--brand);background:#f8fafc;border-radius:12px;padding:1rem 1rem 1rem 1.25rem;}
    .not-refundable{background:linear-gradient(90deg,#fef2f2,#fff);border:1px solid #fecaca;border-left:6px solid #ef4444;border-radius:14px;padding:1rem 1.1rem;box-shadow:0 8px 30px rgba(239,68,68,.08);}
    .not-refundable strong{color:#b91c1c;}
    .anchor-link{text-decoration:none;color:inherit;}
    .anchor-link:hover{color:#4f46e5;}
  </style>
</head>
<body>

<header class="tc-hero py-5">
  <div class="container">
    <p class="kicker mb-2">Legal</p>
    <h1 class="mb-2">Terms &amp; Conditions</h1>
    <p class="mb-0 text-white-50">Last updated: {{ now()->format('d M Y') }}</p>
  </div>
</header>

<main class="py-4 py-lg-5">
  <div class="container">
    <div class="row g-4 g-lg-5">

      <aside class="col-lg-4">
        <div class="tc-card p-3 p-lg-4">
          <div class="toc">
            <h6 class="text-uppercase text-muted fw-bold mb-3">On this page</h6>
            <div class="list-group small">
              <a href="#aim" class="list-group-item anchor-link">1. Our Aim</a>
              <a href="#works" class="list-group-item anchor-link">2. How It Works</a>
              <a href="#payment" class="list-group-item anchor-link">3. Payment Policy</a>
              <a href="#delivery" class="list-group-item anchor-link">4. Delivery Policy</a>
              <a href="#draw" class="list-group-item anchor-link">5. Lucky Draw Process</a>
              <a href="#eligibility" class="list-group-item anchor-link">6. Eligibility Criteria</a>
              <a href="#notes" class="list-group-item anchor-link">7. Important Notes</a>
              <a href="#contact" class="list-group-item anchor-link">8. Contact</a>
            </div>
          </div>
        </div>
      </aside>

      <section class="col-lg-8">
        <article class="tc-card p-4 p-lg-5">

          <h3 id="aim" class="section-title">1) Our Aim</h3>
          <p>We aim to provide high-quality perfumes and give our customers the opportunity to win cash prizes through our Lucky Draw program. Buy Perfume – Become Our Lucky Draw Member!</p>

          <h3 id="works" class="section-title">2) How It Works</h3>
          <ul>
            <li>Each perfume purchase earns you 1 Lucky Draw ticket.</li>
            <li>You can purchase unlimited perfumes; each purchase gives an additional ticket.</li>
            <li>More purchases = more tickets = higher winning chances.</li>
            <li>Tickets are converted into chits (پرچیاں) and placed in the Lucky Draw bowl.</li>
            <li>The draw will be conducted live on YouTube for transparency.</li>
            <li>Winners are randomly selected during the live event.</li>
          </ul>

          <h3 id="payment" class="section-title">3) Payment Policy</h3>
          <div class="not-refundable mb-3">
            <strong>Important — Payments are Non-Refundable:</strong>
            Once a payment is made, the order cannot be canceled or refunded under any circumstances.
          </div>
          <p>Please ensure your order and details are correct before proceeding to payment.</p>

          <h3 id="delivery" class="section-title">4) Delivery Policy</h3>
          <ul>
            <li>Perfume delivery will be made after the Lucky Draw winners are announced.</li>
            <li>Customers must pay delivery charges; minimum is PKR 570 (may vary by location).</li>
            <li>Delivery will be made to the address provided at purchase; ensure accuracy of details.</li>
          </ul>

          <h3 id="draw" class="section-title">5) Lucky Draw Process</h3>
          <ul>
            <li>You will receive your Lucky Draw ticket number after purchase.</li>
            <li>All tickets are printed as chits (پرچیاں) and entered into the draw bowl.</li>
            <li>15% service fee will be deducted from winning amount.</li>
            <li>The draw will be streamed live on our official YouTube channel on the announced date.</li>
            <li>Winners will be contacted directly after the live event.</li>
          </ul>

          <h3 id="eligibility" class="section-title">6) Eligibility Criteria</h3>
          <ul>
            <li>Participants must be 18 years or older.</li>
            <li>Offer valid only where lucky draws are legally permitted.</li>
            <li>By purchasing, you confirm details are correct and agree to these terms.</li>
          </ul>

          <h3 id="notes" class="section-title">7) Important Notes</h3>
          <ul>
            <li>Buying a perfume gives you a chance to win but does not guarantee a prize.</li>
            <li>Orders without delivery charge payment will not be shipped.</li>
            <li>We are not responsible for technical issues during the draw or delivery delays.</li>
            <li>We reserve the right to change terms, draw dates, or prize details without notice.</li>
          </ul>

          <h3 id="contact" class="section-title">8) Contact</h3>
          <ul>
            
            <li>WhatsApp: <a href="{{ $waLink }}" target="_blank" rel="noopener">{{ $wa }}</a></li>
          </ul>

        </article>
      </section>

    </div>
  </div>
</main>

<footer class="py-4 text-center text-muted small">
  © {{ date('Y') }} {{ $app }}. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
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
