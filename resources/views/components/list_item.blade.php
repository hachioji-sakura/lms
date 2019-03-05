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
        @elseif(isset($button['method']))
        <a href="javascript:void(0);" page_title="{{$domain_name}}{{$button['label']}}" page_form="dialog" page_url="/{{$domain}}/{{$row['id']}}/{{$button['method']}}" role="button" class="btn btn-{{$button['style']}} btn-sm float-left mr-1">
          {{$button['label']}}
        </a>
        @elseif(isset($button['action']))
        <a href="javascript:void(0);" page_title="{{$button['label']}}" page_form="dialog" page_url="/{{$domain}}/{{$row['id']}}?action={{$button['action']}}" role="button" class="btn btn-{{$button['style']}} btn-sm float-left mr-1">
          {{$button['label']}}
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
