<nav class="navbar navbar-expand-lg center-nav transparent navbar-dark">
    <div class="container flex-lg-row flex-nowrap align-items-center">
        <div class="navbar-brand w-100">
            <a href="">
                <img src="{{ asset('img/mindspace_light_banner_no_bg.png') }}" style="width: 200px; height: 50px" alt="Mindspace Coworking Hub" />
            </a>
        </div>
        <div class="navbar-collapse offcanvas offcanvas-nav offcanvas-start">
            <div class="offcanvas-header d-lg-none">
                <h3 class="text-white fs-30 mb-0">Mindspace</h3>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body ms-lg-auto d-flex flex-column h-100">
                <ul class="navbar-nav">
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                </ul>
            </div>
        </div>

        {{--<div class="navbar-other w-100 d-flex ms-auto">
            <ul class="navbar-nav flex-row align-items-center ms-auto">
                <li class="nav-item d-none d-md-block">
                    <a href="{{ \Filament\Facades\Filament::getUrl() }}" class="btn btn-sm btn-primary rounded-pill">Login</a>
                </li>
                <li class="nav-item d-lg-none">
                    <button class="hamburger offcanvas-nav-btn"><span></span></button>
                </li>
            </ul>
        </div>--}}
    </div>
</nav>