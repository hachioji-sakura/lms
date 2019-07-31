@foreach($item->trial_students as $trial_student)
  @component('students.forms.agreement', ['item' => $trial_student->student, 'fields' => null, 'domain' => $domain, 'input' => $input]) @endcomponent
@endforeach
