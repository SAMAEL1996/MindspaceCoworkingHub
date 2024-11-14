@extends('frontend.layouts.public')

@section('title', 'Flexi User')

@section('content')
    <section class="wrapper bg-soft-primary mb-5">
        <div class="container pt-10 pb-19 pt-md-14 pb-md-20 text-center">
            <div class="row">
                <div class="col-md-8 col-lg-7 col-xl-6 col-xxl-5 mx-auto mb-11">
                    <h1 class="display-1 mb-3">Hi {{ $flexi->name }}!</h1>
                    <p class="lead px-lg-7 px-xl-7 px-xxl-6">Thank you for subscribing to our Flexi Pass. <br>You can see your remaining time here.</p>
                </div>
            </div>
            <div class="card image-wrapper bg-full bg-image bg-overlay bg-overlay-400 text-white border-radius-lg-top w-50 mx-auto" data-image-src="{{ asset('img/bg3.jpg') }}">
                <div class="card-body p-9">
                    <div class="row align-items-center counter-wrapper text-center">
                        <div class="col-4 col-lg-4">
                            <h3 class="counter counter-lg text-white">{{ $time['hours'] }}</h3>
                            <p>Hours</p>
                        </div>
                        <div class="col-4 col-lg-4">
                            <h3 class="counter counter-lg text-white">:</h3>
                            <p></p>
                        </div>
                        <div class="col-4 col-lg-4">
                            <h3 class="counter counter-lg text-white">{{ $time['minutes'] }}</h3>
                            <p>Minutes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection