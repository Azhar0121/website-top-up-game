@extends('layouts.customer')

@section('title', $page->title)
@section('meta_description', $page->title.' - TopUp Kilat')

@section('content')

    <section class="static-page-hero">
        <div class="container">
            <h1 class="mb-0">{{ $page->title }}</h1>
        </div>
    </section>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="static-page-card">
                    <div class="static-page-content">
                        {!! $page->content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
