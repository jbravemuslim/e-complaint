@extends('layouts.app')

@section('title', 'Riwayat Keluhan')
@section('content')
<div class="page-head">
    <div class="eyebrow">Riwayat {{ $complains->total() }} Keluhan</div>
    <h4>Riwayat Keluhan</h4>
    <p>Pantau status dan tanggapan atas keluhanmu.</p>
</div>
<div class="panel-card flush">
    @if($complains->isEmpty())
        <div class="empty-state">
            <i class="material-icons">inbox</i>
            <p>Belum ada keluhan. <a href="{{ route('dashboard') }}">Ajukan keluhan pertamamu</a>.</p>
        </div>
    @else
    <table class="clean-table responsive-table hx-row">
        <thead>
            <tr>
                <th>No</th>
                <th>Antrian</th>
                <th>Nama</th>
                <th>Tanggal Masuk</th>
                <th>Status</th>
                <th>Opsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($complains as $i => $complain)
                <tr>
                    <td>{{ $complains->firstItem() + $i }}</td>
                    <td>{{ $complain->id }}</td>
                    <td>{{ $complain->user->name }}</td>
                    <td>{{ $complain->tgl_keluhan->format('Y-m-d') }}</td>
                    <td><span class="status-pill {{ $complain->status }}">{{ $complain->status }}</span></td>
                    <td class="hx-actions">
                        <a class="hx-btn-act" href="{{ route('complains.show', $complain) }}"><i class="material-icons" aria-hidden="true">visibility</i>Detail</a>
                        <form method="POST" action="{{ route('complains.destroy', $complain) }}" class="hx-btn-act-form" onsubmit="return confirm('Anda Yakin Ingin Menghapus?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="hx-btn-act danger"><i class="material-icons" aria-hidden="true">delete_outline</i>Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:16px;">
        {{ $complains->links() }}
    </div>
    @endif
</div>
@endsection
