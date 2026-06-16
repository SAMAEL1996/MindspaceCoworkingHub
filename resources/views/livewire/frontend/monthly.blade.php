<div>
    <div class="row">
        <div class="col-md-8 col-lg-7 col-xl-6 col-xxl-5 mx-auto">
            <h1 class="display-2 mb-3">Welcome Monthyl User!</h1>
            <p class="lead px-lg-7 px-xl-7 px-xxl-6 pb-4">Enter the mobile number linked to your monthly pass.</p>
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
    @if($showMonthlyTime)
        <hr class="my-5">
        @if(!$monthly)
            <div class="row">
                <div class="col-lg-9 col-xl-8 mx-auto">
                    <figure class="mb-10"><img class="w-50 h-auto" src="{{ asset('img/404.png') }}" alt=""></figure>
                </div>
                <div class="col-lg-8 col-xl-9 col-xxl-6 mx-auto text-center">
                    <h1 class="mb-3">Oops! Monthly user not found.</h1>
                    <p class="lead mb-7 px-md-12 px-lg-5 px-xl-7">Try another mobile number.</p>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-10 col-lg-7 col-xl-8 col-xxl-7 mx-auto mb-5">
                    <h3 class="display-3 mb-3">Hi {{ $monthly->name }}!</h3>
                    <p class="lead px-lg-7 px-xl-7 px-xxl-6">Thank you for subscribing to our Monthly Pass. <br>You can see your time consumed and list of your visits here.</p>
                </div>
            </div>
            <div wire:key="monthly-checkin-wrapper-{{ $monthly->id ?? 'none' }}-{{ (int) $currentlyCheckin }}">
                @if($currentlyCheckin)
                    <div wire:key="monthly-checkin-poll-{{ $monthly->id ?? 'none' }}" wire:poll.1000ms="calculateTimeConsume">
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
                            <div class="row align-items-center text-center">
                                <p class="text-white">
                                    <em>Valid until {{ $monthly->date_finish_carbon->format(config('app.date_format')) }}</em>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="row mt-6" wire:key="monthly-visit-history-{{ $monthly->id ?? 'none' }}">
                <div class="col-12 col-xl-10 mx-auto">
                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <div class="px-6 pt-6 pb-3 text-start">
                                <h4 class="mb-1">Your visit history</h4>
                                <p class="text-muted mb-0">
                                    {{ $monthlyVisits->total() }} {{ \Illuminate\Support\Str::plural('record', $monthlyVisits->total()) }} found for your current monthly subscription.
                                </p>
                            </div>

                            @if($monthlyVisits->total())
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-3">Date</th>
                                                <th class="px-4 py-3">Time In</th>
                                                <th class="px-4 py-3">Time Out</th>
                                                <th class="px-4 py-3">Duration</th>
                                                {{-- <th class="px-4 py-3">Status</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($monthlyVisits as $visit)
                                                @php
                                                    $durationInMinutes = $visit->time_out
                                                        ? $visit->time_in_carbon->diffInMinutes($visit->time_out_carbon)
                                                        : null;
                                                @endphp
                                                <tr>
                                                    <td class="px-4 py-3">{{ $visit->time_in_carbon->format(config('app.date_format')) }}</td>
                                                    <td class="px-4 py-3">{{ $visit->time_in_carbon->format(config('app.time_format')) }}</td>
                                                    <td class="px-4 py-3">
                                                        {{ $visit->time_out ? $visit->time_out_carbon->format(config('app.time_format')) : 'Still checked in' }}
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        {{ !is_null($durationInMinutes) ? intdiv($durationInMinutes, 60) . 'h ' . ($durationInMinutes % 60) . 'm' : 'In progress' }}
                                                    </td>
                                                    {{-- <td class="px-4 py-3">
                                                        <span class="badge {{ $visit->status ? 'bg-primary' : 'bg-success' }}">
                                                            {{ $visit->status ? 'Checked In' : 'Checked Out' }}
                                                        </span>
                                                    </td> --}}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($monthlyVisits->hasPages())
                                    <div class="px-4 py-4">
                                        <x-pagination :paginator="$monthlyVisits" page-name="monthly-visits-page" livewire />
                                    </div>
                                @endif
                            @else
                                <div class="px-6 pb-6 text-start">
                                    <p class="mb-0 text-muted">No visit records found yet for this monthly user.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
