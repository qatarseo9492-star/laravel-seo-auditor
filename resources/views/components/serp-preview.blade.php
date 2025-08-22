@props(['title'=>'','url'=>'','description'=>''])
@php
  $t = mb_strlen($title)> 580 ? mb_substr($title,0,577).'…' : $title; /* ~580px */
  $d = mb_strlen($description)> 920 ? mb_substr($description,0,917).'…' : $description; /* ~920px */
@endphp
<div style="background:#111827;border:1px solid #1f2937;border-radius:14px;padding:12px">
  <div style="color:#1d4ed8;font-size:20px;line-height:1.2;margin-bottom:6px">{{ $t }}</div>
  <div style="color:#22c55e;font-size:14px">{{ $url }}</div>
  <div style="color:#d1d5db;font-size:14px;margin-top:6px">{{ $d }}</div>
</div>
