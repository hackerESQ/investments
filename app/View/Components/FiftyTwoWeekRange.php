<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FiftyTwoWeekRange extends Component
{
    public $low;
    public $high;
    public $current;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($low, $high, $current)
    {
        $this->low = $low;
        $this->high = $high;
        $this->current = $current;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.fifty-two-week-range');
    }
}
