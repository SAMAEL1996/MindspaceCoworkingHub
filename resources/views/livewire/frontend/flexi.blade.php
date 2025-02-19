<div>
    <div class="row">
        <div class="col-md-8 col-lg-7 col-xl-6 col-xxl-5 mx-auto">
            <h1 class="display-2 mb-3">Welcome Flexi User!</h1>
            <p class="lead px-lg-7 px-xl-7 px-xxl-6 pb-4">Enter the mobile number linked to your flexi pass.</p>
            <div class="form-floating mb-4">
                <input id="form_name" type="text" class="form-control" required wire:model="contact">
                <label for="form_name">Contact Number *</label>
                @error('contact')
                    <div class="fst-italic text-danger"> {{ $message }} </div>
                @enderror
            </div>
            <div class="col-12 text-center">
                <button wire:click="checkTime" class="btn btn-primary rounded-pill btn-send mb-3" wire:loading.attr="disabled">Check Time</button>
                <br>
                <div wire:loading wire:target="checkTime">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($showFlexiTime)
        <hr class="my-5">
        @if(!$flexi)
            <div class="row">
                <div class="col-lg-9 col-xl-8 mx-auto">
                    <figure class="mb-10"><img class="w-50 h-auto" src="{{ asset('img/404.png') }}" alt=""></figure>
                </div>
                <div class="col-lg-8 col-xl-7 col-xxl-6 mx-auto text-center">
                    <h1 class="mb-3">Oops! Flexi user not found.</h1>
                    <p class="lead mb-7 px-md-12 px-lg-5 px-xl-7">Try another mobile number.</p>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-8 col-lg-7 col-xl-6 col-xxl-5 mx-auto mb-5">
                    <h3 class="display-3 mb-3">Hi {{ $flexi->name }}!</h3>
                    <p class="lead px-lg-7 px-xl-7 px-xxl-6">Thank you for subscribing to our Flexi Pass. <br>You can see your remaining time here.</p>
                </div>
            </div>
            @if($flexi->is_active)
                <div wire:poll.1000ms="calculateRemainingTime">
                    <style>
                        .blink {
                            animation: blink-animation 1s step-start infinite;
                        }

                        @keyframes blink-animation {
                            50% {
                                opacity: 0;
                            }
                            100% {
                                opacity: 1;
                            }
                        }

                        .bg-overlay {
                            background-image: url('/img/ongoing-bg.jpg') !important;
                            background-size: cover;
                            background-position: center;
                        }
                    </style>
                    <div class="card image-wrapper bg-full bg-image bg-overlay bg-overlay-400 text-white border-radius-lg-top w-75 mx-auto">
                        <div class="card-body p-9">
                            <div class="row align-items-center counter-wrapper text-center">
                                <div class="col-5 col-lg-5">
                                    <h3 class="counter counter-lg text-white">{{ $time['hours'] }}</h3>
                                    <p>Hours</p>
                                </div>
                                <div class="col-2 col-lg-2">
                                    <h3 class="counter counter-lg text-white blink">:</h3>
                                    <p></p>
                                </div>
                                <div class="col-5 col-lg-5">
                                    <h3 class="counter counter-lg text-white">{{ $time['minutes'] }}</h3>
                                    <p>Minutes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row py-3">
                    <div class="col-md-8 col-lg-7 col-xl-6 col-xxl-5 mx-auto mb-11">
                        <span class="badge badge-lg bg-green rounded-pill">Active</span>
                        <p class="lead px-lg-7 px-xl-7 px-xxl-6 fs-sm fst-italic"><b>Note:</b> You are currently checked in,<br>your remaining time will be updated from time to time.</p>
                    </div>
                </div>
            @else
                <style>
                    .blink {
                        animation: blink-animation 1s step-start infinite;
                    }

                    @keyframes blink-animation {
                        50% {
                            opacity: 0;
                        }
                        100% {
                            opacity: 1;
                        }
                    }

                    .bg-overlay {
                        background-image: url('/img/bg3.jpg') !important;
                        background-size: cover;
                        background-position: center;
                    }
                </style>
                <div class="card image-wrapper bg-full bg-image bg-overlay bg-overlay-400 text-white border-radius-lg-top w-75 mx-auto">
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
                        <span class="badge badge-lg bg-ash text-white rounded-pill">Inactive</span>
                        <p class="lead px-lg-7 px-xl-7 px-xxl-6 fs-sm fst-italic"><b>Note:</b> If you are currently checked in,<br>your remaining time will be updated from time to time.</p>
                    </div>
                </div>
            @endif
        @endif
    @endif
</div>
