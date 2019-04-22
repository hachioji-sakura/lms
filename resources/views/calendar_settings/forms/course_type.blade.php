<div class="col-12 mt-2">
  {{-- TODO: 自動選択+塾のときはマンツー/ピアノのときは不要 --}}
  <div class="form-group">
    <label for="course_type" class="w-100">
      授業形式
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <div class="input-group" id="course_type_form">
        <input class="form-check-input icheck flat-green" type="radio" name="course_type" id="course_type_single" value="single" required="true"
        @if($item->has_tag("course_type", "single"))
        checked
        @endif
        >
        <label class="form-check-label mr-3" for="course_type_single">
            マンツーマン
        </label>
        <input class="form-check-input icheck flat-green" type="radio" name="course_type" id="course_type_group" value="group" required="true"
        @if($item->has_tag("course_type", "group"))
        checked
        @endif
        >
        <label class="form-check-label mr-3" for="course_type_group">
            グループレッスン
        </label>
        <input class="form-check-input icheck flat-green ml-3" type="radio" name="course_type" id="course_type_family" value="family" required="true"
        @if($item->has_tag("course_type", "family"))
        checked
        @elseif(isset($item["tagdata"]) && isset($item["tagdata"]['course_type']) && isset($item["tagdata"]['course_type']['family']))
        checked
        @endif
        >
        <label class="form-check-label mr-3" for="course_type_family">
            ファミリー
        </label>
    </div>
  </div>
</div>
