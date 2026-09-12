<div class="d-flex justify-content-between align-items-center mb-3">

    <div>
        <h1 class="m-0">
            {{ $title }}
        </h1>

        @isset($subtitle)
            <small class="text-muted">
                {{ $subtitle }}
            </small>
        @endisset
    </div>

    @isset($action)

        {{ $action }}

    @endisset

</div>