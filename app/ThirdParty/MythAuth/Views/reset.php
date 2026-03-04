<?php
// \app\ThirdParty\MythAuth\Views\reset.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */
?>
<?= $this->extend($config->viewLayout) ?>
<?= $this->section('main') ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
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

  .reset-container {
    max-width: 500px;
    margin: 60px auto;
    padding: 20px;
  }

  .reset-card {
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(30, 58, 138, 0.1);
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 8px 32px rgba(30, 58, 138, 0.08);
  }

  .reset-header {
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 2px solid rgba(221, 165, 24, 0.2);
    padding-bottom: 20px;
  }

  .reset-header h1 {
    color: #1e3a8a;
    font-size: 24px;
    font-weight: 700;
    letter-spacing: -0.025em;
  }

  .reset-header p {
    color: #94a3b8;
    font-size: 13px;
    margin-top: 5px;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-group label {
    display: block;
    font-weight: 600;
    color: #1e3a8a;
    font-size: 14px;
    margin-bottom: 8px;
  }

  .form-group input[type="password"] {
    width: 100%;
    padding: 12px 15px;
    background-color: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.2s ease;
  }

  .form-group input[type="password"]:focus {
    outline: none;
    background-color: #fff;
    border-color: #1e3a8a;
    box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.1);
  }

  .form-group input[type="hidden"] {
    display: none;
  }

  .invalid-feedback {
    display: block;
    color: #ef4444;
    font-size: 12px;
    margin-top: 6px;
    font-weight: 500;
  }

  .form-group.has-error input {
    border-color: #ef4444;
    background-color: rgba(239, 68, 68, 0.03);
  }

  .password-strength {
    margin-top: 8px;
    padding: 10px;
    background: #f8f6f1;
    border-radius: 8px;
    font-size: 12px;
    color: #475569;
  }

  .submit-btn {
    width: 100%;
    padding: 12px;
    background-color: #1e3a8a;
    color: #ffffff;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 15px;
    letter-spacing: 0.3px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
    transition: all 0.2s ease;
    margin-top: 20px;
  }

  .submit-btn:hover {
    background-color: #172554;
    box-shadow: 0 6px 16px rgba(30, 58, 138, 0.4);
    transform: translateY(-2px);
  }

  .submit-btn:active {
    transform: translateY(0);
  }

  .message-block {
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 13px;
  }

  .message-block.error {
    background-color: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.2);
    color: #7f1d1d;
  }

  .message-block.success {
    background-color: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.2);
    color: #047857;
  }

  .info-box {
    background: linear-gradient(135deg, rgba(30, 58, 138, 0.05), rgba(221, 165, 24, 0.05));
    border-left: 4px solid #dda518;
    padding: 12px;
    border-radius: 8px;
    font-size: 13px;
    color: #1e3a8a;
    margin-bottom: 20px;
  }
  </style>
</head>

<body>

  <div class="reset-container">
    <div class="reset-card">
      <!-- Header -->
      <div class="reset-header">
        <h1>Reset Password</h1>
        <p>Create a new secure password for your account</p>
      </div>

      <!-- Messages -->
      <?php if (session('error')) : ?>
      <div class="message-block error">
        <?= session('error') ?>
      </div>
      <?php endif ?>

      <?php if (session('errors')) : ?>
      <div class="message-block error">
        <strong>Error:</strong>
        <ul style="margin: 5px 0 0 20px;">
          <?php foreach (session('errors') as $error) : ?>
          <li><?= $error ?></li>
          <?php endforeach ?>
        </ul>
      </div>
      <?php endif ?>

      <div class="info-box">
        <strong>💡 Tip:</strong> Use a mix of uppercase, lowercase, numbers, and special characters for a strong
        password.
      </div>

      <!-- Form -->
      <form action="<?= url_to('reset-password') ?>" method="post">
        <?= csrf_field() ?>

        <!-- Hidden token field - passed securely from session -->
        <input type="hidden" name="token" value="<?= old('token', $token ?? '') ?>">

        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" value="<?= isset($email) ? $email : '' ?>" disabled
            style="background-color: #e2e8f0; cursor: not-allowed; color: #64748b;">
          <small style="display: block; margin-top: 5px; color: #94a3b8; font-size: 12px;">Check your email for this
            reset link</small>
        </div>

        <div class="form-group <?php if (session('errors.password')) : ?>has-error<?php endif ?>">
          <label for="password">New Password</label>
          <input type="password" id="password" name="password" placeholder="Enter your new password" required>
          <?php if (session('errors.password')) : ?>
          <div class="invalid-feedback"><?= session('errors.password') ?></div>
          <?php endif ?>
        </div>

        <div class="form-group <?php if (session('errors.pass_confirm')) : ?>has-error<?php endif ?>">
          <label for="pass_confirm">Confirm Password</label>
          <input type="password" id="pass_confirm" name="pass_confirm" placeholder="Confirm your new password" required>
          <?php if (session('errors.pass_confirm')) : ?>
          <div class="invalid-feedback"><?= session('errors.pass_confirm') ?></div>
          <?php endif ?>
        </div>

        <button type="submit" class="submit-btn">Reset Password</button>
      </form>
    </div>
  </div>

</body>

</html>

<?= $this->endSection() ?>