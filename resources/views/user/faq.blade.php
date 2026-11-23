@extends('layouts.app')

@section('title', 'FAQ')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/faq.css') }}">
@endpush
@section('content')
<div class="page-head">
    <div class="eyebrow">Bantuan</div>
    <h4>FAQ E-Complaint</h4>
    <p>Jawaban atas pertanyaan yang sering diajukan.</p>
</div>
<div class="panel-card">
    @forelse($faqs as $faq)
        <div class="panel-card" style="margin-bottom:12px; padding:0; overflow:hidden;">
            <button type="button" class="accordion" style="width:100%; text-align:left;" data-target="panel-{{ $faq->id }}" aria-expanded="false" aria-controls="panel-{{ $faq->id }}">
                {{ $faq->pertanyaan }} <i class="material-icons" aria-hidden="true">expand_more</i>
            </button>
            <div class="pannel" id="panel-{{ $faq->id }}">
                <p>{{ $faq->jawaban }}</p>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="material-icons" aria-hidden="true">quiz</i>
            <p>Belum ada FAQ.</p>
        </div>
    @endforelse
</div>
<script>
    document.querySelectorAll('.accordion').forEach(function (item) {
        item.addEventListener('click', function () {
            var panel = document.getElementById(this.dataset.target);
            var open = panel.style.display === 'block';
            panel.style.display = open ? 'none' : 'block';
            this.classList.toggle('active', !open);
            this.setAttribute('aria-expanded', String(!open));
        });
    });
</script>
@endsection
