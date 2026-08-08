<?php

/**
 * Vercel Serverless Entry Point
 *
 * Vercel's PHP runtime requires a serverless function entry point.
 * This wrapper boots the CodeIgniter 4 application through the
 * standard public front controller.
 */

require_once __DIR__ . '/../public/index.php';