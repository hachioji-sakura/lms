<?php $__c=0; ?>
<div class="tab-pane {{$is_active}}" id="comments_tab_{{$comment_type}}">
  <div class="card card-widget">
    <div class="card-comments">
        @foreach($comments as $comment)
          @if($comment_type==='all' || $comment->type===$comment_type)
            <?php $__c++; ?>
            <div class="card-comment">
              <img class="img-circle img-sm mr-1" src="{{$comment->create_user_icon}}" alt="User Image">
              <span class="username">{{$comment->create_user_name}}
                <span class="text-muted float-right">{{$comment->created_at}}</span>
              </span>
              <div class="comment-text">
                {{$comment->title}}<br>
                {{$comment->body}}<br>
              </div>
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
