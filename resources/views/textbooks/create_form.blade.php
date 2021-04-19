@section('first_form')
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <label for="name">
          {{__('labels.textbook_name')}}
          <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
        </label>
        <input type="text" id="name" name="name" class="form-control" placeholder="例：１、２年の総合復習１"
               @if(isset($textbook))
               value="{{$textbook->name}}"
               @endif
               required="true">
            </div>
    </div>
    @component('textbooks.forms.subject', ['prefix'=>'','textbook'=> $textbook??null,'subjects' => $subjects,'textbook_subjects' => $textbook->subject_list??null]); @endcomponent
    @component('textbooks.forms.grade', ['prefix'=>'','grades' => $grades,'textbook_grades' => $textbook->grade_list??null]); @endcomponent
    @component('textbooks.forms.select_difficulty', ['prefix'=>'','textbook' => $textbook??null ]); @endcomponent
    @component('textbooks.select_form', ['prefix'=>'','textbook'=> $textbook??null,'collection' => $publishers, 'target_item' =>'supplier']); @endcomponent
    @component('textbooks.select_form', ['prefix'=>'','textbook'=> $textbook??null,'collection' => $suppliers ,'target_item' =>'publisher']); @endcomponent
    @component('textbooks.forms.price', ['_edit'=>$_edit,'textbook_prices'=> $textbook_prices??null, 'prices' => $prices??null]); @endcomponent

    <div class="col-12 schedule_type schedule_type_office_work schedule_type_other">
      <div class="form-group">
        <label for="explain" class="w-100">
          {{__('labels.explain')}}
          <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        </label>
        <textarea type="text" id="explain" name="explain" class="form-control" placeholder="" required="true">@if($_edit==true && isset($textbook)){{$textbook->explain}}@endif</textarea>
      </div>
    </div>
  </div>
@endsection
