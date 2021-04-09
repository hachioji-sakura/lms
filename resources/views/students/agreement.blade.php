@component('students.forms.agreement', ['item' => $student, 'fields' => $fields, 'domain' => $domain, 'input' => false,  'agreement' => $agreement, 'user'=>$user]) @endcomponent

<div class="col-12 col-md-12 mb-1">
  <button type="reset" class="btn btn-secondary btn-block">
    <i class="fa fa-times-circle mr-1"></i>
    {{__('labels.close_button')}}
  </button>
</div>
