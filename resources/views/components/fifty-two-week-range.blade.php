<span class="" style="width:90em;overflow: hidden; white-space: nowrap;" title="${{ number_format($low, 2) }} - ${{ number_format($high, 2) }}">
    
    @php
        $percent = (($current - $low) * 100) / ($high - $low);
    @endphp

    @for ($x = 0; $x < 10; $x++)
        @php
            if ($percent > ($x * 10)) {
                echo " &#9679;";
            } else {
                echo "	&#9675;";
            }
        @endphp
    @endfor
</span>