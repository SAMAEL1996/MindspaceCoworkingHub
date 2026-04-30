@extends('frontend.layouts.app')

@section('title', 'Contact')

@section('content')
    <section class="wrapper image-wrapper bg-image bg-overlay bg-overlay-300" data-image-src="{{ asset('img/bg16.png') }}">
        <div class="container pt-17 pb-19 pt-md-18 pb-md-17 text-center">
            <div class="row">
                <div class="col-lg-8 col-xl-7 col-xxl-6 mx-auto" data-cues="slideInDown" data-group="page-title">
                    <h1 class="display-1 text-white fs-60 px-md-15 px-lg-0 mb-0">
                        MINDSPACE
                    </h1>
                    <h4 class="display-3 text-white mb-5">Coworking & Study Hub</h4>
                    <p class="lead fs-lg text-white lh-sm mb-7 mx-md-6 mx-lg-5">
                        Whether you’re a freelancer, entrepreneur, remote worker, or student, our coworking space offers the
                        perfect environment to boost your focus and productivity.
                    </p>
                </div>
                <!-- /column -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
        <div class="overflow-hidden">
            <div class="divider text-light mx-n2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 60">
                    <path fill="currentColor" d="M0,0V60H1440V0A5771,5771,0,0,1,0,0Z" />
                </svg>
            </div>
        </div>
    </section>

    <section class="wrapper bg-light">
        <div class="container py-14 py-md-17">
            <div class="row">
                <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2 mx-auto text-center">
                    <h2 class="fs-15 text-uppercase text-muted mb-3">Our Passes</h2>
                    <h3 class="display-4 mb-10 px-xl-10 px-xxl-15">Find the perfect pass for your daily grind, creative
                        bursts, or focused sprints.</h3>
                </div>
            </div>
        </div>
        <div class="container py-14 py-md-17">
            <div class="card shadow-lg mt-n16 mt-md-n21 mb-15 mb-md-14">
                <div class="row gx-0">
                    <div class="col-lg-6 image-wrapper bg-image bg-cover rounded d-none d-md-block"
                        data-image-src="https://ik.imagekit.io/wow2navhj/Mindspace/studying-01.jpg?updatedAt=1752539051893"
                        data-cues="fadeIn">
                    </div>
                    <div class="col-lg-6">
                        <div class="p-10 p-md-11 p-lg-13">
                            <h2 class="display-4 mb-3" data-cues="slideInDown">Daily Pass</h2>
                            <p class="lead fs-lg fst-italic">Pay only for the time you need.</p>
                            <p>Ideal for short visits or spontaneous study sessions, it’s perfect for students or
                                professionals who only need a few hours of focused time without any commitment.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container py-14 py-md-17">
            <div class="card shadow-lg mt-n16 mt-md-n21 mb-15 mb-md-14">
                <div class="row gx-0">
                    <div class="col-lg-6">
                        <div class="p-10 p-md-11 p-lg-13" data-cues="slideInDown">
                            <h2 class="display-4 mb-3">Flexi Pass</h2>
                            <p class="lead fs-lg fst-italic">Your hours, your schedule.</p>
                            <p>Purchase hours in advance and use them whenever it suits you. It's great for people with
                                unpredictable routines who still want a productive environment.</p>
                        </div>
                    </div>
                    <div class="col-lg-6 image-wrapper bg-image bg-cover rounded d-none d-md-block"
                        data-image-src="https://ik.imagekit.io/wow2navhj/Mindspace/studying-04.jpg?updatedAt=1752539053018"
                        data-cues="fadeIn">
                    </div>
                </div>
            </div>
        </div>
        <div class="container py-14 py-md-17">
            <div class="card shadow-lg mt-n16 mt-md-n21 mb-15 mb-md-14">
                <div class="row gx-0">
                    <div class="col-lg-6 image-wrapper bg-image bg-cover rounded d-none d-md-block"
                        data-image-src="https://ik.imagekit.io/wow2navhj/Mindspace/studying-03.jpg?updatedAt=1752539051763"
                        data-cues="fadeIn">
                    </div>
                    <div class="col-lg-6">
                        <div class="p-10 p-md-11 p-lg-13" data-cues="slideInDown">
                            <h2 class="display-4 mb-3">Monthly Pass</h2>
                            <p class="lead fs-lg fst-italic">Work or study without limits.</p>
                            <p>Enjoy full access every day of the month. It’s the best choice for regular users who want a
                                consistent and reliable workspace.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container py-14 py-md-17">
            <div class="card shadow-lg mt-n16 mt-md-n21 mb-15 mb-md-14">
                <div class="row gx-0">
                    <div class="col-lg-6">
                        <div class="p-10 p-md-11 p-lg-13" data-cues="slideInDown">
                            <h2 class="display-4 mb-3">Conference</h2>
                            <p class="lead fs-lg fst-italic">Private space, professional feel.</p>
                            <p>Allows you to reserve dedicated rooms for meetings, group discussions, or small events. Ideal
                                for those needing a quiet and formal setting for collaboration.</p>
                        </div>
                    </div>
                    <div class="col-lg-6 image-wrapper bg-image bg-cover rounded d-none d-md-block"
                        data-image-src="https://ik.imagekit.io/wow2navhj/Mindspace/group-meeting-02.jpg?updatedAt=1752539052466"
                        data-cues="fadeIn">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="wrapper bg-white">
        <div class="container py-7">
            <div class="row mb-8 text-center">
                <div class="col-lg-10 col-xl-9 col-xxl-8 mx-auto">
                    <h2 class="fs-15 text-uppercase text-muted mb-3">What We Offer</h2>
                    <h3 class="display-4">Everything you need for a productive, focused, and comfortable work or study
                        experience.</h3>
                </div>
            </div>
            <div class="row gx-md-8 gy-8 mb-15 mb-md-17 text-center">
                <div class="col-md-6 col-lg-3">
                    <div class="px-md-3 px-lg-0 px-xl-3">
                        <img src="{{ asset('img/wifi.svg') }}"
                            class="svg-inject icon-svg icon-svg-md solid-mono text-grape mb-5" alt="" />
                        <h4>Free WiFi</h4>
                        <p class="mb-2">Fast and reliable internet connection</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="px-md-3 px-lg-0 px-xl-3">
                        <img src="{{ asset('img/office-chair.svg') }}"
                            class="svg-inject icon-svg icon-svg-md solid-mono text-grape mb-5" alt="" />
                        <h4>Comfortable seats</h4>
                        <p class="mb-2">Ergonomic and cozy seating.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="px-md-3 px-lg-0 px-xl-3">
                        <img src="{{ asset('img/coffee-maker.svg') }}"
                            class="svg-inject icon-svg icon-svg-md solid-mono text-grape mb-5" alt="" />
                        <h4>Free Refreshments</h4>
                        <p class="mb-2">Unlimited coffee, tea, and light snacks</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="px-md-3 px-lg-0 px-xl-3">
                        <img src="{{ asset('img/24-hour.svg') }}"
                            class="svg-inject icon-svg icon-svg-md solid-mono text-grape mb-5" alt="" />
                        <h4>Flexible Hours</h4>
                        <p class="mb-2">24 hours open except Sundays (until 7pm only)</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="wrapper bg-gray">
        <div class="container py-7 py-md-8">
            <div class="row mb-8 text-center">
                <div class="col-lg-10 col-xl-9 col-xxl-8 mx-auto">
                    <h2 class="fs-15 text-uppercase text-muted mb-3">Track Your Stay</h2>
                    <h3 class="display-4">See how long you've stayed, monitor your usage, or manage your remaining hours
                        based on your pass type.</h3>
                </div>
            </div>
            <div class="row gx-lg-8 gx-xl-12 gy-10 mb-14 mb-md-17 align-items-center">
                <div class="col-lg-6 position-relative">
                    <div class="shape bg-dot leaf rellax w-17 h-18" data-rellax-speed="1"
                        style="bottom: -2rem; left: -0.7rem;"></div>
                    <figure class="rounded"><img src="{{ asset('img/time-track.jpg') }}"
                            srcset="{{ asset('img/time-track.jpg') }} 2x" alt="" /></figure>
                </div>
                <div class="col-lg-6 col-xxl-5">
                    <div class="d-flex flex-row mb-5">
                        <div>
                            <span class="icon btn btn-circle btn-soft-primary pe-none me-5"><span class="number fs-18"><i
                                        class="uil uil-bullseye"></i></span></span>
                        </div>
                        <div>
                            <h4 class="mb-1"><a href="" class="hover more">Daily Pass</a></h4>
                            <p class="mb-0">Track how many hours you’ve used today and see if you’re close to your limit.
                            </p>
                        </div>
                    </div>
                    <div class="d-flex flex-row mb-5">
                        <div>
                            <span class="icon btn btn-circle btn-soft-primary pe-none me-5"><span class="number fs-18"><i
                                        class="uil uil-bullseye"></i></span></span>
                        </div>
                        <div>
                            <h4 class="mb-1"><a href="{{ route('flexi.remaining-time') }}" class="hover more">Flexi
                                    Pass</a></h4>
                            <p class="mb-0">View your remaining hours, usage history, and expiration-free balance.</p>
                        </div>
                    </div>
                    <div class="d-flex flex-row">
                        <div>
                            <span class="icon btn btn-circle btn-soft-primary pe-none me-5"><span class="number fs-18"><i
                                        class="uil uil-bullseye"></i></span></span>
                        </div>
                        <div>
                            <h4 class="mb-1"><a href="" class="hover more">Monthly Pass</a></h4>
                            <p class="mb-0">Track your daily visits and get a summary of your access history.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--
        REVIEWS
    -->
    <section class="wrapper bg-soft-primary">
        <div class="container py-7 py-md-8">
            <div class="row">
                <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2 mx-auto text-center">
                    <h2 class="fs-15 text-uppercase text-muted mb-3">Customer Reviews</h2>
                    <h3 class="display-4 mb-10 px-xl-10 px-xxl-15">Don't take our word for it. See what customers are
                        saying about us.</h3>
                </div>
            </div>
            <div class="grid">
                <div class="row isotope gy-6">
                    <div class="item col-md-6 col-xl-4">
                        <div class="card shadow-lg">
                            <div class="card-body">
                                <span class="ratings five mb-3"></span>
                                <blockquote class="icon mb-0">
                                    <p>“Perfect working area for WFH or Hybrid.”</p>
                                    <div class="blockquote-details">
                                        <span class="avatar bg-pale-primary text-primary w-12 h-12">
                                            <span>MC</span>
                                        </span>
                                        <!--<img class="rounded-circle w-12" src="./assets/img/avatars/te1.jpg" srcset="./assets/img/avatars/te1@2x.jpg 2x" alt="" />-->
                                        <div class="info">
                                            <h5 class="mb-1">Mark Vincent Cantero</h5>
                                        </div>
                                    </div>
                                </blockquote>
                            </div>
                        </div>
                    </div>
                    <div class="item col-md-6 col-xl-4">
                        <div class="card shadow-lg">
                            <div class="card-body">
                                <span class="ratings five mb-3"></span>
                                <blockquote class="icon mb-0">
                                    <p>“Amazing co-working space, environment is both professional and welcoming :) Staff is
                                        incredibly friendly and accommodating.”</p>
                                    <div class="blockquote-details">
                                        <span class="avatar bg-pale-primary text-primary w-12 h-12">
                                            <span>GM</span>
                                        </span>
                                        <!--<img class="rounded-circle w-12" src="./assets/img/avatars/te1.jpg" srcset="./assets/img/avatars/te1@2x.jpg 2x" alt="" />-->
                                        <div class="info">
                                            <h5 class="mb-1">Garcia Mila</h5>
                                        </div>
                                    </div>
                                </blockquote>
                            </div>
                        </div>
                    </div>
                    <div class="item col-md-6 col-xl-4">
                        <div class="card shadow-lg">
                            <div class="card-body">
                                <span class="ratings five mb-3"></span>
                                <blockquote class="icon mb-0">
                                    <p>“Great space! Owners were very accommodating to share and let me use one their
                                        facility. Will definitely come back!”</p>
                                    <div class="blockquote-details">
                                        <span class="avatar bg-pale-primary text-primary w-12 h-12">
                                            <span>JT</span>
                                        </span>
                                        <!--<img class="rounded-circle w-12" src="./assets/img/avatars/te1.jpg" srcset="./assets/img/avatars/te1@2x.jpg 2x" alt="" />-->
                                        <div class="info">
                                            <h5 class="mb-1">Joseph Tanael</h5>
                                        </div>
                                    </div>
                                </blockquote>
                            </div>
                        </div>
                    </div>
                    <div class="item col-md-6 col-xl-4">
                        <div class="card shadow-lg">
                            <div class="card-body">
                                <span class="ratings five mb-3"></span>
                                <blockquote class="icon mb-0">
                                    <p>“The staff and owners were very nice and friendly. They also have a spacious working
                                        area. They also have parking area for customer. BEST PART they are open 24 hrs from
                                        monday to saturday.”</p>
                                    <div class="blockquote-details">
                                        <span class="avatar bg-pale-primary text-primary w-12 h-12">
                                            <span>JR</span>
                                        </span>
                                        <!--<img class="rounded-circle w-12" src="./assets/img/avatars/te1.jpg" srcset="./assets/img/avatars/te1@2x.jpg 2x" alt="" />-->
                                        <div class="info">
                                            <h5 class="mb-1">Jessieca Roderno</h5>
                                        </div>
                                    </div>
                                </blockquote>
                            </div>
                        </div>
                    </div>
                    <div class="item col-md-6 col-xl-4">
                        <div class="card shadow-lg">
                            <div class="card-body">
                                <span class="ratings five mb-3"></span>
                                <blockquote class="icon mb-0">
                                    <p>
                                        “1000 star reccommendation. Their rates are cheap but the place is really good. Wifi
                                        connection is fast, free snacks and drinks, open 24/7 during weekdays, chairs and
                                        tables are kept clean too.
                                        How I wish you have branch near my city but I am always willing to come to your
                                        Dasma branch.”
                                    </p>
                                    <div class="blockquote-details">
                                        <span class="avatar bg-pale-primary text-primary w-12 h-12">
                                            <span>CM</span>
                                        </span>
                                        <!--<img class="rounded-circle w-12" src="./assets/img/avatars/te1.jpg" srcset="./assets/img/avatars/te1@2x.jpg 2x" alt="" />-->
                                        <div class="info">
                                            <h5 class="mb-1">Christina Joy Mancilla</h5>
                                        </div>
                                    </div>
                                </blockquote>
                            </div>
                        </div>
                    </div>
                    <div class="item col-md-6 col-xl-4">
                        <div class="card shadow-lg">
                            <div class="card-body">
                                <span class="ratings five mb-3"></span>
                                <blockquote class="icon mb-0">
                                    <p>
                                        “Finally found my co-working space! The workspace is comfortable, and the amenities
                                        are great!”
                                    </p>
                                    <div class="blockquote-details">
                                        <span class="avatar bg-pale-primary text-primary w-12 h-12">
                                            <span>MP</span>
                                        </span>
                                        <!--<img class="rounded-circle w-12" src="./assets/img/avatars/te1.jpg" srcset="./assets/img/avatars/te1@2x.jpg 2x" alt="" />-->
                                        <div class="info">
                                            <h5 class="mb-1">Michael Planas</h5>
                                        </div>
                                    </div>
                                </blockquote>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--
        FAQ
    -->
    <section class="wrapper bg-soft-primary">
        <div class="container py-7 py-md-8">
            <div class="row">
                <div class="col-lg-11 col-xxl-10 mx-auto text-center">
                    <h2 class="fs-15 text-uppercase text-muted mb-3">FAQ</h2>
                    <h3 class="display-4 mb-10 px-lg-12 px-xl-10 px-xxl-15">If you don't see an answer to your question,
                        you can send us an email from our contact form.</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-7 mx-auto">
                    <div id="accordion-3" class="accordion-wrapper">
                        <div class="card accordion-item shadow-lg">
                            <div class="card-header" id="accordion-heading-3-1">
                                <button class="collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#accordion-collapse-3-1" aria-expanded="false"
                                    aria-controls="accordion-collapse-3-1">What are your hours of operation?</button>
                            </div>
                            <div id="accordion-collapse-3-1" class="collapse" aria-labelledby="accordion-heading-3-1"
                                data-bs-target="#accordion-3">
                                <div class="card-body">
                                    <p>We are open 24 hours everyday except Sunday (until 7pm only)</p>
                                    <p>Monday opens at 7am.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card accordion-item shadow-lg">
                            <div class="card-header" id="accordion-heading-3-2">
                                <button class="collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#accordion-collapse-3-2" aria-expanded="false"
                                    aria-controls="accordion-collapse-3-2">Where are you located?</button>
                            </div>
                            <div id="accordion-collapse-3-2" class="collapse" aria-labelledby="accordion-heading-3-2"
                                data-bs-target="#accordion-3">
                                <div class="card-body">
                                    <p>We are located at 3rd floor Titan Bldg., The Promenade South, Brgy. Salawag, Dasmariñas Cavite.</p>
                                    <p>Google Maps Pin: Mindspace Coworking Hub</p>
                                </div>
                            </div>
                        </div>
                        <div class="card accordion-item shadow-lg">
                            <div class="card-header" id="accordion-heading-3-3">
                                <button class="collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#accordion-collapse-3-3" aria-expanded="false"
                                    aria-controls="accordion-collapse-3-3">What are your coworking rates?</button>
                            </div>
                            <div id="accordion-collapse-3-3" class="collapse" aria-labelledby="accordion-heading-3-3"
                                data-bs-target="#accordion-3">
                                <div class="card-body">
                                    <p>
                                        <a href="#" data-glightbox="title: Daily Pass;" data-gallery="g1">
                                            <img src="{{ asset('img/rates/daily_pass.jpg') }}" alt="Daily Pass">
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card accordion-item shadow-lg">
                            <div class="card-header" id="accordion-heading-3-4">
                                <button class="collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#accordion-collapse-3-4" aria-expanded="false"
                                    aria-controls="accordion-collapse-3-4">Do you have a meeting room?</button>
                            </div>
                            <div id="accordion-collapse-3-4" class="collapse" aria-labelledby="accordion-heading-3-4"
                                data-bs-target="#accordion-3">
                                <div class="card-body">
                                    <p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum
                                        massa justo sit amet risus. Cras mattis consectetur purus sit amet fermentum.
                                        Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Cum sociis
                                        natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec sed
                                        odio dui. Cras justo odio, dapibus ac facilisis.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
