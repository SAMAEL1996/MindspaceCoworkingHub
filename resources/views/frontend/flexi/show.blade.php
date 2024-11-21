@extends('frontend.layouts.public')

@section('title', 'Flexi User')

@section('content')
    <section class="wrapper bg-soft-primary mb-5">
        <div class="container pt-10 pb-9 pt-md-7 pb-md-10 text-center">
            @if(request()->has('contact'))
                <div class="row">
                    <div class="col-md-8 col-lg-7 col-xl-6 col-xxl-5 mx-auto mb-11">
                        <h1 class="display-1 mb-3">Hi {{ $flexi->name }}!</h1>
                        <p class="lead px-lg-7 px-xl-7 px-xxl-6">Thank you for subscribing to our Flexi Pass. <br>You can see your remaining time here.</p>
                    </div>
                </div>
                <div class="card image-wrapper bg-full bg-image bg-overlay bg-overlay-400 text-white border-radius-lg-top w-75 mx-auto" data-image-src="{{ asset('img/bg3.jpg') }}">
                    <div class="card-body p-9">
                        <div class="row align-items-center counter-wrapper text-center">
                            <div class="col-5 col-lg-5">
                                <h3 class="counter counter-lg text-white">{{ $time['hours'] }}</h3>
                                <p>Hours</p>
                            </div>
                            <div class="col-2 col-lg-2">
                                <h3 class="counter counter-lg text-white">:</h3>
                                <p></p>
                            </div>
                            <div class="col-5 col-lg-5">
                                <h3 class="counter counter-lg text-white">{{ $time['minutes'] }}</h3>
                                <p>Minutes</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row py-3">
                    <div class="col-md-8 col-lg-7 col-xl-6 col-xxl-5 mx-auto mb-11">
                        <p class="lead px-lg-7 px-xl-7 px-xxl-6 fs-sm fst-italic"><b>Note:</b> If you are currently checked in,<br>your remaining time will be updated after check-out.</p>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-8 col-lg-7 col-xl-6 col-xxl-5 mx-auto">
                        <form action="{{ route('flexi.remaining-time') }}" method="post">
                            @csrf
                            <h1 class="display-2 mb-3">Welcome Flexi User!</h1>
                            <p class="lead px-lg-7 px-xl-7 px-xxl-6 pb-4">Enter the mobile number linked to your flexi pass.</p>
                            <div class="form-floating mb-4">
                                <input id="form_name" type="text" name="contact" class="form-control" placeholder="Jane" required>
                                <label for="form_name">Contact Number *</label>
                                @error('contact')
                                    <div class="fst-italic text-danger"> {{ $message }} </div>
                                @enderror
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary rounded-pill btn-send mb-3">Check your time!</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection