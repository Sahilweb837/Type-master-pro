 <?php
/**
 * Custom Type Master Pro
 * Global Footer Template — B&W Editorial Edition
 */
?>
    </main>

    <!-- ============================================================
         APP FOOTER — B&W Editorial
         ============================================================ -->
    <style>
    /* ============================================================
       FOOTER SCOPED STYLES — .tm-footer
       ============================================================ */
    .tm-footer {
        --ft-paper:    #fafaf8;
        --ft-paper-2:  #f2f1ec;
        --ft-ink:      #0b0b0b;
        --ft-ink-2:    #1f1f1f;
        --ft-muted:    #6b6a63;
        --ft-muted-2:  #94918a;
        --ft-line:     #d8d6cd;
        --ft-line-2:   #c4c1b6;
        --ft-accent:   #ff4d00;
        --ft-mono:     "JetBrains Mono", ui-monospace, "SF Mono", Menlo, Consolas, monospace;
        --ft-sans:     "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;

        background: var(--ft-paper);
        color: var(--ft-ink);
        border-top: 1px solid var(--ft-line);
        font-family: var(--ft-sans);
        margin-top: auto;
        position: relative;
    }
    .tm-footer * { box-sizing: border-box; }

    /* Inset top accent hairline */
    .tm-footer::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        background: var(--ft-ink);
    }

    .tm-footer-inner {
        max-width: 1240px;
        margin: 0 auto;
        padding: 3rem 2rem 1.75rem;
    }
    @media (max-width: 640px) {
        .tm-footer-inner { padding: 2.25rem 1.25rem 1.25rem; }
    }

    /* ---------- Grid ---------- */
    .tm-footer-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr 1fr 1.2fr;
        gap: 2.5rem;
        padding-bottom: 2.5rem;
        border-bottom: 1px solid var(--ft-line);
        margin-bottom: 1.5rem;
    }
    @media (max-width: 991px) {
        .tm-footer-grid {
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
    }
    @media (max-width: 575px) {
        .tm-footer-grid {
            grid-template-columns: 1fr;
            gap: 1.75rem;
        }
    }

    /* ---------- Col 1: Brand ---------- */
    .tm-ft-brand {
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
        text-decoration: none;
        color: var(--ft-ink);
        margin-bottom: 1rem;
    }
    .tm-ft-brand-mark {
        width: 34px;
        height: 34px;
        background: var(--ft-ink);
        color: var(--ft-paper);
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: background 0.2s ease;
    }
    .tm-ft-brand:hover .tm-ft-brand-mark {
        background: var(--ft-accent);
    }
    .tm-ft-brand-name {
        font-weight: 800;
        font-size: 1.1rem;
        letter-spacing: -0.02em;
        color: var(--ft-ink);
    }
    .tm-ft-brand-name span {
        font-weight: 400;
        color: var(--ft-muted);
    }
    .tm-ft-badge {
        font-family: var(--ft-mono);
        font-size: 0.55rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        background: var(--ft-ink);
        color: var(--ft-paper);
        padding: 0.15rem 0.35rem;
        border-radius: 3px;
        margin-left: 0.35rem;
        vertical-align: middle;
    }

    .tm-ft-desc {
        font-size: 0.86rem;
        line-height: 1.65;
        color: var(--ft-muted);
        max-width: 320px;
        margin: 0 0 1.15rem;
    }

    .tm-ft-status {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.75rem;
        border: 1px solid var(--ft-line);
        border-radius: 999px;
        background: var(--ft-paper-2);
        font-family: var(--ft-mono);
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        color: var(--ft-ink);
    }
    .tm-ft-status .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--ft-accent);
        box-shadow: 0 0 0 3px rgba(255, 77, 0, 0.18);
        animation: tmFtPulse 1.8s ease-in-out infinite;
    }
    @keyframes tmFtPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    /* ---------- Column headings ---------- */
    .tm-ft-heading {
        font-family: var(--ft-mono);
        font-size: 0.66rem;
        font-weight: 600;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--ft-muted);
        margin: 0 0 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .tm-ft-heading::before {
        content: "";
        width: 14px;
        height: 1px;
        background: var(--ft-accent);
        flex-shrink: 0;
    }

    /* ---------- Links ---------- */
    .tm-ft-links {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }
    .tm-ft-links a,
    .tm-ft-links button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--ft-ink-2);
        text-decoration: none;
        background: transparent;
        border: none;
        padding: 0;
        cursor: pointer;
        font-family: inherit;
        line-height: 1.3;
        transition: color 0.15s ease, transform 0.15s ease;
        text-align: left;
    }
    .tm-ft-links a:hover,
    .tm-ft-links button:hover {
        color: var(--ft-accent);
        transform: translateX(2px);
    }
    .tm-ft-links a i,
    .tm-ft-links button i {
        font-size: 0.78rem;
        color: var(--ft-muted-2);
        transition: color 0.15s ease;
        flex-shrink: 0;
    }
    .tm-ft-links a:hover i,
    .tm-ft-links button:hover i {
        color: var(--ft-accent);
    }

    /* Theme swatches (kept minimal — B&W only) */
    .tm-ft-theme-row {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.35rem;
    }
    .tm-ft-theme-swatch {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        border: 2px solid var(--ft-line-2);
        cursor: pointer;
        padding: 0;
        transition: all 0.15s ease;
        position: relative;
    }
    .tm-ft-theme-swatch:hover {
        border-color: var(--ft-ink);
        transform: scale(1.08);
    }
    .tm-ft-theme-swatch.active {
        border-color: var(--ft-accent);
        box-shadow: 0 0 0 2px rgba(255, 77, 0, 0.25);
    }
    .tm-ft-theme-swatch[data-set-theme="light"] {
        background: linear-gradient(135deg, #fafaf8 50%, #e8e6df 50%);
    }
    .tm-ft-theme-swatch[data-set-theme="dark"] {
        background: linear-gradient(135deg, #0b0b0b 50%, #1f1f1f 50%);
    }

    /* ---------- Ergo card ---------- */
    .tm-ft-ergo {
        background: var(--ft-paper-2);
        border: 1px solid var(--ft-line);
        border-radius: 8px;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }
    .tm-ft-ergo-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.78rem;
        color: var(--ft-muted);
    }
    .tm-ft-ergo-row:not(:last-child) {
        padding-bottom: 0.65rem;
        border-bottom: 1px dashed var(--ft-line);
    }
    .tm-ft-ergo-row kbd {
        font-family: var(--ft-mono);
        font-size: 0.65rem;
        background: var(--ft-paper);
        color: var(--ft-ink);
        border: 1px solid var(--ft-line-2);
        border-radius: 3px;
        padding: 0.1rem 0.35rem;
        font-weight: 700;
        display: inline-block;
        min-width: 20px;
        text-align: center;
    }
    .tm-ft-ergo-badge {
        font-family: var(--ft-mono);
        font-size: 0.62rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        background: var(--ft-ink);
        color: var(--ft-paper);
        padding: 0.15rem 0.45rem;
        border-radius: 3px;
        white-space: nowrap;
    }

    /* ---------- Sub-footer ---------- */
    .tm-ft-bottom {
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
        align-items: center;
        text-align: center;
        font-size: 0.78rem;
        color: var(--ft-muted);
    }
    @media (min-width: 768px) {
        .tm-ft-bottom {
            flex-direction: row;
            justify-content: space-between;
            text-align: left;
        }
    }
    .tm-ft-bottom strong {
        color: var(--ft-ink);
        font-weight: 700;
    }
    .tm-ft-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
        justify-content: center;
    }
    .tm-ft-pill {
        font-family: var(--ft-mono);
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        background: var(--ft-paper-2);
        border: 1px solid var(--ft-line);
        border-radius: 999px;
        padding: 0.25rem 0.65rem;
        color: var(--ft-ink-2);
        white-space: nowrap;
    }

    /* ---------- Dark theme overrides ---------- */
    html[data-theme="dark"] .tm-footer {
        --ft-paper:    #0b0b0b;
        --ft-paper-2:  #141414;
        --ft-ink:      #fafaf8;
        --ft-ink-2:    #e5e5e5;
        --ft-muted:    #8a8a8a;
        --ft-muted-2:  #6b6b6b;
        --ft-line:     #262626;
        --ft-line-2:   #333333;
    }
    html[data-theme="dark"] .tm-footer .tm-ft-brand-mark {
        background: var(--ft-ink);
        color: var(--ft-paper);
    }
    html[data-theme="dark"] .tm-footer .tm-ft-theme-swatch[data-set-theme="light"] {
        border-color: #444;
    }
    </style>

    <footer class="tm-footer">
      <div class="tm-footer-inner">
        <div class="tm-footer-grid">

          <!-- Col 1: Brand & Status -->
          <div>
            <a href="<?= BASE_URL ?>/dashboard.php" class="tm-ft-brand">
              <span class="tm-ft-brand-mark">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="2" y="2" width="20" height="20" rx="5" fill="currentColor"/>
                  <path d="M8 9H16M12 9V17" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
                </svg>
              </span>
              <span class="tm-ft-brand-name">
                TypeMaster<span>.pro</span>
                <span class="tm-ft-badge">PRO</span>
              </span>
            </a>

            <p class="tm-ft-desc">
              Engineered for extreme typing precision, keystroke-level diagnostics, real-time WPM calculation, and distraction-free speed acceleration.
            </p>

            <div class="tm-ft-status">
              <span class="dot"></span>
              <span>Typing Engine Active &bull; Zero Input Lag</span>
            </div>
          </div>

          <!-- Col 2: Navigation -->
          <div>
            <h6 class="tm-ft-heading">Quick Navigation</h6>
            <ul class="tm-ft-links">
              <li><a href="<?= BASE_URL ?>/practice.php"><i class="bi bi-play-circle"></i> Practice Arena</a></li>
              <li><a href="<?= BASE_URL ?>/games.php"><i class="bi bi-controller"></i> Arcade Games</a></li>
              <li><a href="<?= BASE_URL ?>/lessons.php"><i class="bi bi-mortarboard"></i> Typing Lessons</a></li>
              <li><a href="<?= BASE_URL ?>/dashboard.php"><i class="bi bi-grid-fill"></i> Speed Dashboard</a></li>
              <li><a href="<?= BASE_URL ?>/history.php"><i class="bi bi-clock-history"></i> Test History</a></li>
            </ul>
          </div>

          <!-- Col 3: Themes -->
          <div>
            <h6 class="tm-ft-heading">Themes</h6>
            <ul class="tm-ft-links" style="margin-bottom: 0.75rem;">
              <li><button type="button" data-set-theme="light"><i class="bi bi-sun-fill"></i> Light (White)</button></li>
              <li><button type="button" data-set-theme="dark"><i class="bi bi-moon-stars-fill"></i> Dark (Obsidian)</button></li>
            </ul>
            <div class="tm-ft-theme-row">
              <button type="button" class="tm-ft-theme-swatch" data-set-theme="light" title="Light theme" aria-label="Switch to light theme"></button>
              <button type="button" class="tm-ft-theme-swatch" data-set-theme="dark" title="Dark theme" aria-label="Switch to dark theme"></button>
            </div>
          </div>

          <!-- Col 4: Ergonomics -->
          <div>
            <h6 class="tm-ft-heading">Keyboard Ergonomics</h6>
            <div class="tm-ft-ergo">
              <div class="tm-ft-ergo-row">
                <span>Quick Restart</span>
                <span><kbd>Tab</kbd> + <kbd>Enter</kbd></span>
              </div>
              <div class="tm-ft-ergo-row">
                <span>Formula Base</span>
                <span class="tm-ft-ergo-badge">5-CHAR WORD</span>
              </div>
              <div class="tm-ft-ergo-row">
                <span>Default Duration</span>
                <span class="tm-ft-ergo-badge">60 SEC</span>
              </div>
            </div>
          </div>

        </div>

        <!-- Sub-footer -->
        <div class="tm-ft-bottom">
          <div>
            &copy; <?= date('Y') ?> <strong><?= defined('APP_NAME') ? e(APP_NAME) : 'TypeMaster Pro' ?></strong>
            <?php if (defined('APP_VERSION')): ?>
              v<?= e(APP_VERSION) ?>
            <?php endif; ?>
            &mdash; All rights reserved.
          </div>
          <div class="tm-ft-meta">
            <span class="tm-ft-pill">Vanilla CSS</span>
            <span class="tm-ft-pill">PHP 8.2+</span>
            <span class="tm-ft-pill">MySQL</span>
          </div>
        </div>
      </div>
    </footer>
  </div><!-- /.main-wrapper -->
</div><!-- /.app-container -->

<!-- ============================================================
     THEME TOGGLE — wires all [data-set-theme] elements
     ============================================================ -->
<script>
(function() {
    'use strict';

    function applyTheme(theme) {
        if (theme !== 'dark' && theme !== 'light') theme = 'light';
        document.documentElement.setAttribute('data-theme', theme);
        try { localStorage.setItem('typemaster_theme', theme); } catch (e) {}

        // Sync active state on all swatches
        document.querySelectorAll('.tm-ft-theme-swatch').forEach(function(sw) {
            sw.classList.toggle('active', sw.getAttribute('data-set-theme') === theme);
        });
    }

    // Wire up every theme toggle control anywhere on the page
    document.addEventListener('click', function(e) {
        var trigger = e.target.closest('[data-set-theme]');
        if (!trigger) return;
        e.preventDefault();
        applyTheme(trigger.getAttribute('data-set-theme'));
    });

    // Boot: sync swatch active state to current theme
    var current = document.documentElement.getAttribute('data-theme') || 'light';
    if (current !== 'dark') current = 'light';
    document.querySelectorAll('.tm-ft-theme-swatch').forEach(function(sw) {
        sw.classList.toggle('active', sw.getAttribute('data-set-theme') === current);
    });
})();
</script>

<!-- Bootstrap 5 Bundle JS with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom App Utilities -->
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>

<?php if (!empty($extraScripts)): ?>
  <?= $extraScripts ?>
<?php endif; ?>

</body>
</html> 