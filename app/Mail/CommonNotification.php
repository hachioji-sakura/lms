<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommonNotification extends Mailable
{
    use Queueable, SerializesModels;
    protected $title;
    protected $text;
    protected $type;
    protected $template;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title='学習管理システム（自動送信メール）',
                                $param=['text' => 'このメールはシステムより自動送信しています。'],
                                $type="text", $template="sample")
    {
      $this->title = $title;
      $this->param = $param;
      $this->type = $type;
      $this->template = $template;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      $_template = 'emails.'.$this->template.'_'.$this->type;
      $this->subject($this->title)
                    ->with($this->param);
      if($this->type === 'text'){
        $this->text($_template);
      }
      else if($this->type === 'html'){
        $this->view($_template);
      }
    }
}
