@section('first_form')
  <div class="row">
    @if($_edit)
      @component('textbooks.forms.select_textbook', ['_edit' => $_edit, 'textbook' => $textbook ]); @endcomponent
      @component('textbooks.forms.select_publisher', ['_edit' => $_edit, 'publishers' => $publishers, 'textbook' => $textbook ]); @endcomponent
      @component('textbooks.forms.select_supplier', ['_edit' => $_edit, 'suppliers' => $suppliers, 'textbook'=> $textbook ]); @endcomponent
      @component('textbooks.forms.select_difficulty', ['_edit' => $_edit,'textbook'=> $textbook ]); @endcomponent
      @component('textbooks.forms.subject', ['_edit' => $_edit,'subjects' => $subjects,'textbookSubjects' => $textbookSubjects??null]); @endcomponent
      @component('textbooks.forms.grade', ['_edit' => $_edit,'grades' => $grades,'textbookGrades' => $textbookGrades??null]); @endcomponent
      @component('textbooks.forms.price', ['_edit' => $_edit,'grades' => $grades,'textbookPrices'=> $textbookPrices??null]); @endcomponent
    @else
    {{-- create --}}
      @component('textbooks.forms.select_textbook'); @endcomponent
      @component('textbooks.forms.select_publisher', ['publishers' => $item['publishers']]); @endcomponent
      @component('textbooks.forms.select_supplier', ['suppliers' =>  $item['suppliers'] ]); @endcomponent
      @component('textbooks.forms.select_difficulty'); @endcomponent
      @component('textbooks.forms.subject', ['subjects' => $item['subjects']]); @endcomponent
      @component('textbooks.forms.grade', ['grades' => $item['grades']]); @endcomponent
      @component('textbooks.forms.price'); @endcomponent
      @component('textbooks.forms.explain'); @endcomponent
    @endif
  </div>
@endsection
