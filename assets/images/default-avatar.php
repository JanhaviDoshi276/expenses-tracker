<?php
// Redirect to a generated SVG avatar if no file exists
header('Content-Type: image/svg+xml');
echo '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">
  <rect width="100" height="100" rx="50" fill="#6366f1"/>
  <circle cx="50" cy="38" r="18" fill="rgba(255,255,255,0.9)"/>
  <ellipse cx="50" cy="82" rx="28" ry="20" fill="rgba(255,255,255,0.9)"/>
</svg>';
