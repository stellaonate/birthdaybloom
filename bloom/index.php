<?php $pageTitle = 'Home'; require 'includes/header.php'; ?>
<section class="hero">
  <div class="container hero-grid">
    <div>
      <span class="eyebrow">✨ Celebrate the people you love</span>
      <h1>Never miss a <span>special day</span> again.</h1>
      <p>Save birthdays, see beautiful countdowns, and stay ready to celebrate your favorite people.</p>
      <div class="hero-actions">
        <a class="btn" href="register.php">Start Remembering →</a>
        <a class="btn btn-outline" href="dashboard.php">Open Dashboard</a>
      </div>
      <p class="hero-note">Create an account to view the protected dashboard · Made with love 💗</p>
    </div>
    <div class="hero-art" aria-label="Birthday celebration illustration">
      <span class="confetti confetti-one">✦</span><span class="confetti confetti-two">✳</span>
      <span class="big-cake" aria-hidden="true">🎂</span>
      <div class="floating-note">🎉 Your next celebration is closer than you think!</div>
    </div>
  </div>
</section>
<section class="section" id="features">
  <div class="container">
    <div class="section-heading"><span class="eyebrow">Everything in one place</span><h2>Little reminders, big smiles.</h2><p>Organize the dates that matter without the stress.</p></div>
    <div class="feature-grid">
      <article class="feature-card"><span class="feature-icon">📅</span><h3>Save Important Dates</h3><p>Keep family, friends, and colleagues' birthdays in one convenient place.</p></article>
      <article class="feature-card"><span class="feature-icon">⏳</span><h3>Birthday Countdowns</h3><p>See how many days remain until the next special celebration.</p></article>
      <article class="feature-card"><span class="feature-icon">🔔</span><h3>Upcoming Reminders</h3><p>Quickly spot birthdays coming up in the next seven days.</p></article>
    </div>
  </div>
</section>
<section class="section about-section" id="about"><div class="container about-box"><div><span class="eyebrow">Why BirthdayBloom?</span><h2>Make every person feel remembered.</h2><p>From best friends to family members, BirthdayBloom makes it easier to keep track of birthdays and plan thoughtful celebrations.</p></div><div class="about-emoji" aria-hidden="true">🎈💌🎁</div></div></section>
<section class="section cta-section"><div class="container cta-card"><h2>Ready to spread birthday joy?</h2><p>Create your account and start collecting important dates.</p><a class="btn btn-light" href="register.php">Create an Account</a></div></section>
<?php require 'includes/footer.php'; ?>
