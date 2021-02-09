<div class="col-12">
  <div class="form-group">
    <label for="skype_name">
      Skype名
      <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
    </label>
    <input type="text" name="skype_name" class="form-control" placeholder="live:xxxxxxx" inputtype="hankaku"
    @isset($item)
      value="{{$item->get_tag_value('skype_name')}}"
    @else
      value=""
    @endisset
    maxlength=100>
    <a href="https://support.skype.com/ja/faq/fa10858/skype-ming-tohahe-desuka" target="_blank">{{__('labels.skype_name_about')}}</a>
  </div>
</div>
