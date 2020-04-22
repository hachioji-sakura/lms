@section('page_sidemenu')
<ul class="nav nav-pills nav-sidebar flex-column pl-1" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item has-treeview menu-open">
      <a href="#" class="nav-link">
      <i class="nav-icon fa fa-id-card"></i>
      <p>
        <ruby style="ruby-overhang: none">
          <rb>{{$item->name()}}</rb>
          <rt>{{$item->kana()}}</rt>
        </ruby>
        <span class="ml-2">様</span>
        <i class="right fa fa-angle-left"></i>
      </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a class="nav-link @if($view=="page") active @endif" href="/parents/{{$item->id}}"  >
            <i class="fa fa-home nav-icon"></i>{{__('labels.top')}}
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);" page_form="dialog" page_url="/{{$domain}}/{{$item->id}}/edit" page_title="{{__('labels.teacher_setting')}}">
            <i class="fa fa-user-cog nav-icon"></i>{{__('labels.users')}}{{__('labels.setting')}}
          </a>
        </li>
        <li class="nav-item">
          <a href="/{{$domain}}/{{$item->id}}/messages?search_status=all" class="nav-link">
            <i class="fa fa-envelope nav-icon"></i>{{__('labels.message')}}
          </a>
        </li>
        {{--
        <li class="nav-item">
          <a class="nav-link @if($view=="ask" || $view=="ask_details") active @endif" href="/parents/{{$item->id}}/ask"  >
            <i class="fa fa-phone nav-icon"></i>{{__('labels.contact_page')}}
          </a>
        </li>
        --}}
      </ul>
    </li>
    <li class="nav-item has-treeview menu-open">
      <a href="#" class="nav-link">
      <i class="nav-icon fa fa-users"></i>
      <p>
        登録生徒
        <i class="right fa fa-angle-left"></i>
      </p>
      </a>
      <ul class="nav nav-treeview">
        @foreach($charge_students as $charge_student)
        <li class="nav-item">
        <a class="nav-link" href="/students/{{$charge_student->id}}" >
          <i class="fa fa-user-graduate nav-icon"></i>
          <p>
            <ruby style="ruby-overhang: none">
              <rb>{{$charge_student->student->name()}}</rb>
              <rt>{{$charge_student->student->kana()}}</rt>
            </ruby>
            <span class="badge badge-{{config('status_style')[$charge_student->student->status]}} right">
              {{$charge_student->student->status_name()}}
            </span>
          </p>
        </a>
        </li>
        @endforeach
      </ul>
      <ul class="nav nav-treeview">
        {{-- TODO 兄弟すべての申し込み内容を１ページで見たい場合に使う。
        <li class="nav-item">
          <a class="nav-link" href="/{{$domain}}/{{$item->id}}/agreement" >
            <i class="fa fa-file-invoice nav-icon"></i>ご契約情報
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="javascript:void(0);"  page_form="dialog" page_url="/comments/create?origin={{$domain}}&item_id={{$item->id}}" page_title="{{__('labels.comment_add')}}">
            <i class="fa fa-comment-dots nav-icon"></i>{{__('labels.comment_add')}}
          </a>
        </li>
        --}}
      </ul>
    </li>
</ul>
@endsection
@section('page_footer')
{{--
<dt>
  <a class="btn btn-app" href="javascript:void(0);" page_form="dialog" page_url="/comments/create?origin={{$domain}}&item_id={{$item->id}}" page_title="{{__('labels.comment_add')}}">
    <i class="fa fa-comment-dots"></i>{{__('labels.comment_add')}}
  </a>
</dt>
--}}
@endsection
