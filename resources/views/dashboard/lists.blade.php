@section('contents')
<div class="card-header">
  <h3 class="card-title">@yield('title')</h3>
</div>
<div class="card-body table-responsive p-0">
  @if(count($items) > 0)
  <table class="table table-hover">
    <tbody>
      <tr>
        @foreach($fields as $key => $field)
          <th>{{$field['label']}}</th>
        @endforeach
      </tr>
      @foreach($items as $i => $row)
        <tr>
          @foreach($fields as $key => $field)
            <td>
            @if($key==="buttons")
              @foreach($field["button"] as $button)
                @if($button==="edit")
                <a href="javascript:void(0);" page_title="{{$domain_name}}編集" page_form="dialog" page_url="/{{$domain}}/{{$row['id']}}/edit" role="button" class="btn btn-success btn-sm float-left mr-1">
                  <i class="fa fa-edit"></i>
                </a>
                @elseif($button==="delete")
                <a href="javascript:void(0);" page_title="{{$domain_name}}削除" page_form="dialog" page_url="/{{$domain}}/{{$row['id']}}?action=delete" role="button" class="btn btn-danger btn-sm float-left mr-1">
                  <i class="fa fa-times"></i>
                </a>
                @endif
              @endforeach
            @else
              @if(isset($field['link']))
                <a
                @if($field['link']==='show')
                   href="javascript:void(0);" page_title="{{$domain_name}}詳細" page_form="dialog" page_url="/{{$domain}}/{{$row['id']}}"
                @else
                  href="{{$field['link']}}"
                @endif
                >
              @endif
              {{str_limit($row[$key], 50, '...')}}
              @if(isset($field['link']))
                </a>
              @endif
            @endif
            </td>
          @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <div class="alert">
    <h4><i class="icon fa fa-exclamation-triangle"></i>データがありません</h4>
  </div>
  @endif
</div>
@endsection
