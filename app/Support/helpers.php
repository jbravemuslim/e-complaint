<?php

// Helper global kecil yang dipakai lembur oleh view dashboard.

if (! function_exists('self_avatar')) {
    /** Inisial nama untuk avatar teks, maksimal 2 huruf. */
    function self_avatar(string $name, int $max = 2): string
    {
        $initials = collect(explode(' ', trim($name)))
            ->filter()
            ->take($max)
            ->map(fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('');

        return $initials !== '' ? $initials : '?';
    }
}
