
<?php $__c=0; ?>
<div class="tab-pane {{$is_active}}" id="comments_tab_{{$comment_type}}">
  <div class="card card-prirary cardutline direct-chat direct-chat-primary">
    <div class="card-body">
      <div class="dirct-chat-messages">
        @foreach($comments as $comment)
          @if($comment_type==='all' || $comment->type===$comment_type)
            <?php $__c++; ?>
            <div class="direct-chat-msg p-1 {{$comment->target_user_id == $comment->create_user_id ? 'right' : ''}}">
              <div class="direct-chat-infos clearfix">
                <span class="direct-chat-name float-left">{{$comment->create_user->details()->full_name}}</span>
                <span class="direct-chat-timestamp float-right">{{$comment["created_date"]}}</span>
              </div>
              <img class="direct-chat-img img-sm mr-1" src="{{$comment->create_user->details()->icon}}" alt="User Image">
              <div class="direct-chat-text">
                {!!nl2br($comment->body)!!}
                @if(isset($comment->s3_url))
                <span class="mr-1">
                  <a href="{{$comment->s3_url}}" target="_blank">
                    <i class="fa fa-link mr-1"></i>
                    {{$comment->s3_alias}}
                  </a>
                </span>
                @endif
              </div>
              @if(isset($is_edit) && $is_edit==true)
                @if($user->user_id === $comment->create_user_id)
                <span class="float-right mr-1">
                  <a href="javascript:void(0);" page_title="コメント編集" page_form="dialog" page_url="/comments/{{$comment->id}}/edit?origin={{$domain}}&item_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-left mr-1">
                    <i class="fa fa-edit"></i>
                  </a>
                  <a href="javascript:void(0);" page_title="コメント削除" page_form="dialog" page_url="/comments/{{$comment->id}}?action=delete&domain={{$domain}}&item_id={{$item->id}}" role="button" class="btn btn-default btn-sm float-left mr-1">
                    <i class="fa fa-trash"></i>
                  </a>
                @endif
              @endif
            @endif
          </div>
        @endforeach
        @if($__c < 1)
        <div class="alert">
          <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
