<span class="" style="width:90em;overflow: hidden; white-space: nowrap;" title="${{ number_format($low, 2) }} - ${{ number_format($high, 2) }}">
    
    @php
        $percent = (($current - $low) * 100) / ($high - $low);

        $range = ['no', 'no', 'no', 'no', 'no', 'no', 'no', 'no', 'no', 'no'];

    @endphp

    @for ($x = 0; $x <= count($range); $x++)
        @php
        if ($percent > ($x * 10)) {
            $range[$x] = 'full';
        }
        @endphp
    @endfor
    
    @foreach ($range as $range)
        @php
            if ($range=='full') {
                echo " &#9679;";
            } else {
                echo "	&#9675;";
            }
        @endphp 
    @endforeach
</span>