<?php
// \app\ThirdParty\MythAuth\Views\emails\forgot.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Password Reset Request</title>
  <style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, sans-serif;
    background-color: #fdfbf7;
    color: #334155;
    line-height: 1.6;
  }

  .email-container {
    max-width: 600px;
    margin: 0 auto;
    background: linear-gradient(135deg, #fdfbf7 0%, #f8f6f1 100%);
    padding: 40px 20px;
  }

  .email-card {
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(30, 58, 138, 0.1);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(30, 58, 138, 0.08);
  }

  .email-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #172554 100%);
    padding: 30px;
    text-align: center;
    color: white;
  }

  .email-header h1 {
    font-size: 26px;
    font-weight: 700;
    margin-bottom: 5px;
  }

  .email-header p {
    font-size: 14px;
    opacity: 0.9;
    margin: 0;
  }

  .email-content {
    padding: 30px;
  }

  .content-text {
    color: #475569;
    font-size: 15px;
    margin-bottom: 15px;
    line-height: 1.7;
  }

  .highlight-box {
    background: linear-gradient(135deg, rgba(30, 58, 138, 0.05), rgba(221, 165, 24, 0.05));
    border-left: 4px solid #dda518;
    padding: 15px 20px;
    border-radius: 8px;
    margin: 20px 0;
    font-size: 14px;
    color: #1e3a8a;
  }

  .cta-button {
    display: inline-block;
    background-color: #1e3a8a;
    color: #ffffff !important;
    padding: 12px 28px;
    border-radius: 10px;
    text-decoration: none !important;
    font-weight: 600;
    font-size: 15px;
    letter-spacing: 0.3px;
    margin-top: 10px;
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
    transition: all 0.2s ease;
    border: none;
    display: inline-block;
  }

  .cta-button:hover {
    background-color: #172554;
    box-shadow: 0 6px 16px rgba(30, 58, 138, 0.4);
  }

  .button-container {
    text-align: center;
    margin: 20px 0;
  }

  .divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(30, 58, 138, 0.1), transparent);
    margin: 20px 0;
  }

  .warning-section {
    background-color: rgba(239, 68, 68, 0.05);
    border: 1px solid rgba(239, 68, 68, 0.1);
    padding: 12px;
    border-radius: 8px;
    color: #7f1d1d;
    font-size: 13px;
  }

  .email-footer {
    background: #f8f6f1;
    padding: 20px 30px;
    text-align: center;
    font-size: 12px;
    color: #94a3b8;
  }
  </style>
</head>

<body>
  <div class="email-container">
    <div class="email-card">
      <!-- Header -->
      <div class="email-header">
        <h1>PresenSi</h1>
        <p>Si Pintar Urusan Presensi</p>
      </div>

      <!-- Main Content -->
      <div class="email-content">
        <p class="content-text">
          Hello,
        </p>

        <p class="content-text">
          We received a request to reset your password. If you made this request, use the button below to proceed.
        </p>

        <div class="button-container">
          <a href="<?= url_to('reset-password') . '?token=' . $hash . '&email=' . urlencode($user->email) ?>"
            class="cta-button"
            style="color: #ffffff !important; text-decoration: none; display: inline-block; background-color: #1e3a8a;">
            Reset Password
          </a>
        </div>

        <div class="divider"></div>

        <p class="content-text" style="font-size: 13px; color: #94a3b8;">
          <strong>If the button doesn't work,</strong> copy and paste this link into your browser:
        </p>

        <p
          style="font-size: 12px; color: #475569; word-break: break-all; background: #f1f5f9; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0;">
          <?= url_to('reset-password') . '?token=' . $hash . '&email=' . urlencode($user->email) ?>
        </p>

        <div class="divider"></div>

        <div class="warning-section">
          <strong>⚠️ Security:</strong> If you did not request a password reset, please ignore this email or contact
          support immediately.
        </div>
      </div>

      <!-- Footer -->
      <div class="email-footer">
        <p>This is an automated message, please do not reply to this email.</p>
        <p>&copy; <?= date('Y') ?> PresenSi. All rights reserved.</p>
      </div>
    </div>
  </div>
</body>

</html>