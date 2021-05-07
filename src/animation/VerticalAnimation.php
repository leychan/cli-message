<?php


namespace cliMessage\animation;


class VerticalAnimation extends Animation
{


    /**
     * @inheritDoc
     */
    function animate()
    {
        $dot_array = $this->appendFontEdge();
        $this->object->height = self::APPEND_LENGTH * 2 + $this->object->height;
        $this->object->frequency /= 2;
        while (true) {
            foreach ($dot_array as $font) {
                for ($i = 0; $i < $this->object->height; $i++) {
                    for ($j = 0; $j < $this->object->height; $j++) {
                        echo $font[$i][$j];
                    }
                    echo PHP_EOL;
                }

                $this->clear();

                //打印上移
                for ($i = self::APPEND_LENGTH; $i < $this->object->height; $i++) {
                    for ($j = 0; $j < $this->object->height; $j++) {
                        echo $font[$i][$j];
                    }
                    echo PHP_EOL;
                }
                for ($i = 0; $i < self::APPEND_LENGTH; $i++) {
                    for ($j = 0; $j < $this->object->height; $j++) {
                        echo $font[$i][$j];
                    }
                    echo PHP_EOL;
                }

                $this->clear();


                //打印还原
                for ($i = 0; $i < $this->object->height; $i++) {
                    for ($j = 0; $j < $this->object->height; $j++) {
                        echo $font[$i][$j];
                    }
                    echo PHP_EOL;
                }

                $this->clear();


                //打印下移
                for ($i = 0; $i < self::APPEND_LENGTH; $i++) {
                    for ($j = 0; $j < $this->object->height; $j++) {
                        echo $this->object->fill_icon;
                    }
                    echo PHP_EOL;
                }
                for ($i = 0; $i < $this->object->height - 3; $i++) {
                    for ($j = 0; $j < $this->object->height; $j++) {
                        echo $font[$i][$j];

                    }
                    echo PHP_EOL;

                }

                $this->clear();
            }

        }

    }
}