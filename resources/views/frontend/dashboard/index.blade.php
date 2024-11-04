@extends('frontend.layouts.app')

@section('title', 'Contact')

@section('content')
<section id="home">
    <div class="wrapper bg-gray">
        <div class="container pt-10 pt-md-14 pb-14 pb-md-17 text-center">
            <div class="row text-center">
                <div class="col-lg-9 col-xxl-7 mx-auto" data-cues="zoomIn" data-group="welcome" data-interval="-200">
                    <h2 class="display-1 mb-4">Creative. Smart. Awesome.</h2>
                    <p class="lead fs-24 lh-sm px-md-5 px-xl-15 px-xxl-10">We are an award winning web & mobile design
                        agency that strongly believes in the power of creative ideas.</p>
                </div>
                <!-- /column -->
            </div>
            <!-- /.row -->
            <div class="row text-center mt-10">
                <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
                    <figure><img class="w-auto" src="./assets/img/illustrations/i8.png"
                            srcset="./assets/img/illustrations/i8@2x.png 2x" alt="" /></figure>
                </div>
                <!-- /column -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </div>
    <!-- /.wrapper -->
</section>
<!-- /section -->
<section id="services">
    <div class="wrapper bg-light">
        <div class="container py-14 py-md-17">
            <div class="row gx-lg-8 gx-xl-12 gy-6 mb-10 align-items-center">
                <div class="col-lg-6 order-lg-2">
                    <ul class="progress-list">
                        <li>
                            <p>Marketing</p>
                            <div class="progressbar line blue" data-value="100"></div>
                        </li>
                        <li>
                            <p>Strategy</p>
                            <div class="progressbar line green" data-value="80"></div>
                        </li>
                        <li>
                            <p>Development</p>
                            <div class="progressbar line yellow" data-value="85"></div>
                        </li>
                        <li>
                            <p>Data Analysis</p>
                            <div class="progressbar line orange" data-value="90"></div>
                        </li>
                    </ul>
                    <!-- /.progress-list -->
                </div>
                <!--/column -->
                <div class="col-lg-6">
                    <h3 class="display-5 mb-5">The full service we are offering is specifically designed to meet your
                        business needs and projects.</h3>
                    <p>Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Morbi leo risus, porta ac
                        consectetur ac, vestibulum at eros. Praesent commodo cursus magna, vel scelerisque nisl
                        consectetur duis mollis commodo.</p>
                </div>
                <!--/column -->
            </div>
            <!--/.row -->
            <div class="row gx-lg-8 gx-xl-12 gy-6 gy-md-0 text-center">
                <div class="col-md-6 col-lg-3">
                    <img src="./assets/img/icons/lineal/megaphone.svg"
                        class="svg-inject icon-svg icon-svg-md text-blue mb-3" alt="" />
                    <h4>Marketing</h4>
                    <p class="mb-2">Nulla vitae elit libero, a pharetra augue. Donec id elit non mi porta gravida at
                        eget metus. Cras justo cum sociis natoque magnis.</p>
                </div>
                <!--/column -->
                <div class="col-md-6 col-lg-3">
                    <img src="./assets/img/icons/lineal/target.svg"
                        class="svg-inject icon-svg icon-svg-md text-green mb-3" alt="" />
                    <h4>Strategy</h4>
                    <p class="mb-2">Nulla vitae elit libero, a pharetra augue. Donec id elit non mi porta gravida at
                        eget metus. Cras justo cum sociis natoque magnis.</p>
                </div>
                <!--/column -->
                <div class="col-md-6 col-lg-3">
                    <img src="./assets/img/icons/lineal/settings-3.svg"
                        class="svg-inject icon-svg icon-svg-md text-yellow mb-3" alt="" />
                    <h4>Development</h4>
                    <p class="mb-2">Nulla vitae elit libero, a pharetra augue. Donec id elit non mi porta gravida at
                        eget metus. Cras justo cum sociis natoque magnis.</p>
                </div>
                <!--/column -->
                <div class="col-md-6 col-lg-3">
                    <img src="./assets/img/icons/lineal/bar-chart.svg"
                        class="svg-inject icon-svg icon-svg-md text-orange mb-3" alt="" />
                    <h4>Data Analysis</h4>
                    <p class="mb-2">Nulla vitae elit libero, a pharetra augue. Donec id elit non mi porta gravida at
                        eget metus. Cras justo cum sociis natoque magnis.</p>
                </div>
                <!--/column -->
            </div>
            <!--/.row -->
        </div>
        <!-- /.container -->
    </div>
    <!-- /.wrapper -->
</section>
<!-- /section -->
<section id="process">
    <div class="wrapper bg-gray">
        <div class="container py-14 py-md-17">
            <div class="row gx-lg-8 gx-xl-12 gy-10 mb-14 mb-md-16 align-items-center">
                <div class="col-lg-7">
                    <figure><img class="w-auto" src="./assets/img/illustrations/i3.png"
                            srcset="./assets/img/illustrations/i3@2x.png 2x" alt="" /></figure>
                </div>
                <!--/column -->
                <div class="col-lg-5">
                    <h2 class="fs-15 text-uppercase text-line text-primary mb-3">How It Works?</h2>
                    <h3 class="display-5 mb-7 pe-xxl-5">Everything you need on creating a business process.</h3>
                    <div class="d-flex flex-row mb-4">
                        <div>
                            <img src="./assets/img/icons/lineal/light-bulb.svg"
                                class="svg-inject icon-svg icon-svg-sm text-blue me-4" alt="" />
                        </div>
                        <div>
                            <h4 class="mb-1">Collect Ideas</h4>
                            <p class="mb-1">Nulla vitae elit libero pharetra augue dapibus.</p>
                        </div>
                    </div>
                    <div class="d-flex flex-row mb-4">
                        <div>
                            <img src="./assets/img/icons/lineal/pie-chart-2.svg"
                                class="svg-inject icon-svg icon-svg-sm text-green me-4" alt="" />
                        </div>
                        <div>
                            <h4 class="mb-1">Data Analysis</h4>
                            <p class="mb-1">Vivamus sagittis lacus augue laoreet vel.</p>
                        </div>
                    </div>
                    <div class="d-flex flex-row">
                        <div>
                            <img src="./assets/img/icons/lineal/design.svg"
                                class="svg-inject icon-svg icon-svg-sm text-yellow me-4" alt="" />
                        </div>
                        <div>
                            <h4 class="mb-1">Magic Touch</h4>
                            <p class="mb-0">Cras mattis consectetur purus sit amet.</p>
                        </div>
                    </div>
                </div>
                <!--/column -->
            </div>
            <!--/.row -->
            <div class="row gx-lg-8 gx-xl-12 gy-10 align-items-center">
                <div class="col-lg-7 order-lg-2">
                    <figure><img class="w-auto" src="./assets/img/illustrations/i2.png"
                            srcset="./assets/img/illustrations/i2@2x.png 2x" alt="" /></figure>
                </div>
                <!--/column -->
                <div class="col-lg-5">
                    <h2 class="fs-15 text-uppercase text-line text-primary mb-3">Why Choose Us?</h2>
                    <h3 class="display-5 mb-7">A few reasons why our valued customers choose us.</h3>
                    <div class="accordion accordion-wrapper" id="accordionExample">
                        <div class="card plain accordion-item">
                            <div class="card-header" id="headingOne">
                                <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                    aria-expanded="true" aria-controls="collapseOne"> Professional Design </button>
                            </div>
                            <!--/.card-header -->
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                                data-bs-parent="#accordionExample">
                                <div class="card-body">
                                    <p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut
                                        fermentum massa justo sit amet risus. Cras mattis consectetur purus sit amet
                                        fermentum. Praesent commodo cursus magna, vel.</p>
                                </div>
                                <!--/.card-body -->
                            </div>
                            <!--/.accordion-collapse -->
                        </div>
                        <!--/.accordion-item -->
                        <div class="card plain accordion-item">
                            <div class="card-header" id="headingTwo">
                                <button class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                    aria-expanded="false" aria-controls="collapseTwo"> Top-Notch Support </button>
                            </div>
                            <!--/.card-header -->
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                data-bs-parent="#accordionExample">
                                <div class="card-body">
                                    <p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut
                                        fermentum massa justo sit amet risus. Cras mattis consectetur purus sit amet
                                        fermentum. Praesent commodo cursus magna, vel.</p>
                                </div>
                                <!--/.card-body -->
                            </div>
                            <!--/.accordion-collapse -->
                        </div>
                        <!--/.accordion-item -->
                        <div class="card plain accordion-item">
                            <div class="card-header" id="headingThree">
                                <button class="collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                    aria-expanded="false" aria-controls="collapseThree"> Header and Slider Options
                                </button>
                            </div>
                            <!--/.card-header -->
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                                data-bs-parent="#accordionExample">
                                <div class="card-body">
                                    <p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut
                                        fermentum massa justo sit amet risus. Cras mattis consectetur purus sit amet
                                        fermentum. Praesent commodo cursus magna, vel.</p>
                                </div>
                                <!--/.card-body -->
                            </div>
                            <!--/.accordion-collapse -->
                        </div>
                        <!--/.accordion-item -->
                    </div>
                    <!--/.accordion -->
                </div>
                <!--/column -->
            </div>
            <!--/.row -->
        </div>
        <!-- /.container -->
    </div>
    <!-- /.wrapper -->
</section>
<!-- /section -->
<section id="about">
    <div class="wrapper bg-light">
        <div class="container py-14 py-md-17">
            <div class="row gx-lg-8 gx-xl-12 gy-10 align-items-center">
                <div class="col-lg-4">
                    <h2 class="fs-15 text-uppercase text-line text-primary text-center mb-3">Meet the Team</h2>
                    <h3 class="display-5 mb-5">Save your time and money by choosing our professional team.</h3>
                    <p>Donec id elit non mi porta gravida at eget metus. Morbi leo risus, porta ac consectetur ac,
                        vestibulum at eros tempus porttitor.</p>
                    <a href="#" class="btn btn-primary rounded-pill mt-3">See All Members</a>
                </div>
                <!--/column -->
                <div class="col-lg-8">
                    <div class="swiper-container text-center mb-6" data-margin="30" data-dots="true" data-items-xl="3"
                        data-items-md="2" data-items-xs="1">
                        <div class="swiper">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <img class="rounded-circle w-20 mx-auto mb-4" src="./assets/img/avatars/t1.jpg"
                                        srcset="./assets/img/avatars/t1@2x.jpg 2x" alt="" />
                                    <h4 class="mb-1">Cory Zamora</h4>
                                    <div class="meta mb-2">Marketing Specialist</div>
                                    <p class="mb-2">Etiam porta sem magna malesuada mollis.</p>
                                    <nav class="nav social justify-content-center text-center mb-0">
                                        <a href="#"><i class="uil uil-twitter"></i></a>
                                        <a href="#"><i class="uil uil-slack"></i></a>
                                        <a href="#"><i class="uil uil-linkedin"></i></a>
                                    </nav>
                                    <!-- /.social -->
                                </div>
                                <!--/.swiper-slide -->
                                <div class="swiper-slide">
                                    <img class="rounded-circle w-20 mx-auto mb-4" src="./assets/img/avatars/t2.jpg"
                                        srcset="./assets/img/avatars/t2@2x.jpg 2x" alt="" />
                                    <h4 class="mb-1">Coriss Ambady</h4>
                                    <div class="meta mb-2">Financial Analyst</div>
                                    <p class="mb-2">Aenean eu leo quam. Pellentesque ornare lacinia.</p>
                                    <nav class="nav social justify-content-center text-center mb-0">
                                        <a href="#"><i class="uil uil-youtube"></i></a>
                                        <a href="#"><i class="uil uil-facebook-f"></i></a>
                                        <a href="#"><i class="uil uil-dribbble"></i></a>
                                    </nav>
                                    <!-- /.social -->
                                </div>
                                <!--/.swiper-slide -->
                                <div class="swiper-slide">
                                    <img class="rounded-circle w-20 mx-auto mb-4" src="./assets/img/avatars/t3.jpg"
                                        srcset="./assets/img/avatars/t3@2x.jpg 2x" alt="" />
                                    <h4 class="mb-1">Nikolas Brooten</h4>
                                    <div class="meta mb-2">Sales Manager</div>
                                    <p class="mb-2">Donec ornare elit quam porta gravida at eget.</p>
                                    <nav class="nav social justify-content-center text-center mb-0">
                                        <a href="#"><i class="uil uil-linkedin"></i></a>
                                        <a href="#"><i class="uil uil-tumblr-square"></i></a>
                                        <a href="#"><i class="uil uil-facebook-f"></i></a>
                                    </nav>
                                    <!-- /.social -->
                                </div>
                                <!--/.swiper-slide -->
                                <div class="swiper-slide">
                                    <img class="rounded-circle w-20 mx-auto mb-4" src="./assets/img/avatars/t4.jpg"
                                        srcset="./assets/img/avatars/t4@2x.jpg 2x" alt="" />
                                    <h4 class="mb-1">Jackie Sanders</h4>
                                    <div class="meta mb-2">Investment Planner</div>
                                    <p class="mb-2">Nullam risus eget urna mollis ornare vel eu leo.</p>
                                    <nav class="nav social justify-content-center text-center mb-0">
                                        <a href="#"><i class="uil uil-twitter"></i></a>
                                        <a href="#"><i class="uil uil-facebook-f"></i></a>
                                        <a href="#"><i class="uil uil-dribbble"></i></a>
                                    </nav>
                                    <!-- /.social -->
                                </div>
                                <!--/.swiper-slide -->
                                <div class="swiper-slide">
                                    <img class="rounded-circle w-20 mx-auto mb-4" src="./assets/img/avatars/t5.jpg"
                                        srcset="./assets/img/avatars/t5@2x.jpg 2x" alt="" />
                                    <h4 class="mb-1">Tina Geller</h4>
                                    <div class="meta mb-2">Assistant Buyer</div>
                                    <p class="mb-2">Vivamus sagittis lacus vel augue laoreet rutrum.</p>
                                    <nav class="nav social justify-content-center text-center mb-0">
                                        <a href="#"><i class="uil uil-facebook-f"></i></a>
                                        <a href="#"><i class="uil uil-slack"></i></a>
                                        <a href="#"><i class="uil uil-dribbble"></i></a>
                                    </nav>
                                    <!-- /.social -->
                                </div>
                                <!--/.swiper-slide -->
                            </div>
                            <!--/.swiper-wrapper -->
                        </div>
                        <!-- /.swiper -->
                    </div>
                    <!-- /.swiper-container -->
                </div>
                <!--/column -->
            </div>
            <!--/.row -->
        </div>
        <!-- /.container -->
    </div>
    <!-- /.wrapper -->
</section>
<!-- /section -->
@endsection