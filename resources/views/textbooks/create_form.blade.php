@section('first_form')
  <div class="row">
      @component('textbooks.forms.select_textbook', ['_edit'=>$_edit, 'textbook'=>$textbook ]); @endcomponent
      @component('textbooks.forms.select_publisher', ['_edit' => $_edit, 'publishers' => $publishers, 'textbook'=>$textbook ]); @endcomponent
      @component('textbooks.forms.select_supplier', ['_edit' => $_edit, 'suppliers' => $suppliers, 'textbook'=> $textbook ]); @endcomponent
      @component('textbooks.forms.select_difficulty', ['_edit' => $_edit,'textbook'=> $textbook ]); @endcomponent
      @component('textbooks.forms.subject', ['_edit' => $_edit,'subjects' => $subjects,'textbookSubjects' => $textbookSubjects]); @endcomponent
      @component('textbooks.forms.grade', ['_edit' => $_edit,'grades' => $grades,'textbookGrades' => $textbookGrades]); @endcomponent
      @component('textbooks.forms.price', ['_edit' => $_edit,'grades' => $grades,'textbookPrices'=>$textbookPrices]); @endcomponent
      @component('textbooks.forms.explain', ['_edit' => $_edit, 'textbook' => $textbook ]); @endcomponent
{{--        @component('students.forms.course_minutes', ['_edit'=>$_edit, 'item'=>$item, '_teacher'=>true, 'attributes' => $attributes]) @endcomponent--}}

  </div>
@endsection
