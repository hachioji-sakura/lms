@component('calendars.page', ['item' => $item, 'fields' => $fields, 'domain' => $domain, 'action' => $action])
  @slot('page_message')
    @if(config('app.env')!='product' || strtotime($item->start_time) <= strtotime('15 minute') || strtotime($item->end_time) <= strtotime('1 minute'))
      以下の授業予定の出欠をつけてください
    @else
      <div class="col-12 col-lg-12 col-md-12 mb-1">
        <h4 class="text-danger">出欠確認は、授業開始15分前から行ってください。</h4>
      </div>
    @endif
  @endslot
  @slot('forms')
  <div  id="{{$domain}}_presence">
    @if($item->is_group()===true)
      {{-- グループレッスン系 --}}
      <form method="POST" action="/calendars/{{$item['id']}}">
      @csrf
      @method('PUT')
      <div class="row border-top">
        <div class="col-12 mb-1">
          <div class="form-group">
            <label for="status">
              <i class="fa fa-question-circle mr-1"></i>
              この授業を実施しましたか？
              <span class="right badge badge-danger ml-1">必須</span>
            </label>
            <div class="input-group">
              <div class="form-check">
                <input class="form-check-input icheck flat-green" type="radio" name="status" id="status_presence" value="presence" required="true" onChange="status_change();" validate="status_presence_check();">
                <label class="form-check-label" for="status_presence">
                    実施した
                </label>
              </div>
              <div class="form-check ml-2">
                <input class="form-check-input icheck flat-red" type="radio" name="status" id="status_absence" value="absence" required="true" onChange="status_change();" validate="status_presence_check();">
                <label class="form-check-label" for="status_absence">
                    実施していない
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
      <script>
      function status_change(){
        var status = $("input[name='status']:checked").val();
        if(status=="presence"){
          $("#presence_list").collapse('show');
          $("#presence_list input").show();
        }
        else {
          $("#presence_list").collapse('hide');
          $("#presence_list input").hide();
        }
      }
      function status_presence_check(){
        console.log("status_presence_check");
        var _is_scceuss = false;
        var status = $("input[name='status']:checked").val();
        if(status=="presence"){
          //実施
          $("input.presence_check[type='radio']:checked").each(function(index, value){
            var val = $(this).val();
            console.log(val);
            if(val=="presence"){
              //一人でも出席がいる
              _is_scceuss = true;
            }
          });
          if(!_is_scceuss){
            front.showValidateError('#presence_list_table', '出席した生徒がいません');
          }
        }
        else {
          _is_scceuss = true;
          //実施していない
          $("input.presence_check[type='radio']:checked").each(function(index, value){
            var val = $(this).val();
            console.log(val);
            if(val=="presence"){
              //一人でも出席がいる
              _is_scceuss = false;
            }
          });
          if(!_is_scceuss){
            front.showValidateError('#presence_list_table', '授業を実施していない場合、生徒に出席とつけれません');
          }
        }
        return _is_scceuss;
      }
      </script>
      <div class="row collapse" id="presence_list">
        <div class="col-12 mb-1">
          <div class="form-group">
            <label for="status">
              <i class="fa fa-question-circle mr-1"></i>
              生徒は出席しましたか？
              <span class="right badge badge-danger ml-1">必須</span>
            </label>
          </div>
        </div>
        <div class="col-12">
          <table class="table table-striped w-80" id="presence_list_table">
            <tr class="bg-gray">
              <th class="p-1 pl-2 text-sm "><i class="fa fa-user mr-1"></i>生徒</th>
              <th class="p-1 pl-2 text-sm"><i class="fa fa-check mr-1"></i>出欠</th>
            </tr>
            @foreach($item->members as $member)
            @if($member->user->details()->role==="student")
            <tr class="">
              <th class="p-1 pl-2">
                {{$member->user->details()->name}}</th>
              <td class="p-1 text-sm text-center">
                @if($member->status=="rest")
                  <i class="fa fa-times mr-1"></i>お休み
                @else
                <div class="input-group">
                  <div class="form-check">
                    <input class="form-check-input icheck flat-green presence_check" type="radio" name="{{$member->id}}_status" id="{{$member->id}}_status_presence" value="presence" required="true" >
                    <label class="form-check-label" for="{{$member->id}}_status_presence">
                        出席
                    </label>
                  </div>
                  <div class="form-check ml-2">
                    <input class="form-check-input icheck flat-red presence_check" type="radio" name="{{$member->id}}_status" id="{{$member->id}}_status_absence" value="absence" required="true" >
                    <label class="form-check-label" for="{{$member->id}}_status_absence">
                        欠席
                    </label>
                  </div>
                </div>
                @endif
              </td>
            </tr>
            @endif
            @endforeach
          </table>
        </div>
      </div>
        @if(config('app.env')!='product' || strtotime($item->start_time) <= strtotime('15 minute') || strtotime($item->end_time) <= strtotime('1 minute'))
          {{-- 当日開始15分前～終了15分後までの表示 --}}
          <div class="row">
            <div class="col-12 col-lg-6 col-md-6 mb-1">
              <button type="button" class="btn btn-submit btn-success btn-block"  accesskey="{{$domain}}_presence">
                  <i class="fa fa-check-circle mr-1"></i>
                  出欠をつける
              </button>
            </div>
            <div class="col-12 col-lg-6 col-md-6 mb-1" id="{{$domain}}_presence">
              <button type="reset" class="btn btn-secondary btn-block">
                  閉じる
              </button>
            </div>
          </div>
        @else
          <div class="row">
            <div class="col-12 col-lg-12 col-md-12 mb-1">
                <button type="reset" class="btn btn-secondary btn-block">
                    閉じる
                </button>
            </div>
          </div>
        @endif
      </form>
    @else
      {{-- マンツーマン系 --}}
      <div class="row">
        <div class="col-12 col-lg-6 col-md-6 mb-1">
          <form method="POST" action="/calendars/{{$item['id']}}/presence">
            @csrf
            <input type="hidden" value="1" name="is_all_student" />
            @method('PUT')
            <button type="submit" class="btn btn-success btn-block"  accesskey="{{$domain}}_presence">
                <i class="fa fa-check-circle mr-1"></i>
                出席
            </button>
          </form>
        </div>
        <div class="col-12 col-lg-6 col-md-6 mb-1">
          <form method="POST" action="/calendars/{{$item['id']}}/absence">
            @csrf
            <input type="hidden" value="1" name="is_all_student" />
            @method('PUT')
            <button type="submit" class="btn btn-danger btn-block"  accesskey="{{$domain}}_presence">
              <i class="fa fa-times-circle mr-1"></i>
                欠席
            </button>
          </form>
        </div>
        <div class="col-12 col-lg-12 col-md-12 mb-1">
            <button type="reset" class="btn btn-secondary btn-block">
                閉じる
            </button>
        </div>
      </div>
    @endif
  </div>
  @endslot
@endcomponent
