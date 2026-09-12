@php
    $logoPath = public_path('images/logo.png');

    $logoBase64 = '';

    if (file_exists($logoPath)) {
        $logoBase64 = base64_encode(
            file_get_contents($logoPath)
        );
    }
@endphp


<table class="header-table">

    <tr>

        <td class="logo-cell">

            @if($logoBase64)

                <img
                    src="data:image/png;base64,{{ $logoBase64 }}"
                    class="logo">

            @endif

        </td>


        <td class="company-cell">

            <div class="company-name">
                CV CAHAYA KHANZA PLASTIK
            </div>

            <div class="company-subtitle">
                SISTEM INFORMASI INVENTORY
            </div>

        </td>

    </tr>

</table>


<div class="header-line"></div>