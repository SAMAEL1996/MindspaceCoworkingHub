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
    </style>
    <div wire:ignore class="card image-wrapper bg-full bg-image bg-overlay bg-overlay-400 text-white border-radius-lg-top w-75 mx-auto" data-image-src="{{ asset('img/ongoing-bg.jpg') }}">
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