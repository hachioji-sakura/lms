<div id="{{$domain}}_create">
  <form method="POST" action="/{{$domain}}">
    @csrf
    <div class="row">
      <div class="col-12">
        <label>{{__('labels.target_user')}}</label>
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        <select name="student_parent_id" class="form-control select2" width="100%">
          <option value=" "></option>
          @foreach($student_parents as $student_parent)
            <option value="{{$student_parent->id}}">{{$student_parent->name_last}} {{$student_parent->name_first}}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <label>{{__('labels.title')}}</label>
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <input type="text" name="title" class="form-control" placeholder="タイトル" required="false" maxlength="50" >
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <label>{{__('labels.entry_fee')}}</label>
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <input type="text" name="entry_fee" class="form-control" placeholder="入会金" required="false" inputtype="integer" >
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <label>{{__('labels.monthly_fee')}}</label>
        <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        <input type="text" name="membership_fee" class="form-control" placeholder="月会費" required="false" inputtype="integer" >
      </div>
    </div>
    <div class="row mt-2">
      <div class="col-6">
        <div class="form-group">
          <button class="btn btn-submit btn-primary form-control" accesskey="{{$domain}}_create">
            <i class="fa fa-envelope mr-1"></i>{{__('labels.send_button')}}
          </button>
        </div>
      </div>
      <div class="col-6">
        <button type="reset" class="btn btn-secondary btn-block">
          {{__('labels.cancel')}}
        </button>
      </div>
    </div>

  </form>
</div>
