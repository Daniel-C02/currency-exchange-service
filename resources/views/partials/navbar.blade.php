<nav class="navbar bg-body-tertiary">
    <div class="container-fluid py-6">
        <div class="d-flex gap-5">
            {{-- View Jon output of all currencies --}}
            <a class="btn btn-secondary" href="{{ route('json.currencies') }}" target="_blank">
                View Json Currencies
            </a>
            {{-- View Jon output of all orders --}}
            <a class="btn btn-secondary" href="{{ route('json.orders') }}" target="_blank">
                View Json Orders
            </a>
        </div>
    </div>
</nav>
