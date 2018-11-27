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
          <a href="/@yield('domain')/{{$row['id']}}/edit" role="button" class="btn btn-success btn-sm float-left mr-1">
            <i class="fa fa-edit"></i>
          </a>
          @elseif($button==="delete")
          <a href="/@yield('domain')/{{$row['id']}}?_del=1" role="button" class="btn btn-danger btn-sm float-left mr-1">
            <i class="fa fa-times"></i>
          </a>
          @endif
        @endforeach
      @else
        @if(isset($field['link']))
          @if($field['link']==='show')
            <a href="/@yield('domain')/{{$row['id']}}">{{$row[$key]}}</a>
          @else
            <a href="{{$field['link']}}">{{$row[$key]}}</a>
          @endif
        @else
          {{$row[$key]}}
        @endif
      @endif
      </td>
    @endforeach
  </tr>
@endforeach
