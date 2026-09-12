@if(session('success'))

<div
    class="alert alert-success alert-dismissible fade show auto-dismiss"
    role="alert">

    <i class="fas fa-check-circle"></i>

    {{ session('success') }}

    <button
        type="button"
        class="close"
        data-dismiss="alert">

        <span>&times;</span>

    </button>

</div>

@endif


@if(session('error'))

<div
    class="alert alert-danger alert-dismissible fade show auto-dismiss"
    role="alert">

    <i class="fas fa-times-circle"></i>

    {{ session('error') }}

    <button
        type="button"
        class="close"
        data-dismiss="alert">

        <span>&times;</span>

    </button>

</div>

@endif


@if(session('warning'))

<div
    class="alert alert-warning alert-dismissible fade show auto-dismiss"
    role="alert">

    <i class="fas fa-exclamation-triangle"></i>

    {{ session('warning') }}

    <button
        type="button"
        class="close"
        data-dismiss="alert">

        <span>&times;</span>

    </button>

</div>

@endif


@if(session('info'))

<div
    class="alert alert-info alert-dismissible fade show auto-dismiss"
    role="alert">

    <i class="fas fa-info-circle"></i>

    {{ session('info') }}

    <button
        type="button"
        class="close"
        data-dismiss="alert">

        <span>&times;</span>

    </button>

</div>

@endif


@push('js')

<script>

document.addEventListener('DOMContentLoaded', function(){

    setTimeout(function(){

        $('.auto-dismiss').fadeOut('slow');

    },3000);

});

</script>

@endpush