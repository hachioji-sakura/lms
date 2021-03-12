<div id="calendars_entry" class="direct-chat-msg">
  @include('textbooks.create_form')
  @if(isset($_edit) && $_edit===true)
  <form id="edit" method="POST" action="/{{$domain}}/{{$item['id']}}">
  @method('PUT')
  @else
  <form id="edit" method="POST" action="/{{$domain}}">
  @endif
    @csrf
    @if(isset($origin))
      <input type="hidden" value="{{$origin}}" name="origin" />
    @endif
    @if(isset($student_id))
      <input type="hidden" value="{{$student_id}}" name="student_id" />
    @endif
    @if(isset($manager_id))
      <input type="hidden" value="{{$manager_id}}" name="manager_id" />
    @endif
    <div class="carousel slide" data-ride="carousel" data-interval="false">
      <div class="carousel-inner">
        <div class="carousel-item active">
          @yield('first_form')
          <div class="row">
            @if($item->work==9)
            <div class="col-12 mb-1">
              <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="calendars_entry">
                {{__('labels.update_button')}}
                <i class="fa fa-caret-right ml-1"></i>
              </button>
            </div>
            @else
            <div class="col-12 mb-1">
              <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="calendars_entry"
                @if(isset($_edit) && $_edit==true)
                confirm="{{__('messages.confirm_update')}}">
                {{__('labels.update_button')}}
                @else
                confirm="{{__('messages.confirm_add')}}">
                {{__('labels.create_button')}}
                @endif
                <i class="fa fa-caret-right ml-1"></i>
              </button>
            </div>
            @endif
          </div>
        </div>
      </div>
     </div>
    </div>
  </form>
</div>
