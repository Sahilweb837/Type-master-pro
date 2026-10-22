<?php
/**
 * Custom Type Master Pro
 * Global Footer Template
 */
?>
    </main>

    </main>

    <!-- Global Modern Base Footer -->
    <footer class="app-footer mt-auto">
      <div class="container-fluid px-md-4 py-4 border-top" style="border-color: var(--border-color) !important;">
        <div class="row g-4 mb-4">
          <!-- Col 1: Brand & Status -->
          <div class="col-lg-4 col-md-6">
            <div class="d-flex align-items-center gap-2 mb-2">
              <div class="brand-logo-badge" style="width: 28px; height: 28px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="2" y="2" width="20" height="20" rx="5" fill="#ff5532"/>
                  <rect x="4.5" y="4.5" width="15" height="15" rx="3.5" fill="#ffffff" fill-opacity="0.3" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
                  <path d="M8 9H16M12 9V17" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round"/>
                  <circle cx="16.5" cy="7.5" r="1.5" fill="#ff7654"/>
                </svg>
              </div>
              <span class="fw-bold fs-5" style="letter-spacing: -0.3px;">Type<span style="color: var(--first-color);">Master</span> <span class="badge" style="background: var(--first-color); font-size: 0.65rem; vertical-align: middle;">PRO</span></span>
            </div>
            <p class="text-secondary small mb-3" style="max-width: 320px; line-height: 1.6;">
              Engineered for extreme typing precision, keystroke-level diagnostics, real-time WPM calculation, and distraction-free speed acceleration.
            </p>
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill small" style="background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: var(--soft-flat);">
              <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; display: inline-block; box-shadow: 0 0 8px #10b981;"></span>
              <span class="text-secondary" style="font-size: 0.78rem; font-weight: 600;">Typing Engine Active &bull; Zero Input Lag</span>
            </div>
          </div>

          <!-- Col 2: Navigation Links -->
          <div class="col-lg-3 col-6 col-md-3">
            <h6 class="fw-bold mb-3" style="color: var(--text-primary); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Quick Navigation</h6>
            <ul class="list-unstyled d-flex flex-column gap-2 small">
              <li><a href="<?= BASE_URL ?>/practice.php" class="text-secondary text-decoration-none hover-coral"><i class="bi bi-play-circle me-1"></i> Practice Arena</a></li>
              <li><a href="<?= BASE_URL ?>/dashboard.php" class="text-secondary text-decoration-none hover-coral"><i class="bi bi-grid-fill me-1"></i> Speed Dashboard</a></li>
              <li><a href="<?= BASE_URL ?>/history.php" class="text-secondary text-decoration-none hover-coral"><i class="bi bi-clock-history me-1"></i> Test History & Exports</a></li>
              <li><a href="<?= BASE_URL ?>/settings.php" class="text-secondary text-decoration-none hover-coral"><i class="bi bi-sliders me-1"></i> Customization Suite</a></li>
            </ul>
          </div>

          <!-- Col 3: Visual & Audio Engine -->
          <div class="col-lg-2 col-6 col-md-3">
            <h6 class="fw-bold mb-3" style="color: var(--text-primary); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Themes & Audio</h6>
            <ul class="list-unstyled d-flex flex-column gap-2 small">
              <li><a href="#" data-set-theme="light" class="text-secondary text-decoration-none hover-coral"><i class="bi bi-sun-fill me-1 text-warning"></i> Soft UI White</a></li>
              <li><a href="#" data-set-theme="orange" class="text-secondary text-decoration-none hover-coral"><i class="bi bi-fire me-1" style="color: #ff5532;"></i> Dark Orange</a></li>
              <li><a href="#" data-set-theme="dark" class="text-secondary text-decoration-none hover-coral"><i class="bi bi-moon-stars-fill me-1 text-primary"></i> Dark Obsidian</a></li>
              <li><a href="#" data-set-theme="cyberpunk" class="text-secondary text-decoration-none hover-coral"><i class="bi bi-lightning-charge-fill me-1 text-danger"></i> Cyberpunk</a></li>
            </ul>
          </div>

          <!-- Col 4: Keyboard Shortcuts & Scientific Formulas -->
          <div class="col-lg-3 col-md-6">
            <h6 class="fw-bold mb-3" style="color: var(--text-primary); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Keyboard Ergonomics</h6>
            <div class="p-3 rounded-3" style="background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: var(--soft-flat);">
              <div class="d-flex justify-content-between align-items-center mb-2 small">
                <span class="text-secondary">Quick Restart</span>
                <span><kbd class="px-2 py-1 rounded small" style="background: var(--bg-primary); color: var(--first-color); border: 1px solid var(--border-color);">Tab</kbd> + <kbd class="px-2 py-1 rounded small" style="background: var(--bg-primary); color: var(--first-color); border: 1px solid var(--border-color);">Enter</kbd></span>
              </div>
              <div class="d-flex justify-content-between align-items-center small">
                <span class="text-secondary">Formula Base</span>
                <span class="badge" style="background: var(--first-color-light); color: var(--first-color); font-weight: 700;">Standard 5-Char Word</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Sub-footer copyright bar -->
        <div class="pt-3 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small text-secondary" style="border-color: var(--border-color) !important;">
          <div>
            &copy; <?= date('Y') ?> <strong><?= APP_NAME ?></strong> v<?= APP_VERSION ?>. All rights reserved.
          </div>
          <div class="d-flex align-items-center gap-3">
            <span class="badge" style="background: var(--bg-card); color: var(--second-color); border: 1px solid var(--border-color);">Vanilla CSS &amp; Modern UI</span>
            <span class="badge" style="background: var(--bg-card); color: var(--second-color); border: 1px solid var(--border-color);">PHP 8.2+ &amp; MySQL</span>
          </div>
        </div>
      </div>
    </footer>
  </div><!-- /.main-wrapper -->
</div><!-- /.app-container -->

<!-- Bootstrap 5 Bundle JS with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom App Utilities -->
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>

<?php if (!empty($extraScripts)): ?>
  <?= $extraScripts ?>
<?php endif; ?>

</body>
</html>
