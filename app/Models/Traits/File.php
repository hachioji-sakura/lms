<?php
namespace App\Models\Traits;

trait File
{
  public function is_document(){
    if(empty($this->type)) return false;
    switch(mb_strtolower($this->type)){
      case "txt":
      case "ppt":
      case "pptx":
      case "rtf":
      case "wri":
      case "doc":
      case "docx":
      case "xls":
      case "xlsx":
        return true;
        break;
    }
    return false;
  }

  public function is_pdf(){
    if(empty($this->type)) return false;
    switch(mb_strtolower($this->type)){
      case "pdf":
        return true;
        break;
    }
    return false;
  }

  public function is_image(){
    if(empty($this->type)) return false;
    switch(mb_strtolower($this->type)){
      case "tif":
      case "tiff":
      case "jpg":
      case "jpeg":
      case "png":
      case "gif":
      case "bmp":
      case "pict":
        return true;
        break;
    }
    return false;
  }
  public function is_movie(){
    if(empty($this->type)) return false;
    switch(mb_strtolower($this->type)){
      case "mp4":
      case "avi":
      case "mpeg":
      case "wmv":
      case "webm":
      case "flv":
      case "ogm":
      case "mkv":
      case "mov":
      case "m2ts":
      case "ts":
      case "qt":
      case "asf":
      case "ra":
      case "rv":
      case "ram":
      case "rpm":
      case "vdo":
      case "scr":
      case "swf":
        return true;
        break;
    }
    return false;
  }
  public function is_music(){
    if(empty($this->type)) return false;
    switch(mb_strtolower($this->type)){
      case "mp3":
      case "wma":
      case "mov":
      case "alac":
      case "tta":
      case "m4a":
      case "ape":
      case "mac":
      case "mka":
      case "flac":
      case "aif":
      case "aifc":
      case "aiff":
      case "mid":
      case "midi":
        return true;
        break;
    }
    return false;
  }
}
