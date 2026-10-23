<?php
/**
 * Custom Type Master Pro
 * Landing Page — Editorial / Premium B&W Edition (v3)
 * Fully responsive + fully working speed test
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$pageTitle = 'Master Your Typing Speed & Precision';
include __DIR__ . '/includes/header.php';
?>

<style>
/* ============================================================
   EDITORIAL B&W LANDING — TypeMaster PRO (v3)
   ============================================================ */
:root {
    --ink:        #0b0b0b;
    --ink-2:      #1a1a1a;
    --ink-3:      #262626;
    --paper:      #fafaf8;
    --paper-2:    #f2f1ec;
    --paper-3:    #e8e6df;
    --line:       #d8d6cd;
    --line-2:     #c4c1b6;
    --muted:      #6b6a63;
    --muted-2:    #94918a;
    --accent:     #ff4d00;
    --radius:     4px;
    --radius-lg:  10px;
    --font-sans:  "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    --font-serif: "Instrument Serif", "Playfair Display", Georgia, serif;
    --font-mono:  "JetBrains Mono", ui-monospace, "SF Mono", Menlo, Consolas, monospace;
}

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500;600;700&display=swap');

.tm-landing {
    background: var(--paper);
    color: var(--ink);
    font-family: var(--font-sans);
    overflow-x: hidden;
    position: relative;
    -webkit-font-smoothing: antialiased;
    -webkit-tap-highlight-color: transparent;
}
.tm-landing * { box-sizing: border-box; }

/* Paper grain */
.tm-landing::before {
    content: "";
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 1;
    opacity: 0.35;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/%3E%3CfeColorMatrix values='0 0 0 0 0.4 0 0 0 0 0.4 0 0 0 0 0.4 0 0 0 0.08 0'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
    mix-blend-mode: multiply;
}
.tm-landing > * { position: relative; z-index: 2; }

.tm-wrap {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 2rem;
}
@media (max-width: 640px) {
    .tm-wrap { padding: 0 1.15rem; }
}

/* ===================== TOP BAR ===================== */
.tm-topbar {
    border-bottom: 1px solid var(--line);
    padding: 1rem 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    font-size: 0.82rem;
    letter-spacing: 0.02em;
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
    font-weight: 800;
    font-size: 0.95rem;
    letter-spacing: -0.02em;
    color: var(--ink);
    text-decoration: none;
    flex-shrink: 0;
}
.tm-logo-mark {
    width: 26px;
    height: 26px;
    background: var(--ink);
    color: var(--paper);
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-mono);
    font-weight: 700;
    font-size: 0.8rem;
    flex-shrink: 0;
}
.tm-topbar-nav {
    display: flex;
    gap: 1.75rem;
}
.tm-topbar-nav a {
    color: var(--muted);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.15s ease;
}
.tm-topbar-nav a:hover { color: var(--ink); }
.tm-topbar-right {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
}
.tm-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.3rem 0.7rem;
    border: 1px solid var(--line-2);
    border-radius: 999px;
    font-family: var(--font-mono);
    font-size: 0.68rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--ink);
    white-space: nowrap;
}
.tm-pill .dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: var(--accent);
    animation: tmPulse 1.6s ease-in-out infinite;
}
@keyframes tmPulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}
@media (max-width: 900px) {
    .tm-topbar-nav { display: none; }
}
@media (max-width: 480px) {
    .tm-topbar { padding: 0.85rem 0; }
    .tm-pill { display: none; }
    .tm-btn-sm { padding: 0.5rem 0.85rem; font-size: 0.76rem; }
}

/* ===================== HERO ===================== */
.tm-hero {
    padding: 3.5rem 0 4rem;
    border-bottom: 1px solid var(--line);
}

.tm-hero-intro {
    max-width: 900px;
    margin: 0 auto 2.5rem;
    text-align: center;
}

.tm-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 1.5rem;
    padding: 0.4rem 0.85rem;
    border: 1px solid var(--line-2);
    border-radius: 999px;
    background: var(--paper-2);
}
.tm-eyebrow .dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: var(--accent);
    animation: tmPulse 1.6s ease-in-out infinite;
}

.tm-hero h1 {
    font-family: var(--font-serif);
    font-size: clamp(2.4rem, 6.5vw, 5rem);
    font-weight: 400;
    line-height: 1.02;
    letter-spacing: -0.035em;
    margin: 0 0 1.5rem;
    color: var(--ink);
}
.tm-hero h1 em {
    font-style: italic;
    color: var(--ink);
    position: relative;
    display: inline-block;
}
.tm-hero h1 em::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0.02em;
    height: 0.1em;
    background: var(--accent);
    border-radius: 1px;
}

.tm-hero-lead {
    font-size: clamp(0.98rem, 1.5vw, 1.12rem);
    line-height: 1.65;
    color: var(--muted);
    max-width: 620px;
    margin: 0 auto 2rem;
}

.tm-hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.65rem;
    justify-content: center;
    margin-bottom: 2.5rem;
}

.tm-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.8rem 1.3rem;
    font-size: 0.88rem;
    font-weight: 600;
    letter-spacing: 0.01em;
    border-radius: var(--radius);
    text-decoration: none;
    border: 1px solid var(--ink);
    cursor: pointer;
    transition: all 0.18s ease;
    font-family: inherit;
    white-space: nowrap;
    line-height: 1;
    -webkit-appearance: none;
    appearance: none;
    user-select: none;
}
.tm-btn-dark {
    background: var(--ink);
    color: var(--paper);
}
.tm-btn-dark:hover {
    background: transparent;
    color: var(--ink);
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.12);
}
.tm-btn-outline {
    background: transparent;
    color: var(--ink);
}
.tm-btn-outline:hover {
    background: var(--ink);
    color: var(--paper);
    transform: translateY(-1px);
}
.tm-btn-sm {
    padding: 0.55rem 1rem;
    font-size: 0.8rem;
}
.tm-btn-lg {
    padding: 0.95rem 1.65rem;
    font-size: 0.92rem;
}

/* Proof strip */
.tm-hero-proof {
    display: flex;
    justify-content: center;
    border-top: 1px solid var(--line);
    border-bottom: 1px solid var(--line);
    max-width: 780px;
    margin: 0 auto 2.75rem;
}
.tm-proof-item {
    flex: 1;
    padding: 1.1rem 1.25rem;
    text-align: center;
    border-right: 1px solid var(--line);
}
.tm-proof-item:last-child { border-right: none; }
.tm-proof-item .v {
    font-family: var(--font-mono);
    font-size: 1.3rem;
    font-weight: 700;
    letter-spacing: -0.03em;
    line-height: 1;
    margin-bottom: 0.35rem;
    color: var(--ink);
}
.tm-proof-item .l {
    font-size: 0.66rem;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: var(--muted);
    font-weight: 600;
}

/* ===================== ARENA ===================== */
.tm-arena {
    background: var(--ink);
    color: var(--paper);
    border-radius: var(--radius-lg);
    padding: 1.75rem;
    position: relative;
    overflow: hidden;
    max-width: 980px;
    margin: 0 auto;
    box-shadow:
        0 1px 0 rgba(255,255,255,0.06) inset,
        0 24px 60px rgba(11,11,11,0.25),
        0 4px 12px rgba(11,11,11,0.15);
}
.tm-arena::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 15% 0%, rgba(255,77,0,0.12) 0%, transparent 45%),
        radial-gradient(circle at 85% 100%, rgba(255,255,255,0.05) 0%, transparent 40%);
    pointer-events: none;
}

.tm-arena-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.85rem;
    padding-bottom: 1.25rem;
    margin-bottom: 1.25rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    position: relative;
}
.tm-arena-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-family: var(--font-mono);
    font-size: 0.68rem;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--paper);
    opacity: 0.9;
}
.tm-arena-tag .live-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--accent);
    box-shadow: 0 0 0 3px rgba(255,77,0,0.2);
    animation: tmPulse 1.6s ease-in-out infinite;
}

.tm-picker {
    display: inline-flex;
    gap: 2px;
    padding: 3px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 6px;
}
.tm-picker .duration-btn {
    background: transparent;
    border: none;
    color: rgba(250,250,248,0.55);
    font-family: var(--font-mono);
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.4rem 0.7rem;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.15s ease;
    letter-spacing: 0.04em;
    -webkit-appearance: none;
    appearance: none;
    min-width: 42px;
}
.tm-picker .duration-btn:hover { color: var(--paper); }
.tm-picker .duration-btn.active {
    background: var(--paper);
    color: var(--ink);
}

.tm-hud {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1px;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: var(--radius);
    overflow: hidden;
    margin-bottom: 1.1rem;
}
.tm-hud-cell {
    background: var(--ink-2);
    padding: 0.9rem 0.5rem;
    text-align: center;
}
.tm-hud-label {
    font-family: var(--font-mono);
    font-size: 0.6rem;
    font-weight: 600;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: rgba(250,250,248,0.5);
    margin-bottom: 0.35rem;
}
.tm-hud-value {
    font-family: var(--font-mono);
    font-size: 1.55rem;
    font-weight: 700;
    color: var(--paper);
    letter-spacing: -0.02em;
    line-height: 1;
}

.tm-board {
    background: var(--ink-2);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: var(--radius);
    padding: 1.6rem 1.5rem;
    min-height: 190px;
    cursor: text;
    user-select: none;
    margin-bottom: 1rem;
    position: relative;
    transition: border-color 0.2s ease;
}
.tm-board.focused { border-color: rgba(255,255,255,0.3); }
.tm-text {
    font-family: var(--font-mono);
    font-size: 1.22rem;
    line-height: 1.95;
    color: rgba(250,250,248,0.32);
    word-break: break-word;
    letter-spacing: 0.005em;
}
.tm-text .inline-char { transition: color 0.08s ease; position: relative; }
.tm-text .inline-char.correct-char { color: var(--paper); }
.tm-text .inline-char.error-char {
    color: var(--accent);
    text-decoration: underline wavy;
    text-underline-offset: 5px;
}
.tm-text .inline-char.current-char::before {
    content: "";
    position: absolute;
    left: -2px;
    top: 12%;
    bottom: 12%;
    width: 2px;
    background: var(--accent);
    animation: tmCaret 1s steps(2) infinite;
}
@keyframes tmCaret {
    0%, 49% { opacity: 1; }
    50%, 100% { opacity: 0; }
}

.tm-overlay {
    position: absolute;
    inset: 0;
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: rgba(11,11,11,0.94);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    border-radius: var(--radius);
    padding: 1.25rem;
    text-align: center;
    z-index: 3;
}
.tm-overlay.show { display: flex; }
.tm-overlay-badge {
    font-family: var(--font-mono);
    font-size: 0.62rem;
    font-weight: 600;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--accent);
    margin-bottom: 0.75rem;
}
.tm-overlay h4 {
    font-family: var(--font-serif);
    font-size: 1.6rem;
    font-weight: 400;
    color: var(--paper);
    margin: 0 0 0.4rem;
    letter-spacing: -0.02em;
    line-height: 1.15;
}
.tm-overlay h4 strong {
    font-family: var(--font-mono);
    font-style: normal;
    font-weight: 700;
}
.tm-overlay p {
    font-size: 0.78rem;
    color: rgba(250,250,248,0.55);
    margin: 0 0 1rem;
    max-width: 320px;
    line-height: 1.55;
}
.tm-overlay-btns {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    justify-content: center;
}
.tm-overlay .tm-btn {
    padding: 0.65rem 1.1rem;
    font-size: 0.82rem;
}
.tm-overlay .tm-btn-outline {
    border-color: rgba(255,255,255,0.25);
    color: var(--paper);
}
.tm-overlay .tm-btn-outline:hover {
    background: var(--paper);
    color: var(--ink);
    border-color: var(--paper);
}

.tm-board-foot {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.6rem;
    font-family: var(--font-mono);
    font-size: 0.7rem;
    color: rgba(250,250,248,0.5);
}
.tm-board-foot kbd {
    font-family: var(--font-mono);
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.12);
    padding: 0.1rem 0.4rem;
    border-radius: 3px;
    font-size: 0.65rem;
    color: var(--paper);
}
.tm-restart {
    background: transparent;
    border: 1px solid rgba(255,255,255,0.15);
    color: var(--paper);
    padding: 0.4rem 0.75rem;
    border-radius: 4px;
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    -webkit-appearance: none;
    appearance: none;
}
.tm-restart:hover {
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.3);
}
.tm-board-foot a {
    color: var(--paper);
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    transition: gap 0.15s ease;
}
.tm-board-foot a:hover { gap: 0.6rem; }

/* ===================== MARQUEE ===================== */
.tm-marquee {
    border-top: 1px solid var(--line);
    border-bottom: 1px solid var(--line);
    background: var(--paper-2);
    padding: 0.9rem 0;
    overflow: hidden;
    white-space: nowrap;
    position: relative;
}
.tm-marquee-track {
    display: inline-block;
    animation: tmScroll 40s linear infinite;
    font-family: var(--font-mono);
    font-size: 0.78rem;
    font-weight: 500;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--muted);
}
.tm-marquee-track span {
    display: inline-block;
    margin: 0 1.75rem;
}
.tm-marquee-track span::before {
    content: "◆";
    color: var(--accent);
    margin-right: 1.75rem;
    font-size: 0.6em;
    vertical-align: middle;
}
@keyframes tmScroll {
    from { transform: translateX(0); }
    to { transform: translateX(-50%); }
}

/* ===================== SECTIONS ===================== */
.tm-section {
    padding: 5rem 0;
    border-bottom: 1px solid var(--line);
}
.tm-section-head {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 3rem;
    align-items: start;
    margin-bottom: 3.25rem;
    padding-bottom: 1.75rem;
    border-bottom: 1px solid var(--line);
}
.tm-section-label {
    font-family: var(--font-mono);
    font-size: 0.68rem;
    font-weight: 600;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
}
.tm-section-label::before {
    content: "";
    width: 20px;
    height: 1px;
    background: var(--accent);
}
.tm-section-head h2 {
    font-family: var(--font-serif);
    font-size: clamp(1.75rem, 4vw, 2.6rem);
    font-weight: 400;
    letter-spacing: -0.02em;
    line-height: 1.1;
    margin: 0;
    color: var(--ink);
}
.tm-section-head h2 em { font-style: italic; }
.tm-section-head p {
    font-size: 0.98rem;
    line-height: 1.7;
    color: var(--muted);
    margin: 0;
    max-width: 480px;
    padding-top: 0.35rem;
}

/* ===================== FEATURES ===================== */
.tm-features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    border-top: 1px solid var(--line);
    border-left: 1px solid var(--line);
}
.tm-feature {
    padding: 2.25rem 1.85rem;
    border-right: 1px solid var(--line);
    border-bottom: 1px solid var(--line);
    position: relative;
    background: var(--paper);
    transition: background 0.2s ease;
}
.tm-feature:hover { background: var(--paper-2); }
.tm-feature-num {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 500;
    letter-spacing: 0.1em;
    color: var(--muted);
    margin-bottom: 1.25rem;
    display: block;
}
.tm-feature-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--ink);
    color: var(--paper);
    border-radius: 6px;
    font-size: 1rem;
    margin-bottom: 1.25rem;
    transition: background 0.2s ease;
}
.tm-feature:hover .tm-feature-icon {
    background: var(--accent);
}
.tm-feature h3 {
    font-family: var(--font-serif);
    font-size: 1.35rem;
    font-weight: 400;
    letter-spacing: -0.01em;
    color: var(--ink);
    margin: 0 0 0.6rem;
    line-height: 1.2;
}
.tm-feature p {
    font-size: 0.86rem;
    line-height: 1.65;
    color: var(--muted);
    margin: 0;
}

/* ===================== STEPS ===================== */
.tm-steps {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2.25rem;
}
.tm-step {
    padding-top: 1.35rem;
    border-top: 1px solid var(--ink);
    position: relative;
}
.tm-step::before {
    content: "";
    position: absolute;
    top: -3px;
    left: 0;
    width: 5px;
    height: 5px;
    background: var(--accent);
    border-radius: 50%;
}
.tm-step-num {
    font-family: var(--font-mono);
    font-size: 0.68rem;
    font-weight: 600;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 1.1rem;
}
.tm-step h3 {
    font-family: var(--font-serif);
    font-size: 1.4rem;
    font-weight: 400;
    color: var(--ink);
    margin: 0 0 0.65rem;
    letter-spacing: -0.01em;
}
.tm-step p {
    font-size: 0.86rem;
    line-height: 1.65;
    color: var(--muted);
    margin: 0;
}

/* ===================== QUOTE ===================== */
.tm-quote {
    padding: 4.5rem 0;
    border-bottom: 1px solid var(--line);
    text-align: center;
    background: var(--paper-2);
}
.tm-quote blockquote {
    font-family: var(--font-serif);
    font-size: clamp(1.35rem, 3vw, 2rem);
    font-weight: 400;
    line-height: 1.4;
    letter-spacing: -0.015em;
    color: var(--ink);
    max-width: 780px;
    margin: 0 auto 1.75rem;
    padding: 0 1rem;
}
.tm-quote blockquote em { font-style: italic; }
.tm-quote cite {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 500;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--muted);
    font-style: normal;
}

/* ===================== CTA ===================== */
.tm-cta {
    padding: 5rem 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.tm-cta h2 {
    font-family: var(--font-serif);
    font-size: clamp(2.25rem, 6vw, 4.25rem);
    font-weight: 400;
    letter-spacing: -0.03em;
    line-height: 1;
    margin: 0 0 1.25rem;
    color: var(--ink);
    max-width: 780px;
    margin-left: auto;
    margin-right: auto;
}
.tm-cta h2 em { font-style: italic; color: var(--muted); }
.tm-cta p {
    font-size: 0.98rem;
    line-height: 1.65;
    color: var(--muted);
    max-width: 520px;
    margin: 0 auto 2rem;
}
.tm-cta-btns {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 0.65rem;
}
.tm-cta-meta {
    margin-top: 2.75rem;
    padding-top: 1.75rem;
    border-top: 1px solid var(--line);
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
    font-family: var(--font-mono);
    font-size: 0.68rem;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--muted-2);
    padding: 1.75rem 0 2.5rem;
}
.tm-footnote span { color: var(--accent); margin: 0 0.4rem; }

/* ============================================================
   RESPONSIVE — single consolidated block
   ============================================================ */
@media (max-width: 900px) {
    .tm-hero { padding: 2.5rem 0 3rem; }
    .tm-hero-intro { margin-bottom: 2rem; }
    .tm-hero h1 { font-size: clamp(2.2rem, 8vw, 3.5rem); }
    .tm-section { padding: 3.5rem 0; }
    .tm-section-head {
        grid-template-columns: 1fr;
        gap: 1.25rem;
        margin-bottom: 2.25rem;
        padding-bottom: 1.25rem;
    }
    .tm-features { grid-template-columns: 1fr; }
    .tm-steps { grid-template-columns: 1fr; gap: 1.75rem; }
    .tm-cta { padding: 3.5rem 0; }
    .tm-quote { padding: 3.5rem 0; }
}

@media (max-width: 640px) {
    .tm-hero { padding: 2rem 0 2.5rem; }
    .tm-eyebrow { font-size: 0.62rem; padding: 0.35rem 0.7rem; }
    .tm-hero h1 { font-size: clamp(2rem, 9vw, 2.9rem); letter-spacing: -0.03em; }
    .tm-hero-lead { font-size: 0.95rem; }
    .tm-hero-actions { flex-direction: column; width: 100%; }
    .tm-hero-actions .tm-btn { width: 100%; }
    .tm-hero-proof { flex-direction: column; margin-bottom: 2rem; }
    .tm-proof-item {
        border-right: none;
        border-bottom: 1px solid var(--line);
        padding: 0.85rem 1rem;
    }
    .tm-proof-item:last-child { border-bottom: none; }

    .tm-arena { padding: 1.1rem; border-radius: 8px; }
    .tm-arena-head { padding-bottom: 0.9rem; margin-bottom: 0.9rem; gap: 0.6rem; }
    .tm-arena-tag { font-size: 0.6rem; }
    .tm-picker .duration-btn { padding: 0.35rem 0.6rem; font-size: 0.68rem; min-width: 38px; }

    .tm-hud { grid-template-columns: repeat(2, 1fr); }
    .tm-hud-cell { padding: 0.7rem 0.4rem; }
    .tm-hud-label { font-size: 0.55rem; }
    .tm-hud-value { font-size: 1.3rem; }

    .tm-board { padding: 1rem 0.9rem; min-height: 160px; }
    .tm-text { font-size: 1rem; line-height: 1.8; }

    .tm-overlay h4 { font-size: 1.3rem; }
    .tm-overlay p { font-size: 0.72rem; }
    .tm-overlay-btns { flex-direction: column; width: 100%; }
    .tm-overlay-btns .tm-btn { width: 100%; }

    .tm-board-foot { font-size: 0.65rem; }
    .tm-board-foot .d-none.d-sm-inline { display: none !important; }

    .tm-feature { padding: 1.75rem 1.5rem; }
    .tm-step h3 { font-size: 1.3rem; }
    .tm-cta h2 { font-size: clamp(1.9rem, 8vw, 2.6rem); }
    .tm-cta-btns { flex-direction: column; width: 100%; }
    .tm-cta-btns .tm-btn { width: 100%; }
    .tm-cta-meta { gap: 1.5rem; }
    .tm-cta-meta .tm-proof-item .v { font-size: 1.1rem; }
    .tm-cta-meta .tm-proof-item .l { font-size: 0.6rem; }
}

@media (max-width: 380px) {
    .tm-hero h1 { font-size: 1.85rem; }
    .tm-hud-value { font-size: 1.15rem; }
    .tm-text { font-size: 0.92rem; }
}
</style>

<div class="tm-landing">

  <!-- ============ TOP BAR ============ -->
  <div class="tm-wrap">
    <div class="tm-topbar">
      <div class="tm-topbar-left">
        <a href="<?= BASE_URL ?>/" class="tm-logo">
          <span class="tm-logo-mark">T</span>
          <span>TypeMaster<span style="font-weight:400;opacity:0.6;">.pro</span></span>
        </a>
        <nav class="tm-topbar-nav">
          <a href="#features">Features</a>
          <a href="#how">How it works</a>
          <a href="<?= BASE_URL ?>/practice.php">Practice</a>
        </nav>
      </div>
      <div class="tm-topbar-right">
        <span class="tm-pill"><span class="dot"></span> Live v2.0</span>
        <a href="<?= BASE_URL ?>/login.php" class="tm-btn tm-btn-outline tm-btn-sm">Sign in</a>
      </div>
    </div>
  </div>

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
    TypeMaster Pro <span>◆</span> Version 2.0 <span>◆</span> Built for typists
  </div>

</div>

<!-- ============================================================
     INLINE SPEED ENGINE — fully working on mobile + desktop
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

  /* ---------- Passage rendering ---------- */
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

  /* ---------- Reset ---------- */
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

  /* ---------- Timer ---------- */
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

  /* ---------- Metrics ---------- */
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

  /* ---------- Finish ---------- */
  function finishTest() {
    if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
    isFinished = true;
    updateMetrics();

    overlayNetWpm.textContent = netWpmEl.textContent;
    overlayAcc.textContent    = accEl.textContent.replace('%', '');
    overlay.classList.add('show');
  }

  /* ---------- Input handling ---------- */
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

  /* ---------- Focus management ---------- */
  function focusBoard() {
    // On iOS, focus must come from within a user gesture — the click handler qualifies
    hiddenInput.focus({ preventScroll: true });
    board.classList.add('focused');
  }

  board.addEventListener('mousedown', function(e) {
    // Prevent default blur-on-click of hidden input
    e.preventDefault();
    focusBoard();
  });
  board.addEventListener('touchstart', function(e) {
    // Allow touch to focus without zooming
    focusBoard();
  }, { passive: true });
  board.addEventListener('click', focusBoard);

  document.addEventListener('click', function(e) {
    if (!board.contains(e.target) && e.target !== hiddenInput) {
      board.classList.remove('focused');
      if (document.activeElement === hiddenInput) hiddenInput.blur();
    }
  });

  /* ---------- Duration buttons ---------- */
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

  /* ---------- Reset / Retry ---------- */
  resetBtn.addEventListener('click', function() { resetTest(); focusBoard(); });
  retryBtn.addEventListener('click', function() { resetTest(); focusBoard(); });

  /* ---------- Tab + Enter restart shortcut ---------- */
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

  /* ---------- Boot ---------- */
  initPassage();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>