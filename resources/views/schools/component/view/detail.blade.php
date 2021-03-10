<div class="col-12">
  <div class="row">
    <div class="col-xs-6 col-lg-6">
      <h6>{{ $school_view_entity->localizeName('name') }}</h6>
      <p>{{ $high_school_entity->name() }}</p>
      <h6>{{ $school_view_entity->localizeName('name_kana') }}</h6>
      <p>{{ $high_school_entity->nameKana() }}</p>
      <h6>{{ $school_view_entity->localizeName('post_number') }}</h6>
      <p>{{ $high_school_entity->postNumber() }}</p>
      <h6>{{ $school_view_entity->localizeName('address') }}</h6>
      <p>{{ $high_school_entity->address() }}</p>
      <h6>{{ $school_view_entity->localizeName('phone_number') }}</h6>
      <p>{{ $high_school_entity->phoneNumber() }}</p>
      <h6>{{ $school_view_entity->localizeName('fax_number') }}</h6>
      <p>{{ $high_school_entity->faxNumber() }}</p>
    </div>
    <div class="col-xs-6 col-lg-6">
      <h6>{{ $school_view_entity->localizeName('process') }}</h6>
      <p>{{ $high_school_entity->process() }}</p>
      <h6>{{ $school_view_entity->localizeName('url') }}</h6>
      <p><a href="{{ $high_school_entity->url() }}" target="_blank" rel="noopener noreferrer">{!! $high_school_entity->url() !!}</a></p>
      <h6>{{ $school_view_entity->localizeName('department_names') }}</h6>
      <p>{{ $high_school_entity->departmentNames() }}</p>
      <h6>{{ $school_view_entity->localizeName('access') }}</h6>
      {{-- 改行が含まれているため、加工を行う。{!! !!}ではXSS攻撃が防げないため --}}
      <span>{!! nl2br(e($high_school_entity->access())) !!}</span>
    </div>
  </div>
</div>
