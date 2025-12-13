<?php
// admin/includes/admin-helpers.php
// One place for shared helpers. Safe to include multiple times.

if (!function_exists('h')) {
  function h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
  }
}

if (!function_exists('money')) {
  function money($n) {
    return number_format((float)$n, 2);
  }
}

if (!function_exists('statusNice')) {
  function statusNice($s) {
    return ucwords(str_replace('_', ' ', (string)$s));
  }
}

if (!function_exists('badge')) {
  function badge($status) {
    return match((string)$status) {
      'completed'        => 'bg-success',
      'out_for_delivery' => 'bg-primary',
      'preparing'        => 'bg-warning text-dark',
      'cancelled'        => 'bg-danger',
      default            => 'bg-secondary',
    };
  }
}

if (!function_exists('nextStatus')) {
  function nextStatus($s) {
    return match((string)$s) {
      'pending'          => 'preparing',
      'preparing'        => 'out_for_delivery',
      'out_for_delivery' => 'completed',
      default            => null,
    };
  }
}

if (!function_exists('fmtDT')) {
  function fmtDT($dt) {
    if (!$dt) return "—";
    $ts = strtotime($dt);
    return $ts ? date("M d, Y • g:i A", $ts) : (string)$dt;
  }
}
