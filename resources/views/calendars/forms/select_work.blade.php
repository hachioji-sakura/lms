<div class="col-12 mt-2">
  <div class="form-group">
    <label for="course_type" class="w-100">
      授業形式
      <span class="right badge badge-danger ml-1">必須</span>
    </label>
    <div class="input-group" id="course_type_form">
      <div class="form-check">
          <input class="form-check-input icheck flat-green" type="radio" name="course_type" id="course_type_single" value="single" required="true" onChange="course_type_change()">
          <label class="form-check-label" for="course_type_single">
              マンツーマン
          </label>
      </div>
      <div class="form-check ml-2">
          <input class="form-check-input icheck flat-green" type="radio" name="course_type" id="course_type_group" value="group" required="true" onChange="course_type_change()" >
          <label class="form-check-label" for="course_type_group">
              グループレッスン
          </label>
      </div>
      <div class="form-check ml-2">
          <input class="form-check-input icheck flat-green" type="radio" name="course_type" id="course_type_family" value="family" required="true" onChange="course_type_change()" >
          <label class="form-check-label" for="course_type_family">
              ファミリー
          </label>
      </div>
    </div>
  </div>
</div>
<script>
function course_type_change(obj){
}
</script>
