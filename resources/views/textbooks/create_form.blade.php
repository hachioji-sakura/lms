@section('first_form')
  <div class="row">
    @if($_edit)
      @component('textbooks.forms.select_textbook', [ 'textbook' => $textbook ]); @endcomponent
      @component('textbooks.forms.select_publisher', ['textbook' => $textbook, 'publishers' => $item['publishers']]); @endcomponent
      @component('textbooks.forms.select_supplier', [ 'textbook'=> $textbook, 'suppliers' => $item['suppliers'] ]); @endcomponent
      @component('textbooks.forms.select_difficulty', ['textbook'=> $textbook ]); @endcomponent
      @component('textbooks.forms.subject', ['subjects' => $item['subjects'],'textbook_subjects' => $textbook->subject_list??null]); @endcomponent
      @component('textbooks.forms.grade', ['grades' => $item['grades'],'textbook_grades' => $textbook->grade_list??null]); @endcomponent
      @component('textbooks.forms.price', ['textbook_prices'=> $textbook_prices??null]); @endcomponent
        @component('textbooks.forms.explain', [ 'textbook' => $textbook ]); @endcomponent
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
