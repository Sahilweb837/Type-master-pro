 <?php
/**
 * Custom Type Master Pro
 * Landing Page — Pixel Theme · Sans-Serif Readable
 * Fully responsive + fully working speed test
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$pageTitle = 'Master Your Typing Speed & Precision';
$noMainPadding = true;
include __DIR__ . '/includes/header.php';
?>

<style>
/* ============================================================
   PIXEL THEME · SANS-SERIF READABLE LANDING — TypeMaster PRO
   ============================================================ */
:root {
    --px-ink:      #f8fafc;
    --px-paper:    #0a0e17;
    --px-paper-2:  #141c2e;
    --px-paper-3:  #192338;
    --px-shadow:   0px;

    /* READABLE BODY */
    --px-sans:     "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    /* DECORATIVE (brand only) */
    --px-pixel:    "Press Start 2P", "Courier New", monospace;
    /* NUMBERS / CODE */
    --px-mono:     "JetBrains Mono", ui-monospace, "SF Mono", Menlo, Consolas, monospace;
}

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&family=Press+Start+2P&display=swap');

html[data-theme="dark"] .tm-landing {
    --px-ink:      #f8fafc;
    --px-paper:    #0a0e17;
    --px-paper-2:  #141c2e;
    --px-paper-3:  #192338;
}

.tm-landing {
    background: var(--px-paper);
    color: var(--px-ink);
    font-family: var(--px-sans);
    overflow-x: hidden;
    position: relative;
    -webkit-font-smoothing: antialiased;
    -webkit-tap-highlight-color: transparent;
    image-rendering: pixelated;
}
.tm-landing * { box-sizing: border-box; }

/* 4px pixel-grid texture */
.tm-landing::before {
    content: "";
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 1;
    background-image:
        linear-gradient(rgba(0,0,0,0.045) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,0,0,0.045) 1px, transparent 1px);
    background-size: 4px 4px;
}
html[data-theme="dark"] .tm-landing::before {
    background-image:
        linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px);
}
.tm-landing > * { position: relative; z-index: 2; }

.tm-wrap {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 2rem;
}
@media (max-width: 640px) {
    .tm-wrap { padding: 0 1.1rem; }
}

/* Pixel button */
.tm-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.7rem 1.15rem;
    font-family: var(--px-sans);
    font-size: 0.86rem;
    font-weight: 600;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    text-decoration: none;
    border: 3px solid var(--px-ink);
    box-shadow: var(--px-shadow) var(--px-shadow) 0 var(--px-ink);
    background: var(--px-paper);
    color: var(--px-ink);
    cursor: pointer;
    transition: transform 0.05s steps(1), box-shadow 0.05s steps(1), background 0.05s steps(1);
    white-space: nowrap;
    line-height: 1.2;
    -webkit-appearance: none;
    appearance: none;
    user-select: none;
}
.tm-btn:hover { background: var(--px-paper-2); }
.tm-btn:active {
    transform: translate(var(--px-shadow), var(--px-shadow));
    box-shadow: 0 0 0 var(--px-ink);
}
.tm-btn-dark {
    background: var(--px-ink);
    color: var(--px-paper);
}
.tm-btn-dark:hover { background: var(--px-ink); color: var(--px-paper); }
.tm-btn-outline {
    background: var(--px-paper);
    color: var(--px-ink);
}
.tm-btn-sm { padding: 0.5rem 0.85rem; font-size: 0.76rem; }
.tm-btn-lg { padding: 0.9rem 1.4rem; font-size: 0.92rem; }

/* ===================== TOP BAR ===================== */
.tm-topbar {
    border-bottom: 4px solid var(--px-ink);
    padding: 0.9rem 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    flex-wrap: nowrap;
}
.tm-topbar-left {
    display: flex;
    align-items: center;
    gap: 1.75rem;
    min-width: 0;
}
.tm-logo {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    text-decoration: none;
    color: var(--px-ink);
    flex-shrink: 0;
}
/* DECORATIVE — pixel font allowed */
.tm-logo-mark {
    width: 32px; height: 32px;
    background: var(--px-ink);
    color: var(--px-paper);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-family: var(--px-pixel);
    font-size: 0.62rem;
    flex-shrink: 0;
    position: relative;
}
.tm-logo-mark::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(45deg, var(--px-paper) 25%, transparent 25%),
        linear-gradient(-45deg, var(--px-paper) 25%, transparent 25%);
    background-size: 4px 4px;
    opacity: 0.15;
    pointer-events: none;
}
.tm-logo-text {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
    gap: 0.1rem;
}
/* DECORATIVE — pixel wordmark */
.tm-logo-name {
    font-family: var(--px-pixel);
    font-size: 0.6rem;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}
.tm-logo-sub {
    font-family: var(--px-sans);
    font-size: 0.66rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    color: var(--px-ink);
    opacity: 0.7;
}
.tm-topbar-nav {
    display: flex;
    gap: 1.5rem;
    align-items: center;
}
.tm-topbar-nav a {
    color: var(--px-ink);
    text-decoration: none;
    font-family: var(--px-sans);
    font-size: 0.86rem;
    font-weight: 600;
    letter-spacing: 0.01em;
    padding: 0.2rem 0.1rem;
    border-bottom: 2px solid transparent;
    transition: border-color 0.05s steps(1);
}
.tm-topbar-nav a:hover { border-bottom-color: var(--px-ink); }
.tm-topbar-right {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
}

/* Live pill */
.tm-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.3rem 0.65rem;
    border: 2px solid var(--px-ink);
    box-shadow: 2px 2px 0 var(--px-ink);
    font-family: var(--px-sans);
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--px-ink);
    white-space: nowrap;
    background: var(--px-paper);
}
.tm-pill .dot {
    width: 6px; height: 6px;
    background: var(--px-ink);
    animation: pxBlink 1.2s steps(1) infinite;
}
@keyframes pxBlink {
    0%, 49% { opacity: 1; }
    50%, 100% { opacity: 0; }
}

@media (max-width: 900px) {
    .tm-topbar-nav { display: none; }
}
@media (max-width: 480px) {
    .tm-topbar { padding: 0.75rem 0; }
    .tm-pill { display: none; }
    .tm-logo-name { font-size: 0.5rem; }
    .tm-logo-sub { display: none; }
    .tm-btn-sm { padding: 0.45rem 0.7rem; font-size: 0.72rem; }
}

/* ===================== HERO ===================== */
.tm-hero {
    padding: 3rem 0 3.5rem;
    border-bottom: 4px solid var(--px-ink);
}
.tm-hero-intro {
    max-width: 900px;
    margin: 0 auto 2.5rem;
    text-align: center;
}

.tm-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-family: var(--px-sans);
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--px-ink);
    margin-bottom: 1.5rem;
    padding: 0.4rem 0.85rem;
    border: 2px solid var(--px-ink);
    box-shadow: 2px 2px 0 var(--px-ink);
    background: var(--px-paper);
}
.tm-eyebrow .dot {
    width: 6px; height: 6px;
    background: var(--px-ink);
    animation: pxBlink 1.2s steps(1) infinite;
}

/* Main heading — SANS-SERIF, readable */
.tm-hero h1 {
    font-family: var(--px-sans);
    font-size: clamp(2rem, 5.5vw, 3.6rem);
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -0.035em;
    margin: 0 0 1.35rem;
    color: var(--px-ink);
}
.tm-hero h1 em {
    font-style: normal;
    display: inline-block;
    position: relative;
    color: var(--px-ink);
}
.tm-hero h1 em::after {
    content: "";
    position: absolute;
    left: 0; right: 0;
    bottom: -6px;
    height: 5px;
    background: var(--px-ink);
}

.tm-hero-lead {
    font-family: var(--px-sans);
    font-size: clamp(0.95rem, 1.4vw, 1.08rem);
    line-height: 1.7;
    color: var(--px-ink);
    max-width: 640px;
    margin: 0 auto 2rem;
    font-weight: 400;
}

.tm-hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.85rem;
    justify-content: center;
    margin-bottom: 2.5rem;
}

/* Proof strip */
.tm-hero-proof {
    display: flex;
    justify-content: center;
    border: 3px solid var(--px-ink);
    box-shadow: var(--px-shadow) var(--px-shadow) 0 var(--px-ink);
    max-width: 780px;
    margin: 0 auto 2.75rem;
    background: var(--px-paper);
}
.tm-proof-item {
    flex: 1;
    padding: 1rem 1.25rem;
    text-align: center;
    border-right: 3px solid var(--px-ink);
}
.tm-proof-item:last-child { border-right: none; }
/* Numbers → mono */
.tm-proof-item .v {
    font-family: var(--px-mono);
    font-size: 1.05rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    margin-bottom: 0.4rem;
    color: var(--px-ink);
}
.tm-proof-item .l {
    font-family: var(--px-sans);
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    color: var(--px-ink);
    text-transform: uppercase;
    opacity: 0.75;
}

/* ===================== ARENA ===================== */
.tm-arena {
    background: var(--px-ink);
    color: var(--px-paper);
    border: 3px solid var(--px-ink);
    box-shadow: 5px 5px 0 var(--px-ink);
    padding: 1.5rem;
    max-width: 980px;
    margin: 0 auto;
    position: relative;
    image-rendering: pixelated;
}
.tm-arena::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(45deg, rgba(255,255,255,0.035) 25%, transparent 25%),
        linear-gradient(-45deg, rgba(255,255,255,0.035) 25%, transparent 25%),
        linear-gradient(45deg, transparent 75%, rgba(255,255,255,0.035) 75%),
        linear-gradient(-45deg, transparent 75%, rgba(255,255,255,0.035) 75%);
    background-size: 8px 8px;
    background-position: 0 0, 0 4px, 4px -4px, -4px 0;
    pointer-events: none;
}
.tm-arena > * { position: relative; z-index: 1; }

.tm-arena-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.85rem;
    padding-bottom: 1.1rem;
    margin-bottom: 1.1rem;
    border-bottom: 3px solid var(--px-paper);
}
.tm-arena-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-family: var(--px-sans);
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--px-paper);
}
.tm-arena-tag .live-dot {
    width: 8px; height: 8px;
    background: var(--px-paper);
    animation: pxBlink 1.2s steps(1) infinite;
}

/* Duration picker */
.tm-picker {
    display: inline-flex;
    gap: 0;
    border: 3px solid var(--px-paper);
    background: var(--px-ink);
}
.tm-picker .duration-btn {
    background: transparent;
    border: none;
    border-right: 2px solid var(--px-paper);
    color: var(--px-paper);
    font-family: var(--px-mono);
    font-size: 0.82rem;
    font-weight: 600;
    letter-spacing: 0.03em;
    padding: 0.35rem 0.7rem;
    cursor: pointer;
    transition: background 0.05s steps(1), color 0.05s steps(1);
    -webkit-appearance: none;
    appearance: none;
    min-width: 46px;
}
.tm-picker .duration-btn:last-child { border-right: none; }
.tm-picker .duration-btn:hover { background: rgba(255,255,255,0.1); }
.tm-picker .duration-btn.active {
    background: var(--px-paper);
    color: var(--px-ink);
}

/* HUD */
.tm-hud {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
    border: 3px solid var(--px-paper);
    margin-bottom: 1.1rem;
}
.tm-hud-cell {
    padding: 0.85rem 0.5rem;
    text-align: center;
    border-right: 2px solid var(--px-paper);
}
.tm-hud-cell:last-child { border-right: none; }
.tm-hud-label {
    font-family: var(--px-sans);
    font-size: 0.66rem;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--px-paper);
    opacity: 0.75;
    margin-bottom: 0.35rem;
}
/* Numbers → mono, clearly readable */
.tm-hud-value {
    font-family: var(--px-mono);
    font-size: 1.55rem;
    font-weight: 700;
    color: var(--px-paper);
    letter-spacing: -0.02em;
    line-height: 1.1;
}

/* Board */
.tm-board {
    background: var(--px-ink);
    border: 3px solid var(--px-paper);
    padding: 1.5rem 1.4rem;
    min-height: 190px;
    cursor: text;
    user-select: none;
    margin-bottom: 1rem;
    position: relative;
    transition: border-color 0.05s steps(1);
}
.tm-board.focused { border-color: var(--px-paper); box-shadow: inset 0 0 0 2px var(--px-paper); }
.tm-text {
    font-family: var(--px-mono);
    font-size: 1.22rem;
    line-height: 1.95;
    color: rgba(255,255,255,0.35);
    word-break: break-word;
    letter-spacing: 0.01em;
}
.tm-text .inline-char { transition: color 0.08s ease; position: relative; }
.tm-text .inline-char.correct-char { color: var(--px-paper); }
.tm-text .inline-char.error-char {
    background: var(--px-paper);
    color: var(--px-ink);
}
.tm-text .inline-char.current-char::before {
    content: "";
    position: absolute;
    left: -2px;
    top: 12%;
    bottom: 12%;
    width: 3px;
    background: var(--px-paper);
    animation: pxCaret 1s steps(2) infinite;
}
@keyframes pxCaret {
    0%, 49% { opacity: 1; }
    50%, 100% { opacity: 0; }
}

/* Overlay */
.tm-overlay {
    position: absolute;
    inset: 0;
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: var(--px-ink);
    padding: 1.25rem;
    text-align: center;
    z-index: 3;
    border: 3px solid var(--px-paper);
}
.tm-overlay.show { display: flex; }
.tm-overlay-badge {
    font-family: var(--px-sans);
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--px-paper);
    margin-bottom: 0.85rem;
    padding: 0.3rem 0.6rem;
    border: 2px solid var(--px-paper);
    animation: pxBlink 1.2s steps(1) infinite;
}
.tm-overlay h4 {
    font-family: var(--px-sans);
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--px-paper);
    margin: 0 0 0.85rem;
    line-height: 1.3;
    letter-spacing: -0.01em;
}
.tm-overlay h4 strong {
    font-family: var(--px-mono);
    font-weight: 700;
}
.tm-overlay p {
    font-family: var(--px-sans);
    font-size: 0.88rem;
    color: var(--px-paper);
    opacity: 0.75;
    margin: 0 0 1rem;
    max-width: 380px;
    line-height: 1.5;
}
.tm-overlay-btns {
    display: flex;
    gap: 0.6rem;
    flex-wrap: wrap;
    justify-content: center;
}
.tm-overlay .tm-btn {
    padding: 0.55rem 0.9rem;
    font-size: 0.78rem;
    border-color: var(--px-paper);
    box-shadow: 2px 2px 0 var(--px-paper);
    background: var(--px-ink);
    color: var(--px-paper);
}
.tm-overlay .tm-btn:hover { background: var(--px-ink); }
.tm-overlay .tm-btn:active {
    transform: translate(2px, 2px);
    box-shadow: 0 0 0 var(--px-paper);
}
.tm-overlay .tm-btn-dark {
    background: var(--px-paper);
    color: var(--px-ink);
    border-color: var(--px-paper);
    box-shadow: 2px 2px 0 var(--px-paper);
}

/* Board foot */
.tm-board-foot {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.6rem;
    font-family: var(--px-sans);
    font-size: 0.78rem;
    color: var(--px-paper);
    opacity: 0.85;
    font-weight: 500;
}
.tm-board-foot kbd {
    font-family: var(--px-mono);
    background: var(--px-paper);
    color: var(--px-ink);
    padding: 0.1rem 0.4rem;
    font-size: 0.72rem;
    font-weight: 700;
    border: 2px solid var(--px-paper);
}
.tm-restart {
    background: transparent;
    border: 2px solid var(--px-paper);
    color: var(--px-paper);
    padding: 0.35rem 0.65rem;
    font-family: var(--px-sans);
    font-size: 0.76rem;
    font-weight: 600;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    cursor: pointer;
    transition: background 0.05s steps(1), color 0.05s steps(1);
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    -webkit-appearance: none;
    appearance: none;
}
.tm-restart:hover { background: var(--px-paper); color: var(--px-ink); }
.tm-board-foot a {
    color: var(--px-paper);
    text-decoration: none;
    font-family: var(--px-sans);
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    border-bottom: 2px solid var(--px-paper);
    padding-bottom: 1px;
}

/* ===================== MARQUEE ===================== */
.tm-marquee {
    border-top: 3px solid var(--px-ink);
    border-bottom: 3px solid var(--px-ink);
    background: var(--px-ink);
    color: var(--px-paper);
    padding: 0.7rem 0;
    overflow: hidden;
    white-space: nowrap;
    position: relative;
}
.tm-marquee-track {
    display: inline-block;
    animation: tmScroll 40s linear infinite;
    font-family: var(--px-sans);
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.18em;
    text-transform: uppercase;
}
.tm-marquee-track span { display: inline-block; margin: 0 1.5rem; }
.tm-marquee-track span::before {
    content: "■";
    margin-right: 1.5rem;
    font-size: 0.6em;
    vertical-align: middle;
}
@keyframes tmScroll {
    from { transform: translateX(0); }
    to { transform: translateX(-50%); }
}

/* ===================== SECTIONS ===================== */
.tm-section {
    padding: 4.5rem 0;
    border-bottom: 4px solid var(--px-ink);
}
.tm-section-head {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 3rem;
    align-items: start;
    margin-bottom: 3rem;
    padding-bottom: 1.75rem;
    border-bottom: 3px solid var(--px-ink);
}
.tm-section-label {
    font-family: var(--px-sans);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--px-ink);
    margin-bottom: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.55rem;
}
.tm-section-label::before {
    content: "";
    width: 14px;
    height: 14px;
    background: var(--px-ink);
    display: inline-block;
}
/* Section h2 — SANS-SERIF */
.tm-section-head h2 {
    font-family: var(--px-sans);
    font-size: clamp(1.6rem, 3.4vw, 2.5rem);
    font-weight: 800;
    letter-spacing: -0.02em;
    line-height: 1.15;
    margin: 0;
    color: var(--px-ink);
}
.tm-section-head h2 em {
    font-style: normal;
    text-decoration: underline;
    text-decoration-thickness: 4px;
    text-underline-offset: 4px;
}
.tm-section-head p {
    font-family: var(--px-sans);
    font-size: 0.96rem;
    line-height: 1.7;
    color: var(--px-ink);
    margin: 0;
    max-width: 500px;
    padding-top: 0.35rem;
    opacity: 0.85;
}

/* ===================== FEATURES ===================== */
.tm-features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    border: 3px solid var(--px-ink);
    box-shadow: var(--px-shadow) var(--px-shadow) 0 var(--px-ink);
    background: var(--px-paper);
}
.tm-feature {
    padding: 2rem 1.75rem;
    border-right: 3px solid var(--px-ink);
    position: relative;
    background: var(--px-paper);
    transition: background 0.05s steps(1);
}
.tm-feature:last-child { border-right: none; }
.tm-feature:hover { background: var(--px-paper-2); }
.tm-feature-num {
    font-family: var(--px-mono);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: var(--px-ink);
    margin-bottom: 1.1rem;
    display: block;
    text-transform: uppercase;
    opacity: 0.7;
}
.tm-feature-icon {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--px-ink);
    color: var(--px-paper);
    font-size: 1rem;
    margin-bottom: 1.1rem;
}
/* Feature h3 — sans-serif */
.tm-feature h3 {
    font-family: var(--px-sans);
    font-size: 1.1rem;
    font-weight: 800;
    letter-spacing: -0.01em;
    color: var(--px-ink);
    margin: 0 0 0.7rem;
    line-height: 1.3;
}
.tm-feature p {
    font-family: var(--px-sans);
    font-size: 0.9rem;
    line-height: 1.65;
    color: var(--px-ink);
    margin: 0;
    opacity: 0.8;
}

/* ===================== ARCADE GRID ===================== */
.tm-arcade-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    border: 3px solid var(--px-ink);
    box-shadow: var(--px-shadow) var(--px-shadow) 0 var(--px-ink);
    background: var(--px-paper);
}
@media (max-width: 991px) {
    .tm-arcade-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .tm-arcade-grid { grid-template-columns: 1fr; }
}
.tm-arcade-card {
    padding: 1.85rem 1.6rem;
    border-right: 3px solid var(--px-ink);
    border-bottom: 3px solid var(--px-ink);
    position: relative;
    background: var(--px-paper);
    transition: background 0.05s steps(1);
    display: flex;
    flex-direction: column;
}
.tm-arcade-card:nth-child(3n) { border-right: none; }
.tm-arcade-card:last-child,
.tm-arcade-card:nth-last-child(-n+3) { border-bottom: none; }
.tm-arcade-card:hover { background: var(--px-paper-2); }
.tm-arcade-badge {
    font-family: var(--px-sans);
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    display: inline-block;
    margin-bottom: 0.85rem;
    color: var(--px-ink);
    border: 2px solid var(--px-ink);
    padding: 0.18rem 0.5rem;
    background: var(--px-paper);
    align-self: flex-start;
}
.tm-arcade-icon {
    width: 46px;
    height: 46px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid var(--px-ink);
    font-size: 1.2rem;
    margin-bottom: 1.1rem;
    background: var(--px-ink);
    color: var(--px-paper);
}
/* Arcade h3 — sans-serif */
.tm-arcade-card h3 {
    font-family: var(--px-sans);
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--px-ink);
    margin: 0 0 0.65rem;
    line-height: 1.3;
    letter-spacing: -0.01em;
}
.tm-arcade-card p {
    font-family: var(--px-sans);
    font-size: 0.86rem;
    line-height: 1.6;
    color: var(--px-ink);
    margin: 0 0 1.25rem;
    flex-grow: 1;
    opacity: 0.85;
}
.tm-arcade-foot {
    margin-top: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    padding-top: 1rem;
    border-top: 2px dashed var(--px-ink);
}
.tm-arcade-foot .small {
    font-family: var(--px-mono);
    font-size: 0.68rem;
    letter-spacing: 0.02em;
    color: var(--px-ink);
    opacity: 0.7;
    font-weight: 600;
}
.tm-arcade-foot .tm-btn {
    padding: 0.4rem 0.65rem;
    font-size: 0.72rem;
    box-shadow: 2px 2px 0 var(--px-ink);
}
.tm-arcade-foot .tm-btn:active {
    transform: translate(2px, 2px);
    box-shadow: 0 0 0 var(--px-ink);
}

/* ===================== STEPS ===================== */
.tm-steps {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}
.tm-step {
    padding: 1.5rem 1.35rem;
    border: 3px solid var(--px-ink);
    box-shadow: var(--px-shadow) var(--px-shadow) 0 var(--px-ink);
    background: var(--px-paper);
    position: relative;
}
.tm-step-num {
    font-family: var(--px-mono);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--px-ink);
    margin-bottom: 0.85rem;
    border-bottom: 2px solid var(--px-ink);
    padding-bottom: 0.5rem;
    display: block;
    opacity: 0.7;
}
/* Step h3 — sans-serif */
.tm-step h3 {
    font-family: var(--px-sans);
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--px-ink);
    margin: 0 0 0.65rem;
    line-height: 1.3;
    letter-spacing: -0.01em;
}
.tm-step p {
    font-family: var(--px-sans);
    font-size: 0.88rem;
    line-height: 1.6;
    color: var(--px-ink);
    margin: 0;
    opacity: 0.85;
}

/* ===================== QUOTE ===================== */
.tm-quote {
    padding: 4rem 0;
    border-bottom: 4px solid var(--px-ink);
    text-align: center;
    background: var(--px-ink);
    color: var(--px-paper);
}
.tm-quote blockquote {
    font-family: var(--px-sans);
    font-size: clamp(1.25rem, 2.6vw, 1.85rem);
    font-weight: 500;
    line-height: 1.4;
    letter-spacing: -0.01em;
    color: var(--px-paper);
    max-width: 820px;
    margin: 0 auto 1.5rem;
    padding: 0 1rem;
}
.tm-quote blockquote em {
    font-style: normal;
    border-bottom: 3px solid var(--px-paper);
    padding-bottom: 1px;
    font-weight: 700;
}
.tm-quote cite {
    font-family: var(--px-sans);
    font-size: 0.74rem;
    font-weight: 600;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--px-paper);
    font-style: normal;
    opacity: 0.8;
    display: inline-block;
    padding: 0.3rem 0.8rem;
    border: 2px solid var(--px-paper);
}

/* ===================== CTA ===================== */
.tm-cta {
    padding: 4.5rem 0;
    text-align: center;
    position: relative;
    overflow: hidden;
    background: var(--px-paper-2);
    border-bottom: 4px solid var(--px-ink);
}
/* CTA h2 — sans-serif */
.tm-cta h2 {
    font-family: var(--px-sans);
    font-size: clamp(1.8rem, 4.5vw, 3rem);
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1.1;
    margin: 0 0 1.25rem;
    color: var(--px-ink);
    max-width: 780px;
    margin-left: auto;
    margin-right: auto;
}
.tm-cta h2 em {
    font-style: normal;
    text-decoration: underline;
    text-decoration-thickness: 5px;
    text-underline-offset: 6px;
}
.tm-cta p {
    font-family: var(--px-sans);
    font-size: 0.98rem;
    line-height: 1.65;
    color: var(--px-ink);
    max-width: 520px;
    margin: 0 auto 2rem;
    opacity: 0.85;
}
.tm-cta-btns {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 0.85rem;
}
.tm-cta-meta {
    margin-top: 2.75rem;
    padding-top: 1.75rem;
    border-top: 3px solid var(--px-ink);
    display: flex;
    justify-content: center;
    gap: 2.5rem;
    flex-wrap: wrap;
}
.tm-cta-meta .tm-proof-item {
    text-align: center;
    flex: 0 0 auto;
    border: none;
    padding: 0;
}

/* ===================== FOOTNOTE ===================== */
.tm-footnote {
    text-align: center;
    font-family: var(--px-sans);
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--px-ink);
    padding: 1.5rem 0 2.25rem;
    opacity: 0.7;
}
.tm-footnote span { margin: 0 0.4rem; }

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 900px) {
    .tm-hero { padding: 2.25rem 0 2.75rem; }
    .tm-hero-intro { margin-bottom: 2rem; }
    .tm-hero h1 { font-size: clamp(1.75rem, 6.5vw, 2.8rem); }
    .tm-section { padding: 3.25rem 0; }
    .tm-section-head {
        grid-template-columns: 1fr;
        gap: 1.25rem;
        margin-bottom: 2.25rem;
        padding-bottom: 1.25rem;
    }
    .tm-features { grid-template-columns: 1fr; }
    .tm-feature { border-right: none; border-bottom: 3px solid var(--px-ink); }
    .tm-feature:last-child { border-bottom: none; }
    .tm-steps { grid-template-columns: 1fr; gap: 1.25rem; }
    .tm-cta { padding: 3.25rem 0; }
    .tm-quote { padding: 3.25rem 0; }
}

@media (max-width: 640px) {
    .tm-hero { padding: 1.75rem 0 2.25rem; }
    .tm-eyebrow { font-size: 0.66rem; padding: 0.35rem 0.7rem; }
    .tm-hero h1 { font-size: clamp(1.6rem, 8vw, 2.4rem); line-height: 1.15; }
    .tm-hero-lead { font-size: 0.94rem; }
    .tm-hero-actions { flex-direction: column; width: 100%; }
    .tm-hero-actions .tm-btn { width: 100%; }
    .tm-hero-proof { flex-direction: column; margin-bottom: 2rem; }
    .tm-proof-item {
        border-right: none;
        border-bottom: 3px solid var(--px-ink);
        padding: 0.85rem 1rem;
    }
    .tm-proof-item:last-child { border-bottom: none; }

    .tm-arena { padding: 1.1rem; }
    .tm-arena-head { padding-bottom: 0.9rem; margin-bottom: 0.9rem; gap: 0.6rem; }
    .tm-arena-tag { font-size: 0.7rem; }
    .tm-picker .duration-btn { padding: 0.3rem 0.55rem; font-size: 0.74rem; min-width: 42px; }

    .tm-hud { grid-template-columns: repeat(2, 1fr); }
    .tm-hud-cell { padding: 0.65rem 0.4rem; border-bottom: 2px solid var(--px-paper); }
    .tm-hud-cell:nth-child(2) { border-right: none; }
    .tm-hud-cell:nth-last-child(-n+2) { border-bottom: none; }
    .tm-hud-label { font-size: 0.58rem; }
    .tm-hud-value { font-size: 1.25rem; }

    .tm-board { padding: 1rem 0.85rem; min-height: 160px; }
    .tm-text { font-size: 1rem; line-height: 1.85; }

    .tm-overlay h4 { font-size: 1rem; }
    .tm-overlay p { font-size: 0.82rem; }
    .tm-overlay-btns { flex-direction: column; width: 100%; }
    .tm-overlay-btns .tm-btn { width: 100%; }

    .tm-board-foot { font-size: 0.72rem; }
    .tm-board-foot .d-none.d-sm-inline { display: none !important; }

    .tm-feature { padding: 1.5rem 1.25rem; }
    .tm-arcade-card { padding: 1.5rem 1.25rem; }
    .tm-step h3 { font-size: 1rem; }
    .tm-cta h2 { font-size: clamp(1.5rem, 7vw, 2.2rem); }
    .tm-cta p { font-size: 0.94rem; }
    .tm-cta-btns { flex-direction: column; width: 100%; }
    .tm-cta-btns .tm-btn { width: 100%; }
    .tm-cta-meta { gap: 1.5rem; }
    .tm-cta-meta .tm-proof-item .v { font-size: 0.95rem; }
    .tm-cta-meta .tm-proof-item .l { font-size: 0.66rem; }
}

@media (max-width: 380px) {
    .tm-hero h1 { font-size: 1.4rem; }
    .tm-hud-value { font-size: 1.1rem; }
    .tm-text { font-size: 0.92rem; }
    .tm-logo-name { font-size: 0.48rem; }
}
</style>

<div class="tm-landing">

   

  <!-- ============ HERO ============ -->
  <section class="tm-hero">
    <div class="tm-wrap">

      <div class="tm-hero-intro">
        <div class="tm-eyebrow">
          <span class="dot"></span> Typing Performance Studio
        </div>

        <h1>
          Master your keystrokes.<br>
          <em>Elevate your speed.</em>
        </h1>

        <p class="tm-hero-lead">
          Real-time keystroke diagnostics, scientific Gross &amp; Net WPM formulas, distraction-free practice arenas, and in-depth speed trajectories — all in one clean workspace.
        </p>

        <div class="tm-hero-actions">
          <a href="<?= BASE_URL ?>/register.php" class="tm-btn tm-btn-dark tm-btn-lg">
            <i class="bi bi-rocket-takeoff-fill"></i> Create Free Account
          </a>
          <a href="<?= BASE_URL ?>/practice.php" class="tm-btn tm-btn-outline tm-btn-lg">
            <i class="bi bi-play-circle-fill"></i> Full Arena
          </a>
        </div>
      </div>

      <div class="tm-hero-proof">
        <div class="tm-proof-item">
          <div class="v">&lt; 5ms</div>
          <div class="l">Input latency</div>
        </div>
        <div class="tm-proof-item">
          <div class="v">Gross / Net</div>
          <div class="l">Scientific formulas</div>
        </div>
        <div class="tm-proof-item">
          <div class="v">100% Free</div>
          <div class="l">No paywalls</div>
        </div>
      </div>

      <!-- ===== ARENA ===== -->
      <div class="tm-arena" id="inlineArenaCard">

        <div class="tm-arena-head">
          <span class="tm-arena-tag">
            <span class="live-dot"></span> Live Speed Test
          </span>
          <div class="tm-picker" id="inlineDurationPicker" role="group" aria-label="Test duration">
            <button type="button" class="duration-btn active" data-duration="15" aria-pressed="true">15s</button>
            <button type="button" class="duration-btn" data-duration="30" aria-pressed="false">30s</button>
            <button type="button" class="duration-btn" data-duration="60" aria-pressed="false">60s</button>
          </div>
        </div>

        <div class="tm-hud">
          <div class="tm-hud-cell">
            <div class="tm-hud-label">WPM</div>
            <div class="tm-hud-value" id="inlineWpm">0</div>
          </div>
          <div class="tm-hud-cell">
            <div class="tm-hud-label">Net WPM</div>
            <div class="tm-hud-value" id="inlineNetWpm">0</div>
          </div>
          <div class="tm-hud-cell">
            <div class="tm-hud-label">Accuracy</div>
            <div class="tm-hud-value" id="inlineAcc">100%</div>
          </div>
          <div class="tm-hud-cell">
            <div class="tm-hud-label">Time</div>
            <div class="tm-hud-value" id="inlineTimer">15s</div>
          </div>
        </div>

        <div class="tm-board" id="inlineBoardWrapper" role="textbox" aria-label="Typing area" tabindex="-1">
          <input
            type="text"
            id="inlineHiddenInput"
            autocomplete="off"
            autocapitalize="off"
            autocorrect="off"
            spellcheck="false"
            inputmode="text"
            aria-hidden="true"
            tabindex="0"
            style="position:absolute;opacity:0;left:-9999px;top:-9999px;pointer-events:none;"
          >
          <div class="tm-text" id="inlineTextDisplay"></div>

          <div class="tm-overlay" id="inlineFinishedOverlay">
            <span class="tm-overlay-badge">Test Complete</span>
            <h4>
              <strong id="overlayNetWpm">0</strong> WPM &bull; <strong id="overlayAcc">100</strong>% accuracy
            </h4>
            <p>Create a free account to save your scores, track progress, and compete with other typists.</p>
            <div class="tm-overlay-btns">
              <a href="<?= BASE_URL ?>/register.php" class="tm-btn tm-btn-dark">Save Score / Register</a>
              <button type="button" class="tm-btn tm-btn-outline" id="inlineRetryBtn">Retry</button>
            </div>
          </div>
        </div>

        <div class="tm-board-foot">
          <div style="display:flex;align-items:center;gap:0.5rem;">
            <button type="button" class="tm-restart" id="inlineResetBtn">
              <i class="bi bi-arrow-clockwise"></i> Restart
            </button>
            <span class="d-none d-sm-inline">Press <kbd>Tab</kbd> + <kbd>Enter</kbd></span>
          </div>
          <a href="<?= BASE_URL ?>/practice.php">
            Full Arena <i class="bi bi-arrow-right"></i>
          </a>
        </div>

      </div>
    </div>
  </section>

  <!-- ============ MARQUEE ============ -->
  <div class="tm-marquee">
    <div class="tm-marquee-track">
      <span>Precision</span><span>Speed</span><span>Accuracy</span><span>Analytics</span><span>Focus</span><span>Discipline</span><span>Growth</span><span>Muscle memory</span>
      <span>Precision</span><span>Speed</span><span>Accuracy</span><span>Analytics</span><span>Focus</span><span>Discipline</span><span>Growth</span><span>Muscle memory</span>
    </div>
  </div>

  <!-- ============ FEATURES ============ -->
  <section class="tm-section" id="features">
    <div class="tm-wrap">
      <div class="tm-section-head">
        <div>
          <div class="tm-section-label">Features</div>
          <h2>Built for typists who care<br><em>about the details.</em></h2>
        </div>
        <p>No clutter. No paywalls. Just the metrics that actually move your WPM and accuracy forward — packaged in a workspace that stays out of your way.</p>
      </div>

      <div class="tm-features">
        <div class="tm-feature">
          <span class="tm-feature-num">01 / Accuracy</span>
          <div class="tm-feature-icon"><i class="bi bi-calculator-fill"></i></div>
          <h3>Scientific WPM Math</h3>
          <p>Gross WPM divides typed characters by standardized 5-character word lengths. Net WPM penalizes uncorrected errors per elapsed minute for true competitive accuracy.</p>
        </div>

        <div class="tm-feature">
          <span class="tm-feature-num">02 / Insight</span>
          <div class="tm-feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
          <h3>Live Analytics &amp; Trends</h3>
          <p>Track your speed trajectory over time with Chart.js visualizations, discover weak keys, review accuracy percentages, and export session logs to CSV and JSON.</p>
        </div>

        <div class="tm-feature">
          <span class="tm-feature-num">03 / Control</span>
          <div class="tm-feature-icon"><i class="bi bi-sliders2-vertical"></i></div>
          <h3>Full UI Customization</h3>
          <p>Choose between Soft UI White, Signature Obsidian, Cyberpunk, and Nord Arctic. Adjust font sizing, caret styles (line, block, underline), and auditory feedback cues.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ ARCADE TYPING ZONE ============ -->
  <section class="tm-section" id="arcade" style="background: var(--px-paper-2);">
    <div class="tm-wrap">
      <div class="tm-section-head">
        <div>
          <div class="tm-section-label"><i class="bi bi-controller"></i> Arcade Zone</div>
          <h2>Level up reflexes with<br><em>5 arcade typing games.</em></h2>
        </div>
        <div>
          <p>Ditch monotonous drills. Master subconscious finger coordination, involuntary reflex speed, and peripheral letter recognition through 5 complete 60 FPS HTML5 Canvas games with Web Audio sound effects and cloud high-score leaderboards.</p>
          <div class="d-flex align-items-center gap-2 mt-3">
            <a href="<?= BASE_URL ?>/games.php" class="tm-btn tm-btn-dark tm-btn-sm">
              <i class="bi bi-controller"></i> Open Arcade Hub
            </a>
          </div>
        </div>
      </div>

      <div class="tm-arcade-grid">

        <div class="tm-arcade-card">
          <span class="tm-arcade-badge">★ Most Popular</span>
          <div class="tm-arcade-icon"><i class="bi bi-slash-circle-fill"></i></div>
          <h3>Keyboard Ninja</h3>
          <p>Slash flying fruit targets mid-air with sharp katana strikes. Isolate Home Row, Top Row, or Numbers while dodging explosive ninja bombs!</p>
          <div class="tm-arcade-foot">
            <span class="small">Single-Key / Rows</span>
            <a href="<?= BASE_URL ?>/game-ninja.php" class="tm-btn tm-btn-dark tm-btn-sm">Play &rarr;</a>
          </div>
        </div>

        <div class="tm-arcade-card">
          <span class="tm-arcade-badge">★ Arcade Shooter</span>
          <div class="tm-arcade-icon"><i class="bi bi-rocket-takeoff-fill"></i></div>
          <h3>Space Word Defender</h3>
          <p>Defend your orbital planetary base from invading alien warships. Type complete words to fire laser cannons and save your energy shields!</p>
          <div class="tm-arcade-foot">
            <span class="small">Word Velocity</span>
            <a href="<?= BASE_URL ?>/game-space.php" class="tm-btn tm-btn-dark tm-btn-sm">Play &rarr;</a>
          </div>
        </div>

        <div class="tm-arcade-card">
          <span class="tm-arcade-badge">★ Nitro Racer</span>
          <div class="tm-arcade-icon"><i class="bi bi-speedometer2"></i></div>
          <h3>Cyber Grand Prix</h3>
          <p>Accelerate your supercar past 150 MPH! Accurately type words to ignite nitro exhaust flames, shift gears, and overtake rivals on an endless neon highway.</p>
          <div class="tm-arcade-foot">
            <span class="small">Highway Racing</span>
            <a href="<?= BASE_URL ?>/game-racer.php" class="tm-btn tm-btn-dark tm-btn-sm">Play &rarr;</a>
          </div>
        </div>

        <div class="tm-arcade-card">
          <span class="tm-arcade-badge">★ Sky Popper</span>
          <div class="tm-arcade-icon"><i class="bi bi-balloon-fill"></i></div>
          <h3>Type-a-Balloon</h3>
          <p>Float into the clouds! Pop colorful rising balloons with sharp darts before they drift into the stratosphere. Perfect for finger accuracy and letter recognition.</p>
          <div class="tm-arcade-foot">
            <span class="small">Sky Popping</span>
            <a href="<?= BASE_URL ?>/game-balloon.php" class="tm-btn tm-btn-dark tm-btn-sm">Play &rarr;</a>
          </div>
        </div>

        <div class="tm-arcade-card" style="grid-column: span 2;">
          <span class="tm-arcade-badge">★ Planetary Railgun</span>
          <div class="tm-arcade-icon"><i class="bi bi-fire"></i></div>
          <h3>Meteor Matrix Defense</h3>
          <p>A catastrophic meteor storm is threatening the central city grid. Fire dual railgun plasma strikes and trigger blue EMP shockwaves to vaporize meteors into cosmic dust!</p>
          <div class="tm-arcade-foot">
            <span class="small">Railguns &bull; EMP &bull; Grid Integrity</span>
            <a href="<?= BASE_URL ?>/game-meteor.php" class="tm-btn tm-btn-dark tm-btn-sm">Play &rarr;</a>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ============ HOW IT WORKS ============ -->
  <section class="tm-section" id="how">
    <div class="tm-wrap">
      <div class="tm-section-head">
        <div>
          <div class="tm-section-label">Process</div>
          <h2>Three steps from<br><em>first keystroke to growth.</em></h2>
        </div>
        <p>TypeMaster is intentionally simple. Pick a duration, type, and watch your numbers update live. Everything else is detail.</p>
      </div>

      <div class="tm-steps">
        <div class="tm-step">
          <div class="tm-step-num">01 — Practice</div>
          <h3>Start a session</h3>
          <p>Pick a duration — 15, 30, or 60 seconds. The timer starts the moment you type your first character.</p>
        </div>
        <div class="tm-step">
          <div class="tm-step-num">02 — Measure</div>
          <h3>Track live metrics</h3>
          <p>Watch Gross WPM, Net WPM, and accuracy update in real time as you type — no page reloads, no lag.</p>
        </div>
        <div class="tm-step">
          <div class="tm-step-num">03 — Improve</div>
          <h3>Review &amp; repeat</h3>
          <p>Register to save results, view your speed trajectory, and see exactly where accuracy is limiting your growth.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ QUOTE ============ -->
  <section class="tm-quote">
    <div class="tm-wrap">
      <blockquote>
        &ldquo;The keyboard is the primary tool of the modern digital creator. <em>Precision leads to mastery.</em>&rdquo;
      </blockquote>
      <cite>— TypeMaster Manifesto</cite>
    </div>
  </section>

  <!-- ============ FINAL CTA ============ -->
  <section class="tm-cta">
    <div class="tm-wrap">
      <h2>Start your typing<br><em>journey today.</em></h2>
      <p>Join developers, students, and competitive typists who practice daily with TypeMaster PRO.</p>
      <div class="tm-cta-btns">
        <a href="<?= BASE_URL ?>/register.php" class="tm-btn tm-btn-dark tm-btn-lg">
          <i class="bi bi-person-plus-fill"></i> Register Free Account
        </a>
        <a href="<?= BASE_URL ?>/practice.php" class="tm-btn tm-btn-outline tm-btn-lg">
          <i class="bi bi-keyboard"></i> Practice as Guest
        </a>
      </div>

      <div class="tm-cta-meta">
        <div class="tm-proof-item">
          <div class="v">100% Free</div>
          <div class="l">No credit card</div>
        </div>
        <div class="tm-proof-item">
          <div class="v">&lt; 5ms</div>
          <div class="l">Input lag</div>
        </div>
        <div class="tm-proof-item">
          <div class="v">Zero ads</div>
          <div class="l">Clean workspace</div>
        </div>
      </div>
    </div>
  </section>

  <div class="tm-footnote">
    TypeMaster Pro <span>■</span> Version 2.0 <span>■</span> Built for typists
  </div>

</div>

<!-- ============================================================
     INLINE SPEED ENGINE — unchanged, fully working
     ============================================================ -->
<script>
(function() {
  'use strict';

  var passages = [
    "Programming is the art of expressing algorithms in languages computers can execute reliably.",
    "Mastering touch typing increases coding productivity, frees cognitive load, and builds muscle memory.",
    "Web applications combine front-end reactivity with robust backend database systems for extreme scale.",
    "The keyboard is the primary tool of the modern digital creator; precision leads to true mastery."
  ];

  var currentPassage = passages[0];
  var duration       = 15;
  var timeLeft       = duration;
  var timerInterval  = null;
  var hasStarted     = false;
  var isFinished     = false;
  var charIndex      = 0;
  var correctKeystrokes = 0;
  var errorKeystrokes   = 0;
  var startTime         = null;

  var board        = document.getElementById('inlineBoardWrapper');
  var hiddenInput  = document.getElementById('inlineHiddenInput');
  var textDisplay  = document.getElementById('inlineTextDisplay');
  var wpmEl        = document.getElementById('inlineWpm');
  var netWpmEl     = document.getElementById('inlineNetWpm');
  var accEl        = document.getElementById('inlineAcc');
  var timerEl      = document.getElementById('inlineTimer');
  var overlay      = document.getElementById('inlineFinishedOverlay');
  var overlayNetWpm= document.getElementById('overlayNetWpm');
  var overlayAcc   = document.getElementById('overlayAcc');
  var resetBtn     = document.getElementById('inlineResetBtn');
  var retryBtn     = document.getElementById('inlineRetryBtn');
  var durationBtns = document.querySelectorAll('#inlineDurationPicker .duration-btn');

  if (!board || !hiddenInput || !textDisplay) return;

  function initPassage() {
    currentPassage = passages[Math.floor(Math.random() * passages.length)];
    textDisplay.innerHTML = '';
    var frag = document.createDocumentFragment();
    for (var i = 0; i < currentPassage.length; i++) {
      var span = document.createElement('span');
      span.textContent = currentPassage[i];
      span.className = i === 0 ? 'inline-char current-char' : 'inline-char';
      frag.appendChild(span);
    }
    textDisplay.appendChild(frag);
  }

  function resetTest() {
    if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
    hasStarted = false;
    isFinished = false;
    charIndex  = 0;
    correctKeystrokes = 0;
    errorKeystrokes   = 0;
    startTime = null;
    timeLeft  = duration;

    wpmEl.textContent    = '0';
    netWpmEl.textContent = '0';
    accEl.textContent    = '100%';
    timerEl.textContent  = duration + 's';
    overlay.classList.remove('show');
    hiddenInput.value = '';

    initPassage();
    board.classList.remove('focused');
  }

  function startTimer() {
    hasStarted = true;
    startTime  = Date.now();
    if (timerInterval) clearInterval(timerInterval);
    timerInterval = setInterval(function() {
      timeLeft--;
      timerEl.textContent = timeLeft + 's';
      updateMetrics();
      if (timeLeft <= 0) finishTest();
    }, 1000);
  }

  function updateMetrics() {
    if (!startTime) return;
    var elapsedMinutes = (Date.now() - startTime) / 60000;
    if (elapsedMinutes <= 0.01) return;

    var totalTyped = correctKeystrokes + errorKeystrokes;
    var grossWpm   = Math.round((totalTyped / 5) / elapsedMinutes);
    var netWpm     = Math.max(0, Math.round(grossWpm - (errorKeystrokes / elapsedMinutes)));
    var acc        = totalTyped > 0 ? Math.round((correctKeystrokes / totalTyped) * 100) : 100;

    wpmEl.textContent    = grossWpm;
    netWpmEl.textContent = netWpm;
    accEl.textContent    = acc + '%';
  }

  function finishTest() {
    if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
    isFinished = true;
    updateMetrics();

    overlayNetWpm.textContent = netWpmEl.textContent;
    overlayAcc.textContent    = accEl.textContent.replace('%', '');
    overlay.classList.add('show');
  }

  function handleKey(e) {
    if (isFinished) return;
    if (e.key === 'Tab') return;

    if (!hasStarted && e.key.length === 1 && !e.ctrlKey && !e.metaKey && !e.altKey) {
      startTimer();
    }

    if (e.key === 'Backspace') {
      if (charIndex > 0) {
        charIndex--;
        var spans = textDisplay.querySelectorAll('.inline-char');
        if (spans[charIndex]) {
          spans[charIndex].className = 'inline-char current-char';
        }
        if (spans[charIndex + 1]) {
          spans[charIndex + 1].className = 'inline-char';
        }
      }
      return;
    }

    if (e.key.length === 1 && !e.ctrlKey && !e.metaKey && !e.altKey) {
      if (charIndex >= currentPassage.length) return;

      var expected = currentPassage[charIndex];
      var spans    = textDisplay.querySelectorAll('.inline-char');

      if (e.key === expected) {
        spans[charIndex].className = 'inline-char correct-char';
        correctKeystrokes++;
      } else {
        spans[charIndex].className = 'inline-char error-char';
        errorKeystrokes++;
      }

      charIndex++;

      if (charIndex < currentPassage.length) {
        spans[charIndex].classList.add('current-char');
      } else {
        finishTest();
      }
      updateMetrics();
    }
  }

  hiddenInput.addEventListener('keydown', handleKey);

  function focusBoard() {
    hiddenInput.focus({ preventScroll: true });
    board.classList.add('focused');
  }

  board.addEventListener('mousedown', function(e) {
    e.preventDefault();
    focusBoard();
  });
  board.addEventListener('touchstart', function(e) {
    focusBoard();
  }, { passive: true });
  board.addEventListener('click', focusBoard);

  document.addEventListener('click', function(e) {
    if (!board.contains(e.target) && e.target !== hiddenInput) {
      board.classList.remove('focused');
      if (document.activeElement === hiddenInput) hiddenInput.blur();
    }
  });

  durationBtns.forEach(function(btn) {
    btn.addEventListener('click', function() {
      durationBtns.forEach(function(b) {
        b.classList.remove('active');
        b.setAttribute('aria-pressed', 'false');
      });
      btn.classList.add('active');
      btn.setAttribute('aria-pressed', 'true');
      duration = parseInt(btn.dataset.duration, 10) || 15;
      resetTest();
      focusBoard();
    });
  });

  resetBtn.addEventListener('click', function() { resetTest(); focusBoard(); });
  retryBtn.addEventListener('click', function() { resetTest(); focusBoard(); });

  var tabPressed = false;
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Tab') tabPressed = true;
    if (e.key === 'Enter' && tabPressed) {
      if (document.activeElement === hiddenInput || board.classList.contains('focused')) {
        e.preventDefault();
        resetTest();
        focusBoard();
      }
    }
  });
  document.addEventListener('keyup', function(e) {
    if (e.key === 'Tab') tabPressed = false;
  });

  initPassage();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>