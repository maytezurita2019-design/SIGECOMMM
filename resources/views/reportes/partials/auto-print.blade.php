@if(request('imprimir') === '1')
    @push('scripts')
    <script>
        window.addEventListener('load', () => window.print());
    </script>
    @endpush
@endif
