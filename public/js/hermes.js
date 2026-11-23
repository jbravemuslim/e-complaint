/* Gerak yang tenang: muncul-sekali-saat-scroll, angka berhitung,
   accordion, grafik. Vanilla JS, tanpa dependensi. */
(function () {
    'use strict';

    var reduced = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* 1. Reveal: diam di bawah, fade + naik begitu masuk layar. */
    function initReveal() {
        var els = document.querySelectorAll('.hx-reveal');
        if (!els.length) return;
        if (reduced || !('IntersectionObserver' in window)) {
            els.forEach(function (el) { el.classList.add('hx-visible'); });
            return;
        }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('hx-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });
        els.forEach(function (el) { io.observe(el); });
    }

    /* 2. Angka berhitung naik: [data-hx-count="123"] (+ data-hx-suffix). */
    function initCount() {
        var els = document.querySelectorAll('[data-hx-count]');
        if (!els.length) return;

        function run(el) {
            var target = parseInt(el.getAttribute('data-hx-count'), 10) || 0;
            var suffix = el.getAttribute('data-hx-suffix') || '';
            if (reduced) {
                el.textContent = target + suffix;
                return;
            }
            var start = null, dur = 1200;
            function frame(t) {
                if (!start) start = t;
                var p = Math.min((t - start) / dur, 1);
                var eased = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.round(target * eased) + suffix;
                if (p < 1) requestAnimationFrame(frame);
            }
            requestAnimationFrame(frame);
        }

        if (!('IntersectionObserver' in window)) {
            els.forEach(run);
            return;
        }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    run(entry.target);
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        els.forEach(function (el) { io.observe(el); });
    }

    /* 3. Accordion halus: tombol [data-hx-acc] + panel di sebelahnya. */
    function initAccordion() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-hx-acc]');
            if (!btn) return;
            var panel = document.getElementById(btn.getAttribute('data-hx-acc'));
            if (!panel) return;
            var open = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', String(!open));
            if (open) {
                panel.style.maxHeight = '0px';
            } else {
                panel.style.maxHeight = panel.scrollHeight + 'px';
            }
        });
    }

    /* 4. Grafik batang bulanan buatan sendiri: canvas[data-hx-chart]. */
    function initChart() {
        var cv = document.getElementById('hx-chart');
        if (!cv || !cv.getContext) return;
        var data;
        try {
            data = JSON.parse(cv.getAttribute('data-hx-chart'));
        } catch (e) { return; }
        if (!data || !data.labels || !data.labels.length) return;

        var dpr = window.devicePixelRatio || 1;
        var W = cv.clientWidth || 600, H = parseInt(cv.getAttribute('height'), 10) || 220;
        cv.width = W * dpr;
        cv.height = H * dpr;
        var ctx = cv.getContext('2d');
        ctx.scale(dpr, dpr);

        var max = Math.max.apply(null, data.masuk.concat(data.selesai).concat([1]));
        var padL = 8, padB = 26, padT = 12;
        var n = data.labels.length;
        var slot = (W - padL * 2) / n;
        var barW = Math.min(26, (slot - 18) / 2);
        var base = H - padB;

        ctx.font = '11px "JetBrains Mono", monospace';
        ctx.textAlign = 'center';

        data.labels.forEach(function (label, i) {
            var cx = padL + slot * i + slot / 2;
            [['masuk', '#0000f2', true], ['selesai', '#009688', false]].forEach(function (s) {
                var v = data[s[0]][i] || 0;
                var h = Math.round((H - padT - padB) * v / max);
                ctx.fillStyle = s[1];
                var x = s[2] ? cx - barW - 2 : cx + 2;
                if (ctx.roundRect) {
                    ctx.beginPath();
                    ctx.roundRect(x, base - h, barW, Math.max(h, 1), [4, 4, 0, 0]);
                    ctx.fill();
                } else {
                    ctx.fillRect(x, base - h, barW, Math.max(h, 1));
                }
            });
            ctx.fillStyle = '#5a5a72';
            ctx.fillText(label, cx, H - 8);
        });
    }

    /* 5. Partikel neon melayang: canvas.hx-particles (landing hero & panel auth). */
    function initParticles() {
        if (reduced) return;
        var canvases = document.querySelectorAll('canvas.hx-particles');
        if (!canvases.length) return;
        var COLORS = ['rgba(0,0,242,', 'rgba(0,229,255,', 'rgba(255,0,229,', 'rgba(139,92,246,'];
        var COUNT = 34;

        canvases.forEach(function (cv) {
            if (cv.dataset.hxReady) return;
            var ctx = cv.getContext && cv.getContext('2d');
            if (!ctx) return;
            cv.dataset.hxReady = '1';
            var dpr = Math.min(window.devicePixelRatio || 1, 2);
            var parts = [], raf = null, running = false;

            function rect() { return cv.getBoundingClientRect(); }
            function size() {
                var r = rect();
                if (!r.width || !r.height) return false;
                cv.width = Math.round(r.width * dpr);
                cv.height = Math.round(r.height * dpr);
                return true;
            }
            function spawn(fromBottom) {
                var r = rect();
                return {
                    x: Math.random() * r.width,
                    y: fromBottom ? r.height + 8 : Math.random() * r.height,
                    r: 1 + Math.random() * 2.2,
                    vy: .12 + Math.random() * .35,
                    vx: (Math.random() - .5) * .18,
                    a: .25 + Math.random() * .45,
                    c: COLORS[Math.floor(Math.random() * COLORS.length)]
                };
            }
            function step() {
                if (!running) { raf = null; return; }
                var r = rect();
                ctx.clearRect(0, 0, cv.width, cv.height);
                ctx.save();
                ctx.scale(dpr, dpr);
                parts.forEach(function (p, i) {
                    p.y -= p.vy;
                    p.x += p.vx;
                    if (p.y < -10 || p.x < -10 || p.x > r.width + 10) parts[i] = spawn(true);
                    ctx.beginPath();
                    ctx.fillStyle = p.c + p.a + ')';
                    ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                    ctx.fill();
                });
                ctx.restore();
                raf = requestAnimationFrame(step);
            }
            function start() {
                if (running) return;
                running = true;
                raf = requestAnimationFrame(step);
            }
            function stop() {
                running = false;
                if (raf) { cancelAnimationFrame(raf); raf = null; }
            }

            if (!size()) return;
            for (var i = 0; i < COUNT; i++) parts.push(spawn(false));
            window.addEventListener('resize', size);
            document.addEventListener('visibilitychange', function () {
                if (document.hidden) { stop(); } else { start(); }
            });
            start();
        });
    }

    /* 6. Tilt 3D: [data-hx-tilt] (desktop dengan hover, hormati reduced-motion). */
    function initTilt() {
        if (reduced) return;
        if (window.matchMedia && window.matchMedia('(hover: none)').matches) return;
        var els = document.querySelectorAll('[data-hx-tilt]');
        if (!els.length) return;
        var MAX = 8;

        els.forEach(function (el) {
            if (el.dataset.hxTilt) return;
            el.dataset.hxTilt = '1';
            el.classList.add('hx-tilt');
            el.addEventListener('mousemove', function (e) {
                if (el.classList.contains('hx-reveal') && !el.classList.contains('hx-visible')) return;
                var r = el.getBoundingClientRect();
                var px = (e.clientX - r.left) / r.width - .5;
                var py = (e.clientY - r.top) / r.height - .5;
                el.style.transform = 'perspective(800px) rotateX(' + (-py * MAX).toFixed(2) +
                    'deg) rotateY(' + (px * MAX).toFixed(2) + 'deg) translateY(-3px)';
            });
            el.addEventListener('mouseleave', function () {
                el.style.transform = '';
            });
        });
    }

    /* 7. Orbit 3D: objek sekolah berputar mengelilingi logo.
       Depth fake-3D: skala + opacity + z-index mengikuti cos(sudut).
       Pelan: 1 putaran penuh = 24 detik. */
    function initOrbit3D() {
        if (reduced) return;
        var zone = document.querySelector('.hero');
        var ring = document.querySelector('.hx-orbit-ring');
        if (!zone || !ring || ring.dataset.hxOrbit) return;
        ring.dataset.hxOrbit = '1';
        ring.classList.add('hx-orbit-live');

        var tiles = Array.prototype.slice.call(ring.querySelectorAll('.hx-object'));
        var TAU = Math.PI * 2, PERIOD = 24, Y_SPREAD = 0.25;
        var bobPhase = [0, 1.3, 2.6, 3.9, 5.2, 6.5];
        var mx = 0, my = 0, smx = 0, smy = 0;
        var elapsed = 0, last = null, raf = null, running = false, inView = true;

        function visibleTiles() {
            return tiles.filter(function (t) { return t.offsetWidth > 0; });
        }

        function frame(now) {
            raf = null;
            if (!running) return;
            var dt = last === null ? 0.016 : Math.min((now - last) / 1000, 0.05);
            last = now;
            elapsed += dt;
            smx += (mx - smx) * 0.06;
            smy += (my - smy) * 0.06;

            var r = ring.getBoundingClientRect();
            var R = Math.min(r.width * 0.42, r.height * 0.52);
            var vis = visibleTiles();
            var n = vis.length || 1;

            for (var i = 0; i < n; i++) {
                var a = (i / n) * TAU + (elapsed / PERIOD) * TAU;
                var d = Math.cos(a);                 /* 1 = paling depan, -1 = paling belakang */
                var depth = (d + 1) / 2;
                var x = Math.sin(a) * R + smx * 14;
                var y = d * R * (Y_SPREAD + smy * 0.08) +
                        Math.sin(elapsed * 2.2 + bobPhase[i % 6]) * 5 + smy * 10;
                var tile = vis[i];
                tile.style.transform = 'translate(-50%,-50%) translate3d(' +
                    x.toFixed(1) + 'px,' + y.toFixed(1) + 'px,0) scale(' +
                    (0.78 + 0.37 * depth).toFixed(3) + ')';
                tile.style.zIndex = String(Math.round(d * 50));
                tile.style.opacity = (0.55 + 0.45 * depth).toFixed(2);
            }
            raf = requestAnimationFrame(frame);
        }

        function start() {
            if (running || document.hidden || !inView) return;
            running = true; last = null;
            raf = requestAnimationFrame(frame);
        }
        function stop() {
            running = false;
            if (raf) { cancelAnimationFrame(raf); raf = null; }
        }

        zone.addEventListener('mousemove', function (e) {
            var r = zone.getBoundingClientRect();
            mx = (e.clientX - r.left) / r.width - .5;
            my = (e.clientY - r.top) / r.height - .5;
        });
        zone.addEventListener('mouseleave', function () { mx = 0; my = 0; });
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) { stop(); } else { start(); }
        });
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function (entries) {
                inView = entries[0].isIntersecting;
                if (inView) { start(); } else { stop(); }
            }, { threshold: 0.05 }).observe(zone);
        }
        start();
    }

    document.addEventListener('DOMContentLoaded', function () {
        initReveal();
        initCount();
        initAccordion();
        initChart();
        initParticles();
        initTilt();
        initOrbit3D();
        document.querySelectorAll('.modal').forEach(function (m) {
            m.classList.add('hx-modal');
        });
    });
})();
