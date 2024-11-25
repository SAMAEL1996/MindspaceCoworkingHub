@livewireScripts

<script src="{{ asset('js/frontend/plugins.js') }}"></script>
<script src="{{ asset('js/frontend/theme.js') }}"></script>
<script src="{{ asset('assets/jquery.js') }}"></script>

@yield('js_after')

@stack('scripts_after')
