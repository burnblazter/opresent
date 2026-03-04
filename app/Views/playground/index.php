<?php
// \app\Views\playground\index.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title><?= $title ?> | PresenSi</title>
  <link rel="icon" href="<?= base_url('assets/img/company/logo.png') ?>">
  <link href="<?= base_url('assets/css/tabler.min.css') ?>" rel="stylesheet" />

  <style>
  /* ============================================================
   ROOT
   ============================================================ */
  :root {
    --pr: #1e3a8a;
    --pr-d: #162d6e;
    --pr-l: #dbeafe;
    --sc: #dda518;
    --sc-d: #c89316;
    --sc-l: #fef3c7;
    --ok: #10b981;
    --warn: #f59e0b;
    --err: #ef4444;
    --bg: #f1f5f9;
    --surf: #ffffff;
    --surf2: #f8fafc;
    --bdr: #e2e8f0;
    --txt: #1e293b;
    --muted: #64748b;
    --rad: 12px;
    --shadow: 0 4px 24px rgba(30, 58, 138, .10);
    --t: .22s cubic-bezier(.4, 0, .2, 1);
  }

  *,
  *::before,
  *::after {
    box-sizing: border-box
  }

  html {
    scroll-behavior: smooth
  }

  body {
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    background: var(--bg);
    color: var(--txt);
    margin: 0;
    min-height: 100vh;
  }

  /* ============================================================
   TOP NAV
   ============================================================ */
  .pg-nav {
    background: var(--pr);
    height: 56px;
    padding: 0 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 200;
    box-shadow: 0 2px 12px rgba(0, 0, 0, .2);
  }

  .pg-nav-left {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #fff;
    text-decoration: none;
    font-weight: 700;
    font-size: .95rem
  }

  .pg-nav-left img {
    height: 30px;
    width: auto
  }

  .sandbox-badge {
    background: var(--sc);
    color: #fff;
    font-size: .65rem;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 20px;
    letter-spacing: .8px;
    text-transform: uppercase;
  }

  .live-dot-nav {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #10b981;
    animation: blink 1.2s ease-in-out infinite;
    flex-shrink: 0;
  }

  .nav-right {
    display: flex;
    align-items: center;
    gap: 8px
  }

  .nav-cta {
    background: var(--sc);
    color: #fff;
    border: none;
    padding: 7px 16px;
    border-radius: 8px;
    font-weight: 700;
    font-size: .82rem;
    cursor: pointer;
    text-decoration: none;
    transition: background var(--t), transform var(--t);
    white-space: nowrap;
  }

  .nav-cta:hover {
    background: var(--sc-d);
    transform: translateY(-1px);
    color: #fff
  }

  @keyframes blink {

    0%,
    100% {
      opacity: 1;
      transform: scale(1)
    }

    50% {
      opacity: .4;
      transform: scale(.7)
    }
  }

  /* ============================================================
   HERO
   ============================================================ */
  .pg-hero {
    background: linear-gradient(135deg, var(--pr) 0%, #2d4faa 100%);
    color: #fff;
    padding: 28px 20px 24px;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .pg-hero::before {
    content: '';
    position: absolute;
    width: 360px;
    height: 360px;
    border-radius: 50%;
    background: rgba(221, 165, 24, .08);
    top: -120px;
    right: -80px;
    pointer-events: none;
  }

  .pg-hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(221, 165, 24, .18);
    border: 1px solid rgba(221, 165, 24, .4);
    color: var(--sc);
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 3px 12px;
    border-radius: 20px;
    margin-bottom: 12px;
  }

  .pg-hero h1 {
    margin: 0 0 8px;
    font-size: clamp(1.3rem, 4vw, 1.9rem);
    font-weight: 800;
    line-height: 1.2;
  }

  .pg-hero p {
    margin: 0 auto;
    opacity: .82;
    font-size: .88rem;
    max-width: 520px;
    line-height: 1.6
  }

  .hero-mfa-pills {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-top: 16px;
  }

  .mfa-pill {
    display: flex;
    align-items: center;
    gap: 5px;
    background: rgba(255, 255, 255, .12);
    border: 1px solid rgba(255, 255, 255, .2);
    color: #fff;
    font-size: .75rem;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 20px;
  }

  .mfa-pill-sep {
    color: rgba(255, 255, 255, .4);
    font-size: .9rem
  }

  /* ============================================================
   LAYOUT
   ============================================================ */
  .pg-wrap {
    max-width: 1140px;
    margin: 0 auto;
    padding: 24px 16px 64px
  }

  .pg-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 20px;
    align-items: start;
  }

  @media(max-width:920px) {
    .pg-grid {
      grid-template-columns: 1fr
    }
  }

  /* ============================================================
   CARD
   ============================================================ */
  .pg-card {
    background: var(--surf);
    border-radius: var(--rad);
    box-shadow: var(--shadow);
    border: 1px solid var(--bdr);
    overflow: hidden;
  }

  .pg-card+.pg-card {
    margin-top: 16px
  }

  .card-head {
    padding: 14px 18px;
    border-bottom: 1px solid var(--bdr);
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .card-title {
    font-weight: 700;
    font-size: .88rem;
    color: var(--pr);
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
  }

  .card-body {
    padding: 18px
  }

  /* ============================================================
   MFA STEP INDICATOR
   ============================================================ */
  .mfa-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    margin-bottom: 18px;
  }

  .mfa-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    flex: 1;
    position: relative;
  }

  .mfa-step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 17px;
    left: calc(50% + 18px);
    right: calc(-50% + 18px);
    height: 2px;
    background: var(--bdr);
    transition: background .4s;
  }

  .mfa-step.done:not(:last-child)::after {
    background: var(--ok)
  }

  .mfa-step-icon {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: var(--surf2);
    border: 2px solid var(--bdr);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .95rem;
    transition: all .3s;
    z-index: 1;
  }

  .mfa-step.active .mfa-step-icon {
    background: var(--pr);
    border-color: var(--pr);
    animation: pulseStep .9s ease-in-out infinite alternate;
  }

  .mfa-step.done .mfa-step-icon {
    background: var(--ok);
    border-color: var(--ok)
  }

  .mfa-step-label {
    font-size: .65rem;
    font-weight: 700;
    color: var(--muted);
    text-align: center;
    text-transform: uppercase;
    letter-spacing: .4px;
  }

  .mfa-step.active .mfa-step-label {
    color: var(--pr)
  }

  .mfa-step.done .mfa-step-label {
    color: var(--ok)
  }

  @keyframes pulseStep {
    from {
      box-shadow: 0 0 0 0 rgba(30, 58, 138, .4)
    }

    to {
      box-shadow: 0 0 0 8px rgba(30, 58, 138, 0)
    }
  }

  /* ============================================================
   STATE BANNER
   ============================================================ */
  .state-banner {
    border-radius: 10px;
    padding: 11px 16px;
    font-size: .85rem;
    font-weight: 600;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid transparent;
    transition: all .3s;
    min-height: 44px;
  }

  .state-banner.info {
    background: #dbeafe;
    color: #1e40af;
    border-color: #93c5fd
  }

  .state-banner.success {
    background: #d1fae5;
    color: #065f46;
    border-color: #6ee7b7
  }

  .state-banner.warning {
    background: #fef3c7;
    color: #92400e;
    border-color: #fcd34d
  }

  .state-banner.danger {
    background: #fee2e2;
    color: #991b1b;
    border-color: #fca5a5
  }

  .state-banner.idle {
    background: var(--surf2);
    color: var(--muted);
    border-color: var(--bdr)
  }

  /* ============================================================
   CAMERA
   ============================================================ */
  .cam-outer {
    position: relative;
    background: #0f172a;
    border-radius: 10px;
    overflow: hidden;
    aspect-ratio: 4/3;
  }

  #pg-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transform: scaleX(-1);
  }

  #pg-overlay {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
  }

  .cam-corner {
    position: absolute;
    width: 22px;
    height: 22px;
    border-color: var(--sc);
    border-style: solid;
    border-width: 0;
    opacity: .85;
    transition: opacity var(--t);
  }

  .cam-corner.tl {
    top: 10px;
    left: 10px;
    border-top-width: 3px;
    border-left-width: 3px;
    border-radius: 4px 0 0 0
  }

  .cam-corner.tr {
    top: 10px;
    right: 10px;
    border-top-width: 3px;
    border-right-width: 3px;
    border-radius: 0 4px 0 0
  }

  .cam-corner.bl {
    bottom: 10px;
    left: 10px;
    border-bottom-width: 3px;
    border-left-width: 3px;
    border-radius: 0 0 0 4px
  }

  .cam-corner.br {
    bottom: 10px;
    right: 10px;
    border-bottom-width: 3px;
    border-right-width: 3px;
    border-radius: 0 0 4px 0
  }

  .cam-scanline {
    position: absolute;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--sc), transparent);
    opacity: 0;
    transition: opacity .4s;
  }

  .cam-scanline.on {
    opacity: .55;
    animation: scan 2.2s ease-in-out infinite
  }

  @keyframes scan {
    0% {
      top: 8%
    }

    50% {
      top: 88%
    }

    100% {
      top: 8%
    }
  }

  .cam-init {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: rgba(15, 23, 42, .88);
    backdrop-filter: blur(2px);
    transition: opacity .4s;
  }

  .cam-init.gone {
    opacity: 0;
    pointer-events: none
  }

  .cam-init-icon {
    font-size: 2.8rem;
    margin-bottom: 10px
  }

  .cam-init-text {
    color: #fff;
    font-size: .88rem;
    font-weight: 600;
    text-align: center;
    padding: 0 20px
  }

  /* Success flash overlay */
  .cam-success-flash {
    position: absolute;
    inset: 0;
    background: rgba(16, 185, 129, .18);
    opacity: 0;
    pointer-events: none;
    transition: opacity .3s;
    border-radius: 10px;
  }

  .cam-success-flash.flash {
    animation: flashAnim .6s ease forwards
  }

  @keyframes flashAnim {
    0% {
      opacity: .8
    }

    100% {
      opacity: 0
    }
  }

  /* Cam stats */
  .cam-stats {
    display: flex;
    background: #0f172a;
    border-radius: 0 0 10px 10px;
    padding: 8px 12px;
    gap: 0;
    margin-top: -4px;
  }

  .cam-stat {
    flex: 1;
    text-align: center;
    font-family: 'Courier New', monospace;
    font-size: .7rem;
    color: #00d2ff;
    border-right: 1px solid rgba(0, 210, 255, .12);
    padding: 3px 0;
  }

  .cam-stat:last-child {
    border-right: none
  }

  .cam-stat strong {
    display: block;
    color: #fff;
    font-size: .82rem;
    margin-top: 1px
  }

  /* ============================================================
   MFA BADGE STRIP
   ============================================================ */
  .mfa-badges {
    display: flex;
    gap: 8px;
    margin: 14px 0 0;
    flex-wrap: wrap;
  }

  .mfa-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    flex: 1;
    min-width: 90px;
    padding: 9px 12px;
    border-radius: 10px;
    background: var(--surf2);
    border: 2px solid var(--bdr);
    font-size: .78rem;
    font-weight: 700;
    transition: all .35s;
  }

  .mfa-badge.ok {
    background: #f0fdf4;
    border-color: var(--ok);
    color: #065f46
  }

  .mfa-badge.fail {
    background: #fee2e2;
    border-color: var(--err);
    color: #991b1b
  }

  .mfa-badge.wait {
    background: var(--surf2);
    border-color: var(--bdr);
    color: var(--muted)
  }

  .mfa-badge-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: var(--muted);
    flex-shrink: 0;
    transition: background .3s;
  }

  .mfa-badge.ok .mfa-badge-dot {
    background: var(--ok)
  }

  .mfa-badge.fail .mfa-badge-dot {
    background: var(--err)
  }

  .mfa-badge.wait .mfa-badge-dot {
    background: var(--warn);
    animation: blink 1s infinite
  }

  /* ============================================================
   RECOGNITION RESULT
   ============================================================ */
  .rec-box {
    background: var(--surf2);
    border-radius: 10px;
    padding: 14px 16px;
    border: 2px solid var(--bdr);
    margin: 14px 0 0;
    display: none;
  }

  .rec-box.show {
    display: block;
    animation: fadeUp .3s ease
  }

  .rec-name {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--pr)
  }

  .rec-sub {
    font-size: .78rem;
    color: var(--muted);
    margin-top: 2px
  }

  .conf-bar-wrap {
    height: 8px;
    background: var(--bdr);
    border-radius: 4px;
    overflow: hidden;
    margin-top: 10px
  }

  .conf-bar-fill {
    height: 100%;
    background: var(--sc);
    border-radius: 4px;
    transition: width .6s cubic-bezier(.4, 0, .2, 1);
    width: 0%;
  }

  /* ============================================================
   REGISTER PANEL
   ============================================================ */
  .reg-row {
    display: flex;
    gap: 8px;
    margin-bottom: 10px
  }

  .reg-row input {
    flex: 1;
    border: 2px solid var(--bdr);
    border-radius: 8px;
    padding: 9px 12px;
    font-size: .88rem;
    outline: none;
    transition: border-color var(--t);
    font-family: inherit;
    background: var(--surf);
  }

  .reg-row input:focus {
    border-color: var(--pr)
  }

  .reg-hint {
    font-size: .75rem;
    color: var(--muted);
    line-height: 1.5;
    margin-bottom: 12px;
  }

  /* ============================================================
   GPS & TIME PANEL
   ============================================================ */
  .coord-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 10px;
  }

  .coord-field label {
    display: block;
    font-size: .72rem;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    margin-bottom: 4px;
  }

  .coord-field input {
    width: 100%;
    border: 2px solid var(--bdr);
    border-radius: 8px;
    padding: 8px 10px;
    font-size: .82rem;
    outline: none;
    transition: border-color var(--t);
    font-family: 'Courier New', monospace;
    background: var(--surf);
  }

  .coord-field input:focus {
    border-color: var(--pr)
  }

  .gps-status-row {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .78rem;
    margin-bottom: 10px;
  }

  .gps-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--muted);
    flex-shrink: 0;
  }

  .gps-dot.active {
    background: var(--ok);
    animation: blink 1s infinite
  }

  .gps-dot.ok {
    background: var(--ok)
  }

  .time-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 10px;
  }

  .time-field label {
    display: block;
    font-size: .72rem;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    margin-bottom: 4px;
  }

  .time-field input {
    width: 100%;
    border: 2px solid var(--bdr);
    border-radius: 8px;
    padding: 8px 10px;
    font-size: .88rem;
    outline: none;
    transition: border-color var(--t);
    font-family: inherit;
    background: var(--surf);
  }

  .time-field input:focus {
    border-color: var(--pr)
  }

  .time-status-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: .75rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    background: var(--surf2);
    border: 1px solid var(--bdr);
    color: var(--muted);
    transition: all .3s;
  }

  .time-status-chip.valid {
    background: #d1fae5;
    border-color: var(--ok);
    color: #065f46
  }

  .time-status-chip.late {
    background: #fef3c7;
    border-color: var(--warn);
    color: #92400e
  }

  .time-status-chip.out {
    background: #fee2e2;
    border-color: var(--err);
    color: #991b1b
  }

  /* ============================================================
   FACE LIST
   ============================================================ */
  .face-list-wrap {
    display: flex;
    flex-direction: column;
    gap: 8px
  }

  .face-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    background: var(--surf2);
    border-radius: 9px;
    border: 1px solid var(--bdr);
    transition: all .25s;
    animation: fadeUp .3s ease;
  }

  .face-item.matched {
    border-color: var(--ok);
    background: #f0fdf4
  }

  .face-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--pr);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: .9rem;
    flex-shrink: 0;
  }

  .face-info {
    flex: 1;
    min-width: 0
  }

  .face-name {
    font-weight: 700;
    font-size: .85rem;
    color: var(--txt);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis
  }

  .face-meta {
    font-size: .7rem;
    color: var(--muted)
  }

  .face-match-badge {
    font-size: .7rem;
    font-weight: 700;
    color: var(--ok);
    display: none;
  }

  .face-item.matched .face-match-badge {
    display: block
  }

  .face-empty {
    text-align: center;
    padding: 20px 12px;
    color: var(--muted);
  }

  .face-empty-icon {
    font-size: 2rem;
    margin-bottom: 6px
  }

  .face-empty p {
    font-size: .82rem;
    margin: 0;
    line-height: 1.5
  }

  /* ============================================================
   SUBMIT BUTTON
   ============================================================ */
  .submit-btn {
    width: 100%;
    padding: 13px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    font-weight: 800;
    font-size: .95rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: var(--pr);
    color: #fff;
    transition: all var(--t);
    font-family: inherit;
    position: relative;
    overflow: hidden;
  }

  .submit-btn:disabled {
    opacity: .45;
    cursor: not-allowed;
    transform: none !important
  }

  .submit-btn:not(:disabled):hover {
    background: var(--pr-d);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(30, 58, 138, .3)
  }

  .submit-btn-progress {
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    background: rgba(221, 165, 24, .3);
    width: 0%;
    transition: width .1s linear;
    pointer-events: none;
  }

  /* ============================================================
   RECEIPT / SUCCESS STATE
   ============================================================ */
  .receipt-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, .75);
    backdrop-filter: blur(4px);
    z-index: 500;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  .receipt-overlay.show {
    display: flex;
    animation: fadeIn .3s ease
  }

  .receipt-card {
    background: var(--surf);
    border-radius: 16px;
    padding: 32px 28px;
    max-width: 420px;
    width: 100%;
    box-shadow: 0 24px 60px rgba(0, 0, 0, .2);
    text-align: center;
    animation: slideUp .35s cubic-bezier(.34, 1.56, .64, 1);
  }

  @keyframes slideUp {
    from {
      opacity: 0;
      transform: translateY(30px)
    }

    to {
      opacity: 1;
      transform: translateY(0)
    }
  }

  .receipt-icon {
    font-size: 3.5rem;
    margin-bottom: 8px
  }

  .receipt-title {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--pr);
    margin-bottom: 4px
  }

  .receipt-sub {
    font-size: .82rem;
    color: var(--muted);
    margin-bottom: 18px;
    line-height: 1.5
  }

  .receipt-id {
    font-family: 'Courier New', monospace;
    font-size: .78rem;
    background: var(--surf2);
    border: 1px solid var(--bdr);
    padding: 6px 12px;
    border-radius: 6px;
    display: inline-block;
    margin-bottom: 16px;
    color: var(--muted);
  }

  .receipt-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 18px;
    text-align: left;
  }

  .receipt-field {
    background: var(--surf2);
    border-radius: 8px;
    padding: 10px 12px;
  }

  .receipt-field-label {
    font-size: .68rem;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    margin-bottom: 2px
  }

  .receipt-field-value {
    font-size: .88rem;
    font-weight: 700;
    color: var(--txt)
  }

  .receipt-mfa-row {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-bottom: 18px;
  }

  .receipt-mfa-chip {
    display: flex;
    align-items: center;
    gap: 5px;
    background: #f0fdf4;
    border: 1px solid var(--ok);
    color: #065f46;
    font-size: .72rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
  }

  .sandbox-note {
    font-size: .72rem;
    color: var(--muted);
    background: var(--surf2);
    border: 1px solid var(--bdr);
    padding: 8px 12px;
    border-radius: 8px;
    margin-bottom: 14px;
    line-height: 1.5;
  }

  .sandbox-note strong {
    color: var(--sc)
  }

  /* ============================================================
   MARKETING
   ============================================================ */
  .mkt-card {
    background: linear-gradient(135deg, var(--pr) 0%, #1a3278 100%);
    color: #fff;
    border-radius: var(--rad);
    padding: 26px 22px;
    margin-top: 20px;
    position: relative;
    overflow: hidden;
  }

  .mkt-card::before {
    content: '';
    position: absolute;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: rgba(221, 165, 24, .1);
    top: -70px;
    right: -50px;
    pointer-events: none;
  }

  .mkt-real-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(16, 185, 129, .2);
    border: 1px solid rgba(16, 185, 129, .35);
    color: #6ee7b7;
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .8px;
    text-transform: uppercase;
    padding: 3px 10px;
    border-radius: 20px;
    margin-bottom: 12px;
  }

  .mkt-card h3 {
    font-size: 1.1rem;
    font-weight: 800;
    margin: 0 0 8px
  }

  .mkt-card p {
    font-size: .82rem;
    opacity: .82;
    margin: 0 0 14px;
    line-height: 1.6
  }

  .mkt-features {
    display: flex;
    flex-direction: column;
    gap: 7px;
    margin-bottom: 16px
  }

  .mkt-feature {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .82rem
  }

  .mkt-feature-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--sc);
    flex-shrink: 0
  }

  .mkt-divider {
    height: 1px;
    background: rgba(255, 255, 255, .12);
    margin: 16px 0
  }

  .mkt-cta-row {
    display: flex;
    gap: 8px;
    flex-wrap: wrap
  }

  .mkt-contact-note {
    font-size: .75rem;
    opacity: .65;
    text-align: center;
    margin-top: 10px
  }

  .mkt-contact-note a {
    color: var(--sc);
    text-decoration: none;
    font-weight: 700
  }

  /* ============================================================
   BUTTONS
   ============================================================ */
  .btn-pg {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 9px 16px;
    border-radius: 8px;
    font-weight: 700;
    font-size: .82rem;
    border: none;
    cursor: pointer;
    transition: all var(--t);
    font-family: inherit;
    text-decoration: none;
    white-space: nowrap;
  }

  .btn-pg:disabled {
    opacity: .45;
    cursor: not-allowed;
    transform: none !important
  }

  .btn-sc {
    background: var(--sc);
    color: #fff
  }

  .btn-sc:not(:disabled):hover {
    background: var(--sc-d);
    transform: translateY(-1px);
    color: #fff
  }

  .btn-pr {
    background: var(--pr);
    color: #fff
  }

  .btn-pr:not(:disabled):hover {
    background: var(--pr-d);
    transform: translateY(-1px);
    color: #fff
  }

  .btn-ghost {
    background: var(--surf2);
    color: var(--muted);
    border: 1px solid var(--bdr)
  }

  .btn-ghost:not(:disabled):hover {
    background: var(--bdr);
    color: var(--txt)
  }

  .btn-outline-light {
    background: transparent;
    color: #fff;
    border: 1.5px solid rgba(255, 255, 255, .4);
  }

  .btn-outline-light:not(:disabled):hover {
    background: rgba(255, 255, 255, .1);
    color: #fff
  }

  .btn-full {
    width: 100%
  }

  .btn-sm {
    padding: 6px 12px;
    font-size: .78rem
  }

  /* ============================================================
   MISC
   ============================================================ */
  .section-sep {
    height: 1px;
    background: var(--bdr);
    margin: 14px 0
  }

  .text-sm {
    font-size: .8rem
  }

  .text-muted {
    color: var(--muted)
  }

  .font-mono {
    font-family: 'Courier New', monospace
  }

  .fw-700 {
    font-weight: 700
  }

  .mb-0 {
    margin-bottom: 0
  }

  .mt-2 {
    margin-top: 8px
  }

  .mt-3 {
    margin-top: 12px
  }

  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--muted);
    font-size: .82rem;
    font-weight: 600;
    text-decoration: none;
    padding: 6px 0;
    margin-bottom: 14px;
    transition: color var(--t);
  }

  .back-link:hover {
    color: var(--pr)
  }

  @keyframes fadeUp {
    from {
      opacity: 0;
      transform: translateY(8px)
    }

    to {
      opacity: 1;
      transform: translateY(0)
    }
  }

  @keyframes fadeIn {
    from {
      opacity: 0
    }

    to {
      opacity: 1
    }
  }

  canvas#pg-canvas {
    display: none
  }

  /* Scrollbar */
  ::-webkit-scrollbar {
    width: 5px;
    height: 5px
  }

  ::-webkit-scrollbar-track {
    background: transparent
  }

  ::-webkit-scrollbar-thumb {
    background: var(--bdr);
    border-radius: 3px
  }
  </style>
  <script src="<?= base_url('assets/js/darkreader.min.js') ?>"></script>
  <script>
  const savedTheme = localStorage.getItem('theme-preference');
  if (savedTheme === 'dark') DarkReader.enable({
    brightness: 100,
    contrast: 100,
    sepia: 5
  });
  </script>
</head>

<body>
  <div class="theme-switcher d-flex gap-1" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
    <a href="#" class="nav-link px-2" id="enable-dark-mode"
      style="background: rgba(255,255,255,0.9); border-radius: 50%; padding: 0.6rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"><svg
        xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2"
        stroke="currentColor" fill="none">
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
      </svg></a>
    <a href="#" class="nav-link px-2 d-none" id="enable-light-mode"
      style="background: rgba(255,255,255,0.9); border-radius: 50%; padding: 0.6rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"><svg
        xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2"
        stroke="currentColor" fill="none">
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
        <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
      </svg></a>
  </div>

  <!-- ==================== NAV ==================== -->
  <nav class="pg-nav">
    <a href="<?= base_url('login') ?>" class="pg-nav-left">
      <img src="<?= base_url('assets/img/company/logo.png') ?>" alt="PresenSi" onerror="this.style.display='none'">
      PresenSi
      <span class="sandbox-badge">Playground</span>
      <span class="live-dot-nav"></span>
    </a>
    <div class="nav-right">
      <span class="text-sm" style="color:rgba(255,255,255,.6);display:none" id="nav-clock"></span>
      <a href="#mkt" class="nav-cta">Hubungi Saya</a>
    </div>
  </nav>

  <!-- ==================== HERO ==================== -->
  <section class="pg-hero">
    <div class="pg-hero-eyebrow">
      <span class="live-dot-nav"></span>
      Demonstrasi Sistem Nyata
    </div>
    <h1>Presensi Cerdas — Multi-Factor Authentication</h1>
    <p>Playground ini merepresentasikan sistem presensi anti titip-absen yang berjalan di PresenSi.<br>
      Wajah + GPS + Waktu, semua terverifikasi secara bersamaan.</p>
    <div class="hero-mfa-pills">
      <div class="mfa-pill">🤳 Face Recognition AI</div>
      <span class="mfa-pill-sep">→</span>
      <div class="mfa-pill">📍 GPS Verification</div>
      <span class="mfa-pill-sep">→</span>
      <div class="mfa-pill">🕐 Time Validation</div>
      <span class="mfa-pill-sep">→</span>
      <div class="mfa-pill" style="background:rgba(221,165,24,.2);border-color:rgba(221,165,24,.4)">✅ Presensi Sah</div>
    </div>
  </section>

  <!-- ==================== MAIN ==================== -->
  <div class="pg-wrap">
    <a href="<?= base_url('login') ?>" class="back-link">
      ← Kembali ke Login
    </a>

    <!-- MFA Step Indicator -->
    <div class="mfa-steps" id="mfa-steps">
      <div class="mfa-step active" id="step-face">
        <div class="mfa-step-icon">🤳</div>
        <div class="mfa-step-label">Wajah</div>
      </div>
      <div class="mfa-step" id="step-gps">
        <div class="mfa-step-icon">📍</div>
        <div class="mfa-step-label">Lokasi</div>
      </div>
      <div class="mfa-step" id="step-time">
        <div class="mfa-step-icon">🕐</div>
        <div class="mfa-step-label">Waktu</div>
      </div>
      <div class="mfa-step" id="step-done">
        <div class="mfa-step-icon">✅</div>
        <div class="mfa-step-label">Selesai</div>
      </div>
    </div>

    <!-- Status Banner -->
    <div class="state-banner idle" id="state-banner">
      <span id="banner-icon">💤</span>
      <span id="banner-text">Siap Presensi — Daftarkan wajah Anda terlebih dahulu, lalu atur lokasi &amp; waktu.</span>
    </div>

    <div class="pg-grid">

      <!-- ========== KIRI: KAMERA ========== -->
      <div>
        <div class="pg-card">
          <div class="card-head">
            <h3 class="card-title">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                <circle cx="12" cy="13" r="4" />
              </svg>
              Kamera Presensi
            </h3>
            <span style="font-size:.72rem;color:var(--muted)" id="cam-label-top">Face Recognition Engine</span>
          </div>
          <div class="card-body" style="padding:14px">
            <!-- Camera -->
            <div class="cam-outer">
              <video id="pg-video" autoplay playsinline muted></video>
              <canvas id="pg-overlay"></canvas>
              <div class="cam-corner tl"></div>
              <div class="cam-corner tr"></div>
              <div class="cam-corner bl"></div>
              <div class="cam-corner br"></div>
              <div class="cam-scanline" id="cam-scanline"></div>
              <div class="cam-success-flash" id="cam-flash"></div>
              <div class="cam-init" id="cam-init">
                <div class="cam-init-icon">🧠</div>
                <div class="cam-init-text" id="cam-init-text">Memuat AI engine...<br><small style="opacity:.6">Human.js
                    + BlazeFace</small></div>
              </div>
            </div>
            <!-- Stats bar -->
            <div class="cam-stats">
              <div class="cam-stat">Wajah<strong id="s-faces">0</strong></div>
              <div class="cam-stat">Usia<strong id="s-age">—</strong></div>
              <div class="cam-stat">Emosi<strong id="s-emotion">—</strong></div>
              <div class="cam-stat">Cocok<strong id="s-score">—</strong></div>
            </div>

            <!-- MFA Badge Strip -->
            <div class="mfa-badges">
              <div class="mfa-badge wait" id="badge-face">
                <div class="mfa-badge-dot"></div>
                🤳 Wajah
              </div>
              <div class="mfa-badge wait" id="badge-gps">
                <div class="mfa-badge-dot"></div>
                📍 Lokasi
              </div>
              <div class="mfa-badge wait" id="badge-time">
                <div class="mfa-badge-dot"></div>
                🕐 Waktu
              </div>
            </div>

            <!-- Recognition Result -->
            <div class="rec-box" id="rec-box">
              <div style="display:flex;align-items:center;justify-content:space-between">
                <div>
                  <div class="rec-name" id="rec-name">—</div>
                  <div class="rec-sub" id="rec-sub">Tingkat kecocokan —</div>
                </div>
                <div style="font-size:1.8rem">✅</div>
              </div>
              <div class="conf-bar-wrap">
                <div class="conf-bar-fill" id="conf-fill"></div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-3">
              <button class="submit-btn" id="btn-submit" disabled>
                <div class="submit-btn-progress" id="submit-progress"></div>
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                  style="position:relative">
                  <path d="M9 11l3 3L22 4" />
                  <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                </svg>
                <span id="submit-btn-text" style="position:relative">Presensi Masuk (Sandbox)</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ========== KANAN: PANEL KONTROL ========== -->
      <div>

        <!-- DAFTAR WAJAH -->
        <div class="pg-card">
          <div class="card-head">
            <h3 class="card-title">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
              </svg>
              Daftarkan Wajah
            </h3>
            <button class="btn-pg btn-ghost btn-sm" id="btn-clear">🗑 Reset</button>
          </div>
          <div class="card-body">
            <p class="reg-hint">
              Posisikan wajah Anda di kamera, isi nama, lalu klik
              <strong>Daftarkan</strong>. Data tersimpan di sesi ini saja&nbsp;
              <span style="color:var(--sc)">— tidak masuk database asli.</span>
            </p>
            <div class="reg-row">
              <input type="text" id="reg-name" placeholder="Nama Anda..." maxlength="24">
              <button class="btn-pg btn-sc" id="btn-register" disabled>Daftarkan</button>
            </div>
            <div class="state-banner" id="reg-status" style="display:none;margin-bottom:0;font-size:.8rem"></div>

            <div class="section-sep"></div>

            <!-- Face list -->
            <div id="face-empty" class="face-empty">
              <div class="face-empty-icon">👤</div>
              <p>Belum ada wajah terdaftar.<br>Daftarkan untuk mulai verifikasi.</p>
            </div>
            <div class="face-list-wrap" id="face-list"></div>
          </div>
        </div>

        <!-- GPS PANEL -->
        <div class="pg-card">
          <div class="card-head">
            <h3 class="card-title">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                <circle cx="12" cy="10" r="3" />
              </svg>
              Verifikasi Lokasi (GPS)
            </h3>
            <button class="btn-pg btn-ghost btn-sm" id="btn-gps-auto">📡 Auto</button>
          </div>
          <div class="card-body">
            <div class="gps-status-row">
              <div class="gps-dot" id="gps-dot"></div>
              <span id="gps-status-text" class="text-sm text-muted">Belum ada data GPS</span>
            </div>
            <div class="coord-row">
              <div class="coord-field">
                <label>Latitude</label>
                <input type="number" id="gps-lat" placeholder="-6.200000" step="0.000001" value="">
              </div>
              <div class="coord-field">
                <label>Longitude</label>
                <input type="number" id="gps-lng" placeholder="106.816666" step="0.000001" value="">
              </div>
            </div>
            <p class="text-sm text-muted mb-0" style="line-height:1.5">
              💡 Koordinat bisa diubah bebas. Klik <strong>Auto</strong> untuk
              menggunakan lokasi perangkat Anda saat ini.
            </p>
          </div>
        </div>

        <!-- TIME PANEL -->
        <div class="pg-card">
          <div class="card-head">
            <h3 class="card-title">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
              </svg>
              Validasi Waktu
            </h3>
            <button class="btn-pg btn-ghost btn-sm" id="btn-time-now">🕐 Sekarang</button>
          </div>
          <div class="card-body">
            <div class="time-row">
              <div class="time-field">
                <label>Jam Masuk (Simulasi)</label>
                <input type="time" id="time-input" value="">
              </div>
              <div class="time-field">
                <label>Batas Masuk</label>
                <input type="time" id="time-limit" value="08:00">
              </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
              <div class="time-status-chip" id="time-chip">⏳ Belum diatur</div>
              <span class="text-sm text-muted" id="time-diff-text"></span>
            </div>
            <p class="text-sm text-muted mt-2 mb-0" style="line-height:1.5">
              💡 Ubah jam bebas untuk mensimulasikan
              <strong>Tepat Waktu</strong>, <strong>Terlambat</strong>, atau
              <strong>Di Luar Jadwal</strong>.
            </p>
          </div>
        </div>

      </div>
    </div><!-- /.pg-grid -->

    <!-- ==================== MARKETING ==================== -->
    <div class="mkt-card" id="mkt">
      <div class="mkt-real-badge">✓ Fitur LIVE — Bukan Mockup</div>
      <h3>Sistem Presensi MFA untuk Institusi Anda</h3>
      <p>
        Yang Anda lihat ini adalah engine yang sama persis berjalan di PresenSi setiap hari.
        Face Recognition berjalan 100% di browser — tanpa kirim foto ke server eksternal.
        Cocok untuk sekolah, kampus, klinik, dan perusahaan.
      </p>
      <div class="mkt-features">
        <div class="mkt-feature">
          <div class="mkt-feature-dot"></div><span>Anti titip absen — Face AI + GPS + Time tiga faktor wajib</span>
        </div>
        <div class="mkt-feature">
          <div class="mkt-feature-dot"></div><span>Tidak perlu hardware khusus — cukup browser &amp; kamera</span>
        </div>
        <div class="mkt-feature">
          <div class="mkt-feature-dot"></div><span>Dashboard real-time, laporan otomatis, integrasi API</span>
        </div>
        <div class="mkt-feature">
          <div class="mkt-feature-dot"></div><span>White-label &amp; customisasi penuh untuk kebutuhan Anda</span>
        </div>
      </div>
      <div class="mkt-cta-row">
        <a href="mailto:hello@fael.my.id" class="btn-pg btn-sc">
          ✉️ hello@fael.my.id
        </a>
        <a href="https://wa.me/6283140459026" target="_blank" rel="noopener" class="btn-pg btn-outline-light">
          💬 WhatsApp Konsultasi
        </a>
      </div>
      <div class="mkt-divider"></div>
      <div class="mkt-contact-note">
        Tertarik kerja sama?
        <a href="mailto:hello@fael.my.id">hello@fael.my.id</a> —
        Open untuk kolaborasi B2B, white-label, dan kemitraan reseller.
      </div>
    </div>

  </div><!-- /.pg-wrap -->

  <!-- ==================== RECEIPT OVERLAY ==================== -->
  <div class="receipt-overlay" id="receipt-overlay">
    <div class="receipt-card">
      <div class="receipt-icon">✅</div>
      <div class="receipt-title">Presensi Berhasil!</div>
      <div class="receipt-sub">
        Semua faktor MFA terverifikasi.<br>
        <em>Ini adalah simulasi sandbox — tidak tersimpan di database nyata.</em>
      </div>
      <div class="sandbox-note">
        <strong>⚠️ Sandbox Mode</strong> — Data ini hanya simulasi publik untuk
        menunjukkan alur sistem presensi nyata PresenSi.
      </div>
      <div class="receipt-mfa-row">
        <div class="receipt-mfa-chip">🤳 Wajah ✓</div>
        <div class="receipt-mfa-chip">📍 Lokasi ✓</div>
        <div class="receipt-mfa-chip">🕐 Waktu ✓</div>
      </div>
      <div class="receipt-grid">
        <div class="receipt-field">
          <div class="receipt-field-label">Nama</div>
          <div class="receipt-field-value" id="r-name">—</div>
        </div>
        <div class="receipt-field">
          <div class="receipt-field-label">Akurasi Wajah</div>
          <div class="receipt-field-value" id="r-score">—</div>
        </div>
        <div class="receipt-field">
          <div class="receipt-field-label">Jam Masuk</div>
          <div class="receipt-field-value" id="r-time">—</div>
        </div>
        <div class="receipt-field">
          <div class="receipt-field-label">Status</div>
          <div class="receipt-field-value" id="r-status">—</div>
        </div>
        <div class="receipt-field" style="grid-column:1/-1">
          <div class="receipt-field-label">Koordinat GPS</div>
          <div class="receipt-field-value font-mono" id="r-coords">—</div>
        </div>
      </div>
      <button class="btn-pg btn-pr btn-full" id="btn-receipt-close">Coba Lagi</button>
      <div style="margin-top:10px">
        <a href="#mkt" class="text-sm" style="color:var(--sc);font-weight:700;text-decoration:none"
          onclick="document.getElementById('receipt-overlay').classList.remove('show')">
          Integrasi untuk institusi Anda? →
        </a>
      </div>
    </div>
  </div>

  <!-- Canvas hidden -->
  <canvas id="pg-canvas"></canvas>

  <!-- ==================== SCRIPTS ==================== -->
  <script>
  window.PG_CONFIG = {
    modelBasePath: '<?= base_url('assets/models/') ?>',
    registerUrl: '<?= base_url('playground/register-face') ?>',
    clearUrl: '<?= base_url('playground/clear') ?>',
    submitUrl: '<?= base_url('playground/submit') ?>',
    csrfToken: '<?= csrf_token() ?>',
    csrfHash: '<?= csrf_hash() ?>',
    savedFaces: <?= json_encode($saved_faces) ?>,
  };
  </script>
  <script src="<?= base_url('assets/js/human.js') ?>"></script>
  <script src="<?= base_url('assets/js/human/playground.js') ?>"></script>
  <script>
  document.addEventListener("DOMContentLoaded", function() {
    const btnDark = document.getElementById('enable-dark-mode');
    const btnLight = document.getElementById('enable-light-mode');

    function updateUI(isDark) {
      isDark ? (btnDark?.classList.add('d-none'), btnLight?.classList.remove('d-none')) : (btnLight?.classList.add(
        'd-none'), btnDark?.classList.remove('d-none'));
    }
    updateUI(DarkReader.isEnabled());
    btnDark?.addEventListener('click', (e) => {
      e.preventDefault();
      DarkReader.enable({
        brightness: 100,
        contrast: 100,
        sepia: 5
      });
      localStorage.setItem('theme-preference', 'dark');
      updateUI(true);
    });
    btnLight?.addEventListener('click', (e) => {
      e.preventDefault();
      DarkReader.disable();
      localStorage.setItem('theme-preference', 'light');
      updateUI(false);
    });
  });
  </script>
</body>

</html>