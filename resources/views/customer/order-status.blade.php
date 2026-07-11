@extends('layouts.customer')

@section('title', 'Cek Transaksi')

@section('content')

    <div class="container py-5" style="max-width: 640px;" id="order-status-app" data-invoice="{{ $invoice }}">

        <h1 class="section-heading mb-4 text-center">Cek Status Transaksi</h1>

        <form id="lookup-form" class="hero-search d-flex align-items-center mx-auto mb-4 lookup-form-light" role="search">
            <input type="text" id="invoice-input" placeholder="Masukkan nomor invoice, contoh: INV-20260708-XXXXXX" value="{{ $invoice }}">
            <button type="submit">Cek</button>
        </form>

        <div id="order-result"></div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/pages/order-status.js') }}"></script>
@endpush
