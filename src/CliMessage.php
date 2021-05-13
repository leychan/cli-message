<?php

namespace cliMessage;

use cliMessage\animation\Animation;
use cliMessage\animation\AnimationObject;
use cliMessage\animation\DotAnimation;
use cliMessage\animation\DynamicBannerAnimation;
use cliMessage\animation\FlashAnimation;
use cliMessage\animation\LineAnimation;
use cliMessage\animation\VerticalAnimation;
use League\CLImate\CLImate;

class CliMessage
{

    /**
     * @var string 命令行获取输入的参数的提示信息
     */
    private string $cli_input_tips = '';

    /**
     * @var array|string[] 动画选项
     */
    private array $animations = [
        'flash', 'line', 'dot', 'vertical', 'banner'
    ];

    /**
     * @var string 选择的动画类型
     */
    private string $animation = '';

    private CLImate $cli;

    private AnimationObject $object;

    public function __construct() {
        $this->cli = new CLImate();
        $this->object = new AnimationObject();
    }

    /**
     * @desc 设置每一行的打印字的数量
     * @user lei
     * @date 2021/5/5
     * @param int $num
     */
    public function setPerLineQuantity(int $num)
    {
        $this->object->per_line_quantity = $num;
    }

    /**
     * @desc 设置要打印的内容
     * @user lei
     * @date 2021/5/5
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->object->message = $message;
    }

    public function setInnerShow(string $center_icon) {
        $this->object->center_icon = $center_icon;
    }

    public function setOuterShow(string $fill_icon) {
        $this->object->fill_icon = $fill_icon;
    }

    /**
     * @desc 检查要打印的内容是否为空
     * @user lei
     * @date 2021/5/5
     * @throws \Exception
     */
    private function checkMessage()
    {
        if ($this->object->message === '') {
            throw new \Exception('please set message that you want to print');
        }
    }

    /**
     * @desc 按闪烁动画输出内容
     * @user lei
     * @date 2021/5/5
     */
    private function flashPrint()
    {
        return new FlashAnimation($this->object);
    }

    private function verticalPrint() {
        return new VerticalAnimation($this->object);
    }

    private function bannerPrint() {
        return new DynamicBannerAnimation($this->object);
    }

    /**
     * @desc 按行输出内容
     * @user lei
     * @date 2021/5/6
     */
    private function linePrint() {
        return new LineAnimation($this->object);
    }

    /**
     * @desc 按点输出内容
     * @user lei
     * @date 2021/5/6
     */
    private function dotPrint() {
        return new DotAnimation($this->object);
    }

    public function print() {
        $method = $this->animation . 'Print';
        $this->$method()->animate();
    }

    /**
     * @desc 获取命令行输入的参数
     * @user lei
     * @date 2021/5/5
     * @return mixed
     */
    private function getArgs() {
        $input = $this->cli->input($this->cli_input_tips);
        return $input->prompt();
    }

    /**
     * @desc 命令行获取参数时的提示
     * @user lei
     * @date 2021/5/5
     * @param string $tips
     */
    private function setCliInputTips(string $tips) {
        $this->cli_input_tips = $tips;
    }

    /**
     * @desc 检查每行字数是否合理
     * @user lei
     * @date 2021/5/6
     * @throws \Exception
     */
    private function checkPerLineQuantity() {
        if (!is_numeric($this->object->per_line_quantity)) {
            throw new \Exception('please set the number that you want to print per line');
        }
    }

    private function cliRun() {
        $this->setCliInputTips('请输入您想打印的内容:');
        $this->object->message = $this->getArgs();
        $this->checkMessage();

        $this->setCliInputTips("请输入每一行展示的字数, 默认为'{$this->object->per_line_quantity}':");
        $per_line_quantity = $this->getArgs();
        !empty($per_line_quantity) && $this->setPerLineQuantity($per_line_quantity);
        $this->checkPerLineQuantity();

        $this->setCliInputTips("请输入您想打印时,命中时展示的样式, 默认为'{$this->object->center_icon}':");
        $center_icon = $this->getArgs() ;
        !empty($center_icon) && $this->setInnerShow($center_icon);

        $this->setCliInputTips("请输入您想打印时,未命中时展示的样式, 默认为'{$this->object->fill_icon}':");
        $fill_icon = $this->getArgs();
        !empty($fill_icon) && $this->setOuterShow($fill_icon);

        $input = $this->cli->radio('请输入展示时的动画:', $this->animations);
        $this->animation = $input->prompt();
    }

    public function run()
    {
        try {
            $this->cliRun();
            $this->object = (new dealMessage($this->object))->run();
            $this->print();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }

    }
}