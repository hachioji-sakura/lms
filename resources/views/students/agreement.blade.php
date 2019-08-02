@component('students.forms.agreement', ['item' => $student, 'fields' => $fields, 'domain' => $domain]) @endcomponent
<div class="row">
  <div class="col-12 mb-1">
    <a href="javascript:void(0);" data-dismiss="modal" role="button" class="btn btn-secondary btn-block float-left mr-1">
      <i class="fa fa-times-circle mr-1"></i>
      {{__('labels.close_button')}}
    </a>
  </div>
</div>
