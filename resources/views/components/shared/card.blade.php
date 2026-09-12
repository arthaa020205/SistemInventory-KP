<div class="card">

    @isset($title)
        <div class="card-header">
            <h3 class="card-title">
                {{ $title }}
            </h3>
        </div>
    @endisset

    <div class="card-body">

        {{ $slot }}

    </div>

</div>