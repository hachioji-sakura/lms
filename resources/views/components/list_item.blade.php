<tr>
  @foreach($fields as $key => $field)
    <td>
    @if($key==="buttons")
      @foreach($field["button"] as $key => $button)
        @if( (isset($button['type']) && $button['type']($row)) || !isset($button['type']) )
          @if($button==="edit" || $key === "edit")
          <a href="javascript:void(0);" page_title="{{$domain_name}}{{__('labels.edit')}}" page_form="dialog" page_url="/{{$domain}}/{{$row['id']}}/edit" role="button" class="btn btn-success btn-sm float-left mr-1 my-1">
            <i class="fa fa-edit"></i>
          </a>
          @elseif($button==="delete")
          <a href="javascript:void(0);" page_title="{{$domain_name}}{{__('labels.delete')}}" page_form="dialog" page_url="/{{$domain}}/{{$row['id']}}?action=delete" role="button" class="btn btn-danger btn-sm float-left mr-1 my-1">
            <i class="fa fa-trash"></i>
          </a>
          @elseif(isset($button['method']))
          <a href="javascript:void(0);" page_title="{{$domain_name}}{{$button['label']}}" page_form="dialog" page_url="/{{$domain}}/{{$row['id']}}/{{$button['method']}}" role="button" class="btn btn-{{$button['style']}} btn-sm float-left mr-1 my-1">
            @if(isset($button['icon']))
            <i class="fa fa-{{$button['icon']}} mr-1"></i>
            @endif
            {{$button['label']}}
          </a>
          @elseif(isset($button['action']))
          <a href="javascript:void(0);" page_title="{{$button['label']}}" page_form="dialog" page_url="/{{$domain}}/{{$row['id']}}?action={{$button['action']}}" role="button" class="btn btn-{{$button['style']}} btn-sm float-left mr-1 my-1">
            @if(isset($button['icon']))
            <i class="fa fa-{{$button['icon']}} mr-1"></i>
            @endif
            {{$button['label']}}
          </a>
          @elseif(isset($button['link']))
          <a href="/{{$domain}}/{{$row['id']}}/{{$button['link']}}" role="button" class="btn btn-{{$button['style']}} btn-sm float-left mr-1 my-1">
            @if(isset($button['icon']))
            <i class="fa fa-{{$button['icon']}} mr-1"></i>
            @endif
            {{$button['label']}}
          </a>
          @elseif($button === 'download')
            @if(!empty($row->s3_url))
              <a href="{{$row->s3_url}}" role="button" class="btn btn-info btn-sm float-left mr-1 my-1" target="_blank">
                <i class="fa fa-cloud-download-alt"></i>
              </a>
            @endif
          @endif
        @endif
      @endforeach
    @else
      @if(isset($field['link']))
        <a
        @if($field['link']==='show')
           href="javascript:void(0);" page_title="{{$domain_name}}詳細" page_form="dialog" page_url="/{{$domain}}/{{$row['id']}}"
        @else
          @if(isset($field['page_title']))
            page_title = "{{$field['page_title']}}"
          @endif
          @if(isset($field['page_form']))
            href="javascript:void(0);"  page_form = "{{$field['page_form']}}" page_url="{{$field['link']($row)}}"
          @else
            href="{{$field['link']($row)}}"
          @endif
        @endif
        @if(isset($field['blank']) && $field['blank'] == true)
          target=_blank
        @endif
        >
      @endif
      @if($key==="status_name")
        <small class="badge badge-{{config('status_style')[$row['status']]}} mt-1 mr-1">{{$row[$key]}}</small>
      @elseif(isset($row[$key]) && gettype($row[$key])=='array')
        @foreach($row[$key] as $_item)
        <span class="text-xs mx-2">
          <small class="badge badge-primary mt-1 mr-1">
            {{$_item}}
          </small>
        </span>
        @endforeach
      @elseif(isset($field['include']))
        @include($domain.'.'.$field['include'])
      @else
        @empty($row[$key])
          ー
        @else
          {{str_limit($row[$key], 50, '...')}}
        @endempty
      @endif


      @if(isset($field['link']))
        </a>
      @endif
    @endif
    </td>
  @endforeach
</tr>
