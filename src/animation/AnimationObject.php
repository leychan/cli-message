<?php

namespace cliMessage\animation;

class AnimationObject {

    /**
     * @var int 打印的行数
     */
   public int $lines = 1;

    /**
     * @var int 动画频率
     */
   public int $frequency = 750000;

    /**
     * @var int 一个字体的数组高度
     */
   public int $height = 16;

    /**
     * @var array 点阵二进制数组
     */
   public array $dot_array;

    /**
     * @var int 每行展示的字数
     */
   public int $per_line_quantity = 4;

   public string $center_icon = '■';

   public string $fill_icon = '♡';

    /**
     * @var int 字符串个数
     */
   public int $font_count;

    /**
     * @var bool 是否显示因为填充而产生的空白字体
     */
   public bool $with_white_font = false;

   public string $message;

   public string $gb2312_message;

   public array $message_array;
}