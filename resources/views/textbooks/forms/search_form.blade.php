@component('textbooks.forms.subject', ['prefix'=>'search_','textbook'=> $textbook??null,'subjects' => $subjects??[],'textbook_subjects' => $textbook->subject_list??null]); @endcomponent
@component('textbooks.forms.grade', ['prefix'=>'search_','grades' => $grades??[],'textbook_grades' => $textbook->grade_list??null]); @endcomponent
@component('textbooks.forms.select_difficulty', ['prefix'=>'search_','textbook' => null ]); @endcomponent
@component('textbooks.forms.select_publisher', ['prefix'=>'search_','publishers' => $publishers??[], 'textbook' => null ]); @endcomponent
@component('textbooks.forms.select_supplier', ['prefix'=>'search_','suppliers' => $suppliers??[], 'textbook' => null ]); @endcomponent
<div class="col-12 mb-2">
  <div class="form-group">
    <label for="search_keyword" class="w-100">
      {{__('labels.search_keyword')}}
    </label>
    <input type="text" name="search_keyword" class="form-control" placeholder="" inputtype=""
           @if(!empty(request()->search_keyword))
           value = "{{request()->search_keyword}}"
           @elseif(!empty(request()->search_word))
           value = "{{request()->search_word}}"
      @endif
    >
  </div>
</div>
