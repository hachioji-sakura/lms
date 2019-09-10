<div class="row">
  <div class="col-12">
    <h5 class="bg-info p-1 pl-2 mb-4">
      <i class="fa fa-money-check-alt mr-1"></i>
      {{__('labels.bank_account')}} {{__('labels.info')}}
    </h5>
  </div>
  <div class="col-3 col-lg-3 col-md-3">
    <div class="form-group">
      <label for="bank_no">
        {{__('labels.bank_no')}}
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <input type="text" name="bank_no" class="form-control" placeholder="ex.0006" required="true" inputtype="number"
      @isset($item)
        value="{{$item['bank_no']}}"
      @else
        value="0036"
      @endisset
      maxlength=4>
    </div>
  </div>
  <div class="col-3 col-lg-3 col-md-3">
    <div class="form-group">
      <label for="bank_branch_no">
        {{__('labels.bank_branch_no')}}
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <input type="text" name="bank_branch_no" class="form-control" placeholder="ex.215" required="true" inputtype="number"
      @isset($item)
        value="{{$item['bank_branch_no']}}"
      @endisset
      maxlength=3>
    </div>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="bank_account_type" class="w-100">
        {{__('labels.bank_account_type')}}
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      @foreach($attributes['bank_account_type'] as $index => $name)
      <label class="mx-2">
        <input type="radio" value="{{ $index }}" name="bank_account_type" class="icheck flat-green" required="true"
        @if($_edit===true && isset($item) && $item['bank_account_type'] == $index)
        checked
        @endif
        >{{$name}}
      </label>
      @endforeach
    </div>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="bank_account_no">
        {{__('labels.bank_account_no')}}
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <input type="text" name="bank_account_no" class="form-control" placeholder="ex.0012345" required="true" inputtype="number"
      @isset($item)
        value="{{$item['bank_account_no']}}"
      @endisset
      maxlength=7>
    </div>
  </div>
  <div class="col-6 col-lg-6 col-md-6">
    <div class="form-group">
      <label for="bank_account_name">
        {{__('labels.bank_account_name')}}
        <span class="right badge badge-danger ml-1">{{__('labels.required')}}</span>
      </label>
      <input type="text" name="bank_account_name" class="form-control" placeholder="ex.ハチオウジ タロウ" required="true" inputtype="zenkakukana"
      @isset($item)
        value="{{$item['bank_account_name']}}"
      @endisset
      maxlength=80>
    </div>
  </div>
</div>
