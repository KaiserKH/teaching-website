<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$cfg = require __DIR__ . '/../config.php';
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="Private tuition by experienced teacher">
  <meta name="keywords" content="tuition, private tutor, tuition teacher, coaching">
  <meta property="og:title" content="Private Tuition">
  <meta property="og:description" content="Personalised tuition for school and competitive exams">
  <meta property="og:type" content="website">
  <link rel="stylesheet" href="/assets/css/style.min.css">
  <script defer src="/assets/js/main.min.js"></script>
  <title>Tuition</title>
</head>
<body class="light">
<header class="site-header">
  <div class="container">
    <a class="brand" href="/home">Teacher Name</a>
    <nav class="main-nav">
      <a href="/home">Home</a>
      <a href="/about">About</a>
      <a href="/courses">Courses</a>
      <a href="/schedule">Schedule</a>
      <a href="/gallery">Gallery</a>
      <a href="/blog">Blog</a>
      <a href="/testimonials">Testimonials</a>
      <a href="/contact">Contact</a>
      <form action="/search" method="get" class="search-form" style="display:inline">
        <input name="q" placeholder="Search..." aria-label="Search">
      </form>
    </nav>
    <div class="actions">
      <button id="theme-toggle">Toggle</button>
      <a class="cta" href="/admission">Join Now</a>
    </div>
  </div>
</header>
<!-- Floating WhatsApp -->
<a href="https://wa.me/919900000000?text=Hello%20Teacher" class="whatsapp-float" aria-label="WhatsApp" target="_blank">Chat</a>
<main class="container">
