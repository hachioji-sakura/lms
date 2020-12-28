<div id="matching_form">
    <div class="row p-2">
      <div class="col-6 mb-1">
        <form method="POST" action="/events/{{$event_id}}/lesson_requests/calendars">
          @csrf
          <button type="button" class="btn btn-submit btn-primary w-100" accesskey="matching_form">
            <i class="fa fa-hands-helping"></i>
            OK
          </button>
        </form>
      </div>
      <div class="col-12  mb-1">
          <button type="reset" class="btn btn-secondary btn-block">
              {{__('labels.close_button')}}
          </button>
      </div>
    </div>
</div>
