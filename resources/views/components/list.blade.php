@if(count($items) > 0)

<table class="table table-hover">
  <tbody>
    <tr>
      @foreach($fields as $key => $field)
        <th>
          @if (isset($field['check_box']) && $field['check_box'] == true)
            <input class="frm-check-input icheck flat-green" type="checkbox" id="all_check" >
          @endif
          {{$field['label']}}
        </th>
      @endforeach
    </tr>
    @foreach($items as $i => $row)
      @component('components.list_item', ['row' => $row, 'index' => $i, 'fields' => $fields, 'domain' => $domain, 'domain_name' => $domain_name])
      @endcomponent
    @endforeach
  </tbody>
</table>
@else
<div class="alert">
  <h4><i class="icon fa fa-exclamation-triangle"></i>{{__('labels.no_data')}}</h4>
</div>
@endif


<script>
  $("a.bulk_action_button").on('click', function(){
    var checked = $("input.bulk_action_check").filter(":checked");
  });
  $(function(){
    base.pageSettinged("bulk_action",null);
    $("#bulk_action button.btn-submit").on('click', function(e){
      $(this).prop("disabled",true);
      $("#bulk_action form").submit();
    });

    $("body").on("ifChecked","#all_check", function(e){
      $("input[type=checkbox][name='list_check[]']").iCheck("check");
    });
    $("body").on("ifUnchecked","#all_check", function(e){
      $("input[type=checkbox][name='list_check[]']").iCheck("uncheck");
    });
    $("body").on("ifChecked",".bulk_action_check", function(e){
      var url = $("a.bulk_action_button").attr("page_url");
      $("a.bulk_action_button").show();
      set_action_url();
    });
    $("body").on("ifUnchecked",".bulk_action_check", function(e){
      if( $("input.bulk_action_check").filter(":checked").length == 0 ){
        $("a.bulk_action_button").hide();
      }
      set_action_url();
    });
    function set_action_url(){
      var checked = $("input.bulk_action_check").filter(":checked");
      var url = $("input[name=action_url]").val();
      checked.each(function(){
        url = url + "&list_check[]=" + $(this).val();
      });
      $("a.bulk_action_button").attr("page_url", url);
    }
  });
</script>