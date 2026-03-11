<?php
/*
 * Per-page script injection point.
 * Views may set $inline_js for backward compatibility,
 * but the preferred pattern is a <script> block at the
 * bottom of each view file directly.
 */
if (isset($inline_js) && trim($inline_js) !== '') {
    echo '<script>' . $inline_js . '</script>';
}
?>
