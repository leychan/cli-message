<?php


namespace cliMessage;


use cliMessage\animation\AnimationObject;

class DealMessage
{
    private AnimationObject $object;
    const WIDTH = 16; //一个字二进制表示的数组宽度
    const HEIGHT = 16; //一个字二进制表示的数组长度
    const PER_FONT_BINARY_LENGTH = 256; //一个字二进制串的长度
    const PER_FONT_DOT_LENGTH = 32; //一个字符(非汉字)在汉字库中的长度
    const FONT_FILE = __DIR__ . '/font_file/HZK16'; //汉字库文件

    /**
     * @var string 字符串的二进制形式
     */
    private string $binary_message = '';

    public function __construct(AnimationObject $object)
    {
        $this->object = $object;
        //var_dump($this->object);exit;
    }


    /**
     * @desc 转换要打印的内容到gb2312
     * @user lei
     * @date 2021/5/5
     */
    private function convertToGB2312()
    {
        $this->object->gb2312_message = iconv('utf-8', 'gb2312//IGNORE', $this->object->message);
    }

    /**
     * @desc 将点阵体数据转换成二进制
     * @user lei
     * @date 2021/5/5
     * @param string $str
     * @return string
     */
    private function formatDotStringToBinary(string $str): string
    {
        $len = strlen($str);
        $dots = '';
        for ($i = 0; $i < $len; $i++) {
            $dots .= sprintf("%08b", ord($str[$i]));
        }
        return $dots;
    }


    /**
     * @desc 二进制数据装进数组,准备打印
     * @user lei
     * @date 2021/5/5
     */
    private function shapeDotsToArray()
    {
        $len = count($this->object->message_array);
        for ($line = 0; $line < $len; $line++) { //每一行
            for ($i = 0; $i < $this->object->per_line_quantity; $i++) { //每一行的字数
                for ($j = 0; $j < self::PER_FONT_BINARY_LENGTH; $j++) { //每个字的点阵图数据长度
                    $x = $j % self::WIDTH; //数组的列数
                    $y = floor($j / self::HEIGHT); //数组的行数
                    $offset = $i * self::PER_FONT_BINARY_LENGTH + $j; //当前点阵图数据在数组中的位置
                    $this->object->dot_array[$line][$y][$x + $i * self::WIDTH] =
                        isset($this->object->message_array[$line][$offset]) && $this->object->message_array[$line][$offset] == 1
                            ? $this->object->center_icon : $this->object->fill_icon;
                }
            }
        }
    }

    /**
     * @desc 按照设置的每行显示的字数分割点阵数据
     * @user lei
     * @date 2021/5/5
     */
    private function explodeDots()
    {
        $this->object->font_count = mb_strlen($this->object->message);

        if ($this->object->font_count < $this->object->per_line_quantity) {
            $this->object->per_line_quantity = $this->object->font_count;
        }
        $this->object->lines = intval(ceil($this->object->font_count / $this->object->per_line_quantity));
        $block_len = self::PER_FONT_BINARY_LENGTH * $this->object->per_line_quantity;
        for ($i = 0; $i < $this->object->lines; $i++) {
            $this->object->message_array[] = substr($this->binary_message, $block_len * $i, $block_len);
        }
    }

    /**
     * @desc 读取点阵字库里面的数据
     * @user lei
     * @date 2021/5/5
     * @throws \Exception
     */
    private function readHZK()
    {
        $fp = fopen(self::FONT_FILE, 'r+');
        $gb2312_message_len = strlen($this->object->gb2312_message);
        for ($i = 0; $i < $gb2312_message_len; $i++) {
            // 当前字, 如果是汉字,则占两个字节(GB2312),如果非汉字,则占一个字节
            $tmp = $this->object->gb2312_message[$i];
            //转换成整形,以判断是不是汉字
            $binary_num = ord($tmp);
            if ($binary_num < 160) { //非汉字
                $location = ($binary_num + 156 - 1) * self::PER_FONT_DOT_LENGTH;
            } else { //汉字第一个字节是区码, 第二个字节是位码
                $q_code = $binary_num - 160;
                $w_code = ord($this->object->gb2312_message[++$i]) - 160; //因为汉字占两个字节, 所以这里++i, 否则下次循环会定位到当前汉字的位码
                $location = (94 * ($q_code - 1) + ($w_code - 1)) * self::PER_FONT_DOT_LENGTH;
            }
            //定位到location的位置
            fseek($fp, $location, SEEK_SET);
            $dot_string = fread($fp, self::PER_FONT_DOT_LENGTH);
            $this->binary_message .= $this->formatDotStringToBinary($dot_string);
        }
        fclose($fp);
    }

    public function run() :AnimationObject{
        $this->convertToGB2312();
        $this->readHZK();
        $this->explodeDots();
        $this->shapeDotsToArray();
        return $this->object;
    }
}