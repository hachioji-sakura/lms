@section('first_form')
  <div class="row">
    <div class="form-group ml-2">
      <label for="field1">
        {{__('labels.textbook_name')}}
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <input type="text" id="name" name="name" class="form-control" placeholder="例：１、２年の総合復習１"
             @if(isset($textbook))
             value="{{$textbook->name}}"
             @endif
             required>
    </div>
    @component('textbooks.select_form',['_edit'=>$_edit, 'item'=> $textbook??null, 'target_item' => 'publisher', 'collection' => $item['publishers'] ]) @endcomponent
    @component('textbooks.select_form',['_edit'=>$_edit, 'item'=> $textbook??null, 'target_item' => 'supplier', 'collection' => $item['suppliers'] ]) @endcomponent
    @component('textbooks.forms.select_difficulty', ['_edit'=> $_edit,'textbook' => $textbook??null, 'difficulty' => $item['difficulty']]); @endcomponent
    @component('textbooks.forms.subject', ['_edit'=>$_edit,'textbook'=> $textbook??null,'subjects' => $item['subjects'],'textbook_subjects' => $textbook->subject_list??null]); @endcomponent
    @component('textbooks.forms.grade', ['_edit'=>$_edit,'grades' => $item['grades'],'textbook_grades' => $textbook->grade_list??null]); @endcomponent
    @component('textbooks.forms.price', ['_edit'=>$_edit,'textbook_prices'=> $textbook_prices??null, 'prices' => $prices??null]); @endcomponent

    <div class="col-12 schedule_type schedule_type_office_work schedule_type_other">
      <div class="form-group">
        <label for="remark" class="w-100">
          {{__('labels.explain')}}
          <span class="right badge badge-secondary ml-1">{{__('labels.optional')}}</span>
        </label>
        <textarea type="text" id="explain" name="explain" class="form-control" placeholder="" required="true">
      @if(isset($textbook))
        {{$textbook->explain}}
      @endif
     </textarea>
      </div>
    </div>
  </div>
@endsection
