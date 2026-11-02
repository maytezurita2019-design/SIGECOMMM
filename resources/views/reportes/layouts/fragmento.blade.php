<div class="report-result-fragment">
    @yield('content')

    @stack('styles')

    <style>
        .report-result-fragment form {
            display: none !important;
        }

        .report-result-fragment .card:has(form) {
            display: none !important;
        }

        .report-result-fragment .no-print:has(a[href*="/pdf"], a[href*="/excel"], button[onclick*="print"], button[onclick*="Pdf"], button[id*="imprimir"]) {
            display: none !important;
        }
    </style>
</div>
