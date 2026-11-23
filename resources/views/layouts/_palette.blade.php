@php
$hxRole = auth()->user()->role;
$hxCommands = [];
if ($hxRole === 'user') {
    $hxCommands = [
        ['label' => 'Ajukan Keluhan', 'icon' => 'edit_note', 'href' => route('dashboard')],
        ['label' => 'Riwayat Keluhan', 'icon' => 'history', 'href' => route('riwayat')],
        ['label' => 'FAQ', 'icon' => 'quiz', 'href' => route('faq')],
    ];
} elseif ($hxRole === 'admin') {
    $hxCommands = [
        ['label' => 'Dashboard Admin', 'icon' => 'grid_view', 'href' => route('admin.dashboard')],
        ['label' => 'Complain Proses', 'icon' => 'report', 'href' => route('admin.complains')],
        ['label' => 'Ditanggapi', 'icon' => 'task_alt', 'href' => route('admin.responses')],
    ];
} else {
    $hxCommands = [
        ['label' => 'Dashboard Superadmin', 'icon' => 'grid_view', 'href' => route('superadmin.dashboard')],
        ['label' => 'Complain Proses', 'icon' => 'report', 'href' => route('superadmin.complains')],
        ['label' => 'Ditanggapi', 'icon' => 'task_alt', 'href' => route('superadmin.responses')],
        ['label' => 'Kelola User', 'icon' => 'account_box', 'href' => route('superadmin.users')],
        ['label' => 'Kelola Admin', 'icon' => 'security', 'href' => route('superadmin.admins')],
        ['label' => 'Kelola Tujuan', 'icon' => 'add_circle', 'href' => route('superadmin.tujuans')],
        ['label' => 'Kelola Jenis', 'icon' => 'add_box', 'href' => route('superadmin.jenis')],
        ['label' => 'Kelola FAQ', 'icon' => 'quiz', 'href' => route('superadmin.faqs')],
        ['label' => 'Laporan', 'icon' => 'book', 'href' => route('superadmin.laporan')],
    ];
}
@endphp
<div id="hx-palette" class="hx-palette" hidden>
    <div class="hx-palette-box">
        <input id="hx-palette-input" type="text" placeholder="Ketik perintah atau halaman… (Esc tutup)" autocomplete="off">
        <ul id="hx-palette-list"></ul>
    </div>
</div>
<script>
(function () {
    var COMMANDS = @json($hxCommands);
    var root = document.getElementById('hx-palette');
    var input = document.getElementById('hx-palette-input');
    var list = document.getElementById('hx-palette-list');
    if (!root || !input || !list) return;
    var active = 0, shown = COMMANDS.slice();

    function render() {
        list.innerHTML = '';
        if (!shown.length) {
            var empty = document.createElement('li');
            empty.className = 'hx-palette-empty';
            empty.textContent = 'Tidak ada perintah yang cocok.';
            list.appendChild(empty);
            return;
        }
        shown.forEach(function (cmd, i) {
            var li = document.createElement('li');
            if (i === active) li.className = 'active';
            var a = document.createElement('a');
            a.href = cmd.href;
            var icon = document.createElement('i');
            icon.className = 'material-icons';
            icon.textContent = cmd.icon;
            a.appendChild(icon);
            a.appendChild(document.createTextNode(cmd.label));
            li.appendChild(a);
            list.appendChild(li);
        });
    }

    function open() { root.hidden = false; input.value = ''; active = 0; shown = COMMANDS.slice(); render(); input.focus(); }
    function close() { root.hidden = true; }
    function isOpen() { return !root.hidden; }

    input.addEventListener('input', function () {
        var q = input.value.toLowerCase();
        shown = COMMANDS.filter(function (c) { return c.label.toLowerCase().indexOf(q) !== -1; });
        active = 0;
        render();
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowDown') { e.preventDefault(); active = Math.min(active + 1, shown.length - 1); render(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); active = Math.max(active - 1, 0); render(); }
        else if (e.key === 'Enter' && shown[active]) { window.location.href = shown[active].href; }
        else if (e.key === 'Escape') { close(); }
    });

    root.addEventListener('click', function (e) { if (e.target === root) close(); });

    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); isOpen() ? close() : open(); }
        else if (e.key === 'Escape' && isOpen()) { close(); }
    });

    render();
})();
</script>
