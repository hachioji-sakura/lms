<div class="direct-chat-msg">
  <form method="POST"  action="/{{$domain}}/{{$item->id}}/new">
    @csrf
    @method('PUT')
    <div id="register_form" class="carousel slide" data-ride="carousel" data-interval=false>
      <div class="carousel-inner">
        <div class="carousel-item active">
          @component('components.page_item', ['item' => $item, 'fields' => $fields, 'domain' => $domain])
            @slot('add_form')
              @if(count($candidate_teachers) > 0)
              <ul class="mailbox-attachments clearfix row">
                <li class="col-12 bg-light" accesskey="" target="">
                  <div class="row">
                    <div class="col-4 col-lg-4 col-md-4">
                      講師
                    </div>
                    <div class="col-4 col-lg-4 col-md-4">
                      担当可能科目
                    </div>
                    <div class="col-4 col-lg-4 col-md-4">
                      担当不可科目
                    </div>
                </li>
                @foreach($candidate_teachers as $teacher)
                <li class="col-12" accesskey="" target="">
                  <div class="row">
                    <div class="col-4 col-lg-4 col-md-4">
                      <div class="w-100">
                        <a href="/teachers/{{$teacher->id}}/calendar" target="_blank">
                          <i class="fa fa-calendar-alt mr-2"></i>
                          {{$teacher->name()}}
                        </a>
                      </div>
                      <div class="w-100">
                        @isset($teacher->trial1)
                          @isset($teacher->trial2)
                          希望日時１・２ともに予定あり
                          @else
                          <div class="form-check ml-2">
                            <input class="form-check-input flat-red" type="radio" name="teacher" id="trial2_{{$teacher->id}}" value="{{$teacher->id}}" required="true" alt="trial2">
                            <label class="form-check-label" for="trial2_{{$teacher->id}}">
                                希望日時2にて依頼する
                            </label>
                          </div>
                          @endisset
                        @else
                        <div class="form-check ml-2">
                          <input class="form-check-input flat-red" type="radio" name="teacher" id="trial1_{{$teacher->id}}" value="{{$teacher->id}}" required="true" alt="trial1">
                          <label class="form-check-label" for="trial1_{{$teacher->id}}">
                              希望日時1にて依頼する
                          </label>
                        </div>
                        @endisset
                      </div>
                    </div>
                    <div class="col-4 col-lg-4 col-md-4">
                      @foreach($teacher->enable_subject as $subject)
                      <small class="badge badge-{{$subject['style']}} mt-1 mr-1">
                        {{$subject["key"]}}
                      </small>
                      @endforeach
                    </div>
                    <div class="col-4 col-lg-4 col-md-4">
                      @foreach($teacher->disable_subject as $subject)
                      <small class="badge badge-{{$subject['style']}} mt-1 mr-1">
                        {{$subject["key"]}}
                      </small>
                      @endforeach
                    </div>
                  </div>
                </li>
                @endforeach
              </ul>
              @else
              <div class="alert">
                <h4><i class="icon fa fa-exclamation-triangle"></i>データがありません</h4>
              </div>
              @endif
            @endslot
          @endcomponent
          <div class="row">

            <div class="col-12 mb-1">
              <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-times-circle mr-1"></i>
                キャンセル
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-next btn btn-primary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-right mr-1"></i>
                次へ
              </a>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label for="lesson_place" class="w-100">
                  場所
                  <span class="right badge badge-danger ml-1">必須</span>
                </label>
                <select name="lesson_place" class="form-control" placeholder="場所" required="true">
                  <option value="">(選択してください)</option>
                  @foreach($attributes['lesson_place'] as $index => $name)
                    <option value="{{$index}}">{{$name}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label for="matching_decide" class="w-100">
                  講師を決めた理由は？
                  <span class="right badge badge-danger ml-1">必須</span>
                </label>
                @foreach($attributes['matching_decide'] as $index => $name)
                <label class="mx-2">
                  <input type="checkbox" value="{{ $index }}" name="matching_decide[]" class="flat-red"  onChange="matching_decide_checkbox_change(this)" required="true">{{$name}}
                </label>
                @endforeach
              </div>
            </div>
            <div class="col-12 collapse matching_decide_word_form">
              <div class="form-group">
                <label for="matching_decide_word" class="w-100">
                  その他の場合、理由を記述してください
                  <span class="right badge badge-danger ml-1">必須</span>
                </label>
                <input type="text" id="matching_decide_word" name="matching_decide_word" class="form-control" placeholder="例：数学の受験対策を希望していたため" >
              </div>
            </div>
            <script>
            function matching_decide_checkbox_change(obj){
              var is_other = $('input[type="checkbox"][name="matching_decide[]"][value="other"]').prop("checked");
              if(is_other){
                $(".matching_decide_word_form").collapse("show");
                $(".matching_decide_word_confirm").collapse("show");
              }
              else {
                $(".matching_decide_word_form").collapse("hide");
                $(".matching_decide_word_confirm").collapse("hide");
              }
            }
            </script>
          </div>
          <div class="row">
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" role="button" class="btn-prev btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-arrow-circle-left mr-1"></i>
                戻る
              </a>
            </div>
            <div class="col-12 mb-1">
              <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
                <i class="fa fa-times-circle mr-1"></i>
                キャンセル
              </a>
            </div>
            <div class="col-12 mb-1">
                <button type="submit" class="btn btn-primary btn-block" accesskey="register_form">
                  <i class="fa fa-check mr-1"></i>
                    体験授業予定をこの講師に割り当てる
                </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
$(function(){
  var form_data = util.getLocalData('register_form');
  base.pageSettinged("register_form", form_data);

  //submit
  $("button[type='submit']").on('click', function(e){
    e.preventDefault();
    if(front.validateFormValue('register_form .carousel-item.active')){
      util.removeLocalData('register_form');
      $("form").submit();
    }
  });

  //次へ
  $('.carousel-item .btn-next').on('click', function(e){
    if(front.validateFormValue('register_form .carousel-item.active')){
      var form_data = front.getFormValue('register_form');
      util.setLocalData('register_form', form_data);
      $('#register_form').carousel('next');
    }
  });
  //戻る
  $('.carousel-item .btn-prev').on('click', function(e){
    $('#register_form').carousel('prev');
  });
});
</script>
