<?php


namespace cliMessage\animation;

use cliMessage\utils\Helper;

class VerticalAnimation extends Animation
{

    /**
     * @inheritDoc
     */
    function animate()
    {
        $this->appendFontEdge();
        $this->object->lines = 1;  //一行输出
        $this->object->frequency /= 2;
        //while (true) {
            $this->shift();
        //}
    }

    public function shift() {
        $shifts = [0, 1, 2, 3, 2, 1, 0, -1, -2, -3, -2, -1, 0];
        //$shifts = [0, 1, 2, 3];
        foreach ($this->object->dot_array as $font) {
            foreach ($shifts as $shift) { //每个偏移量
                $this->printShift($font, $shift);
                $this->clear();
                //Helper::printLine();
            }
        }
    }

    public function printShift($font, $shift) {
        for ($i = $shift; $i < $this->object->height + $shift; $i++) {
            for ($j = 0; $j < $this->object->height; $j++) {
                echo $font[$i][$j] ?? $this->object->fill_icon;
            }
            Helper::printLine();
        }
    }
}