@extends('layouts.customer')

@section('title', 'FAQ - Pertanyaan Umum')

@section('meta_description', 'Pertanyaan yang sering diajukan seputar top up game di TopUp Kilat.')

@section('content')

<section class="static-page-hero">
    <div class="container">
        <h1 class="mb-2">Pertanyaan Umum</h1>
        <p class="mb-0 text-light-muted">
            Belum ketemu jawabannya? Hubungi CS kami lewat halaman yang tersedia
        </p>
    </div>
</section>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            @if ($faqs->isEmpty())

                <div class="static-page-card text-center text-muted py-5">
                    Belum ada FAQ tersedia saat ini.
                </div>

            @else

                <div class="accordion faq-accordion" id="faqAccordion">

                    @foreach ($faqs as $i => $faq)

                        <div class="accordion-item">

                            <h2 class="accordion-header" id="heading-{{ $faq->id }}">

                                <button
                                    class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#faq-{{ $faq->id }}"
                                    aria-controls="faq-{{ $faq->id }}"
                                    aria-expanded="{{ $i === 0 ? 'true' : 'false' }}"
                                >
                                    {{ $faq->question }}
                                </button>

                            </h2>

                            <div
                                id="faq-{{ $faq->id }}"
                                class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}"
                                aria-labelledby="heading-{{ $faq->id }}"
                                data-bs-parent="#faqAccordion"
                            >

                                <div class="accordion-body">
                                    {{ $faq->answer }}
                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>
    </div>
</div>

@endsection