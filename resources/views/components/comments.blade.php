<?php $__c=0; ?>
<div class="tab-pane {{$is_active}}" id="comments_tab_{{$comment_type}}">
  <div class="card card-widget">
    <div class="card-comments">
        @foreach($comments as $comment)
          @if($comment_type==='all' || $comment->type===$comment_type)
            <?php $__c++; ?>
            <div class="card-comment">
              <img class="img-circle img-sm mr-1" src="{{$comment->create_user->details()->icon}}" alt="User Image">
              <span class="username">{{$comment->create_user->details()->name}}
                <span class="text-muted float-right">
                  {{$comment->created_at}}
                </span>
              </span>
              <div class="comment-text">
                {{$comment->title}}<br>
                {{$comment->body}}<br>
              </div>
              @if($user->user_id === $comment->create_user_id)
              <span class="float-right mr-1">
                <a href="javascript:void(0);" page_title="コメント編集" page_form="dialog" page_url="/comments/{{$comment->id}}/edit?_page_origin={{$domain}}_{{$item->id}}" role="button" class="btn btn-default btn-sm float-left mr-1">
                  <i class="fa fa-edit"></i>
                </a>
                <a href="javascript:void(0);" page_title="コメント削除" page_form="dialog" page_url="/comments/{{$comment->id}}?action=delete&_page_origin={{$domain}}_{{$item->id}}" role="button" class="btn btn-default btn-sm float-left mr-1">
                  <i class="fa fa-trash"></i>
                </a>
              </span>
              @endif
            </div>
          @endif
        @endforeach
        @if($__c < 1)
          <div class="card-comment">
            <div class="comment-text">
              コメントはありません
            </div>
          </div>
        @endif
    </div>
  </div>
</div>
