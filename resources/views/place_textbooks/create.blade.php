<div id="place_textbooks" class="direct-chat-msg">
  @include('place_textbooks.create_form')
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
    <div class="carousel slide" data-ride="carousel" data-interval="false">
      <div class="carousel-inner">
        <div class="carousel-item active">
          @yield('first_form')
          <div class="row">
            <div class="col-12 mb-1">
              <button type="button" class="btn btn-submit btn-primary btn-block" accesskey="place_textbooks"
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
          </div>
        </div>
      </div>
     </div>
    </div>
  </form>
</div>
