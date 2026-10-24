<?php
/**
 * Custom Type Master Pro
 * Global Footer Template — Pixel Theme / Sans-Serif Readable
 */
?>
    </main>

    <style>
    .tm-footer {
        --ft-ink:      #000000;
        --ft-paper:    #ffffff;
        --ft-paper-2:  #f0f0f0;
        --ft-paper-3:  #e0e0e0;
        --ft-shadow:   3px;
        --ft-sans:     "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        --ft-pixel:    "Press Start 2P", "Courier New", monospace;
        --ft-mono:     "JetBrains Mono", ui-monospace, "SF Mono", Menlo, Consolas, monospace;

        background: var(--ft-paper);
        color: var(--ft-ink);
        border-top: 4px solid var(--ft-ink);
        font-family: var(--ft-sans);
        margin-top: auto;
        position: relative;
        -webkit-font-smoothing: antialiased;
        image-rendering: pixelated;
    }
    .tm-footer * { box-sizing: border-box; }

    .tm-footer::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 1;
        background-image:
            linear-gradient(rgba(0,0,0,0.045) 1px, transparent 1px),
            linear-gradient(90deg, rgba(0,0,0,0.045) 1px, transparent 1px);
        background-size: 4px 4px;
    }
    .tm-footer > * { position: relative; z-index: 2; }
    html[data-theme="dark"] .tm-footer {
        --ft-ink:      #ffffff;
        --ft-paper:    #000000;
        --ft-paper-2:  #1a1a1a;
        --ft-paper-3:  #222222;
    }
    html[data-theme="dark"] .tm-footer::before {
        background-image:
            linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px);
    }

    .tm-footer-inner {
        max-width: 1240px;
        margin: 0 auto;
        padding: 2.75rem 2rem 1.5rem;
    }
    @media (max-width: 640px) {
        .tm-footer-inner { padding: 2rem 1.15rem 1.1rem; }
    }

    .tm-footer-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr 1fr 1.2fr;
        gap: 2rem;
        padding-bottom: 2rem;
        margin-bottom: 1.5rem;
        position: relative;
    }
    .tm-footer-grid::after {
        content: "";
        position: absolute;
        left: 0; right: 0; bottom: 0;
        height: 3px;
        background: var(--ft-ink);
    }
    @media (max-width: 991px) {
        .tm-footer-grid { grid-template-columns: 1fr 1fr; gap: 1.75rem; }
    }
    @media (max-width: 575px) {
        .tm-footer-grid { grid-template-columns: 1fr; gap: 1.5rem; }
    }

    /* Brand */
    .tm-ft-brand {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        text-decoration: none;
        color: var(--ft-ink);
        margin-bottom: 1.1rem;
    }
    .tm-ft-brand-mark {
        width: 36px; height: 36px;
        background: var(--ft-ink);
        color: var(--ft-paper);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        position: relative;
        image-rendering: pixelated;
        transition: transform 0.05s steps(1);
    }
    .tm-ft-brand-mark::after {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(45deg, var(--ft-paper) 25%, transparent 25%),
            linear-gradient(-45deg, var(--ft-paper) 25%, transparent 25%),
            linear-gradient(45deg, transparent 75%, var(--ft-paper) 75%),
            linear-gradient(-45deg, transparent 75%, var(--ft-paper) 75%);
        background-size: 4px 4px;
        background-position: 0 0, 0 2px, 2px -2px, -2px 0;
        opacity: 0.15;
        pointer-events: none;
    }
    .tm-ft-brand-mark svg { position: relative; z-index: 1; }
    .tm-ft-brand:hover .tm-ft-brand-mark {
        transform: translate(2px, 2px);
        box-shadow: -2px -2px 0 var(--ft-ink);
    }
    /* DECORATIVE — pixel font allowed */
    .tm-ft-brand-name {
        font-family: var(--ft-pixel);
        font-weight: 400;
        font-size: 0.65rem;
        letter-spacing: 0.02em;
        color: var(--ft-ink);
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.4rem;
        line-height: 1.5;
    }
    .tm-ft-brand-name .dot-pro {
        font-family: var(--ft-sans);
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        color: var(--ft-ink);
        opacity: 0.6;
    }
    /* Decorative badge — small pixel */
    .tm-ft-badge {
        font-family: var(--ft-pixel);
        font-size: 0.55rem;
        letter-spacing: 0.04em;
        background: var(--ft-ink);
        color: var(--ft-paper);
        padding: 0.18rem 0.35rem;
        border: 2px solid var(--ft-ink);
        line-height: 1.4;
        display: inline-block;
    }

    /* Description — sans-serif readable */
    .tm-ft-desc {
        font-family: var(--ft-sans);
        font-size: 0.88rem;
        line-height: 1.6;
        color: var(--ft-ink);
        max-width: 340px;
        margin: 0 0 1.1rem;
    }

    .tm-ft-status {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.65rem;
        border: 2px solid var(--ft-ink);
        background: var(--ft-paper);
        box-shadow: 2px 2px 0 var(--ft-ink);
        font-family: var(--ft-sans);
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: var(--ft-ink);
    }
    .tm-ft-status .dot {
        width: 7px; height: 7px;
        background: var(--ft-ink);
        animation: tmFtBlink 1.2s steps(1) infinite;
    }
    @keyframes tmFtBlink {
        0%, 49% { opacity: 1; }
        50%, 100% { opacity: 0; }
    }

    /* Headings — sans-serif */
    .tm-ft-heading {
        font-family: var(--ft-sans);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--ft-ink);
        margin: 0 0 1rem;
        display: flex;
        align-items: center;
        gap: 0.55rem;
    }
    .tm-ft-heading::before {
        content: "";
        width: 12px; height: 12px;
        background: var(--ft-ink);
        flex-shrink: 0;
    }

    /* Links — sans-serif */
    .tm-ft-links {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
    }
    .tm-ft-links a,
    .tm-ft-links button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-family: var(--ft-sans);
        font-size: 0.86rem;
        font-weight: 500;
        color: var(--ft-ink);
        text-decoration: none;
        background: transparent;
        border: none;
        padding: 0.15rem 0;
        cursor: pointer;
        line-height: 1.3;
        transition: transform 0.05s steps(1);
        text-align: left;
        border-bottom: 2px solid transparent;
    }
    .tm-ft-links a:hover,
    .tm-ft-links button:hover {
        border-bottom-color: var(--ft-ink);
        transform: translateX(2px);
    }
    .tm-ft-links a i,
    .tm-ft-links button i {
        font-size: 0.82rem;
        color: var(--ft-ink);
        flex-shrink: 0;
    }

    /* Theme swatches */
    .tm-ft-theme-row {
        display: flex;
        gap: 0.55rem;
        margin-top: 0.45rem;
    }
    .tm-ft-theme-swatch {
        width: 30px; height: 30px;
        border-radius: 0;
        border: 3px solid var(--ft-ink);
        box-shadow: 2px 2px 0 var(--ft-ink);
        cursor: pointer;
        padding: 0;
        transition: transform 0.05s steps(1), box-shadow 0.05s steps(1);
        position: relative;
        background: var(--ft-paper);
        -webkit-appearance: none;
        appearance: none;
    }
    .tm-ft-theme-swatch:hover { transform: translate(-1px, -1px); box-shadow: 3px 3px 0 var(--ft-ink); }
    .tm-ft-theme-swatch:active {
        transform: translate(2px, 2px);
        box-shadow: 0 0 0 var(--ft-ink);
    }
    .tm-ft-theme-swatch.active { box-shadow: 0 0 0 var(--ft-ink), 0 0 0 2px var(--ft-paper) inset; }
    .tm-ft-theme-swatch[data-set-theme="light"] { background: var(--ft-paper); }
    .tm-ft-theme-swatch[data-set-theme="dark"]  { background: var(--ft-ink); }
    .tm-ft-theme-swatch.active::after {
        content: "";
        position: absolute;
        left: 50%; top: 50%;
        width: 6px; height: 6px;
        transform: translate(-50%, -50%);
        background: var(--ft-ink);
    }
    .tm-ft-theme-swatch[data-set-theme="dark"].active::after { background: var(--ft-paper); }

    /* Ergo card */
    .tm-ft-ergo {
        background: var(--ft-paper);
        border: 3px solid var(--ft-ink);
        box-shadow: var(--ft-shadow) var(--ft-shadow) 0 var(--ft-ink);
        padding: 0.9rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }
    .tm-ft-ergo-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.5rem;
        font-family: var(--ft-sans);
        font-size: 0.82rem;
        font-weight: 500;
        color: var(--ft-ink);
    }
    .tm-ft-ergo-row:not(:last-child) {
        padding-bottom: 0.6rem;
        border-bottom: 2px dashed var(--ft-ink);
    }
    .tm-ft-ergo-row kbd {
        font-family: var(--ft-mono);
        font-size: 0.72rem;
        background: var(--ft-ink);
        color: var(--ft-paper);
        border: 2px solid var(--ft-ink);
        padding: 0.1rem 0.35rem;
        font-weight: 700;
        display: inline-block;
        min-width: 22px;
        text-align: center;
        line-height: 1.1;
    }
    .tm-ft-ergo-badge {
        font-family: var(--ft-mono);
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        background: var(--ft-ink);
        color: var(--ft-paper);
        padding: 0.15rem 0.45rem;
        border: 2px solid var(--ft-ink);
        white-space: nowrap;
    }

    /* Sub-footer */
    .tm-ft-bottom {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        align-items: center;
        text-align: center;
        font-family: var(--ft-sans);
        font-size: 0.82rem;
        color: var(--ft-ink);
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
        font-family: var(--ft-sans);
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        background: var(--ft-paper);
        border: 2px solid var(--ft-ink);
        box-shadow: 2px 2px 0 var(--ft-ink);
        padding: 0.2rem 0.55rem;
        color: var(--ft-ink);
        white-space: nowrap;
    }
    </style>

    <footer class="tm-footer">
      <div class="tm-footer-inner">
        <div class="tm-footer-grid">

          <div>
            <a href="<?= BASE_URL ?>/dashboard.php" class="tm-ft-brand">
              <span class="tm-ft-brand-mark">
                <svg width="20" height="20" viewBox="0 0 8 8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" shape-rendering="crispEdges">
                  <rect x="0" y="0" width="8" height="8" fill="currentColor"/>
                  <rect x="1" y="1" width="3" height="1" fill="var(--ft-paper)"/>
                  <rect x="2" y="2" width="1" height="4" fill="var(--ft-paper)"/>
                  <rect x="5" y="1" width="1" height="5" fill="var(--ft-paper)"/>
                  <rect x="6" y="1" width="1" height="1" fill="var(--ft-paper)"/>
                  <rect x="6" y="3" width="1" height="1" fill="var(--ft-paper)"/>
                  <rect x="6" y="5" width="1" height="1" fill="var(--ft-paper)"/>
                </svg>
              </span>
              <span class="tm-ft-brand-name">
                TypeMaster<span class="dot-pro">.pro</span>
                <span class="tm-ft-badge">PRO</span>
              </span>
            </a>

            <p class="tm-ft-desc">
              Engineered for extreme typing precision, keystroke-level diagnostics, real-time WPM calculation, and distraction-free speed acceleration.
            </p>

            <div class="tm-ft-status">
              <span class="dot"></span>
              <span>Engine Active &bull; Zero Lag</span>
            </div>
          </div>

          <div>
            <h6 class="tm-ft-heading">Quick Nav</h6>
            <ul class="tm-ft-links">
              <li><a href="<?= BASE_URL ?>/practice.php"><i class="bi bi-play-circle"></i> Practice Arena</a></li>
              <li><a href="<?= BASE_URL ?>/games.php"><i class="bi bi-controller"></i> Arcade Games</a></li>
              <li><a href="<?= BASE_URL ?>/lessons.php"><i class="bi bi-mortarboard"></i> Typing Lessons</a></li>
              <li><a href="<?= BASE_URL ?>/dashboard.php"><i class="bi bi-grid-fill"></i> Speed Dashboard</a></li>
              <li><a href="<?= BASE_URL ?>/history.php"><i class="bi bi-clock-history"></i> Test History</a></li>
            </ul>
          </div>

          <div>
            <h6 class="tm-ft-heading">Themes</h6>
            <ul class="tm-ft-links" style="margin-bottom: 0.75rem;">
              <li><button type="button" data-set-theme="light"><i class="bi bi-sun-fill"></i> Light</button></li>
              <li><button type="button" data-set-theme="dark"><i class="bi bi-moon-stars-fill"></i> Dark</button></li>
            </ul>
            <div class="tm-ft-theme-row">
              <button type="button" class="tm-ft-theme-swatch" data-set-theme="light" title="Light theme" aria-label="Switch to light theme"></button>
              <button type="button" class="tm-ft-theme-swatch" data-set-theme="dark" title="Dark theme" aria-label="Switch to dark theme"></button>
            </div>
          </div>

          <div>
            <h6 class="tm-ft-heading">Ergonomics</h6>
            <div class="tm-ft-ergo">
              <div class="tm-ft-ergo-row">
                <span>Restart</span>
                <span><kbd>Tab</kbd> + <kbd>Enter</kbd></span>
              </div>
              <div class="tm-ft-ergo-row">
                <span>Formula</span>
                <span class="tm-ft-ergo-badge">5-CHAR</span>
              </div>
              <div class="tm-ft-ergo-row">
                <span>Duration</span>
                <span class="tm-ft-ergo-badge">60 SEC</span>
              </div>
            </div>
          </div>

        </div>

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

<script>
(function() {
    'use strict';
    function applyTheme(theme) {
        if (theme !== 'dark' && theme !== 'light') theme = 'light';
        document.documentElement.setAttribute('data-theme', theme);
        try { localStorage.setItem('typemaster_theme', theme); } catch (e) {}
        document.querySelectorAll('.tm-ft-theme-swatch').forEach(function(sw) {
            sw.classList.toggle('active', sw.getAttribute('data-set-theme') === theme);
        });
    }
    document.addEventListener('click', function(e) {
        var trigger = e.target.closest('[data-set-theme]');
        if (!trigger) return;
        e.preventDefault();
        applyTheme(trigger.getAttribute('data-set-theme'));
    });
    var current = document.documentElement.getAttribute('data-theme') || 'light';
    if (current !== 'dark') current = 'light';
    document.querySelectorAll('.tm-ft-theme-swatch').forEach(function(sw) {
        sw.classList.toggle('active', sw.getAttribute('data-set-theme') === current);
    });
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js?v=<?= filemtime(__DIR__ . '/../assets/js/app.js') ?>"></script>
<?php if (!empty($extraScripts)): ?>
  <?= $extraScripts ?>
<?php endif; ?>
</body>
</html>