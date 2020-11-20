<?php
namespace App\Models\Traits;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


trait Scopes
{
  public function scopeSearch($query, $request){
    if( $request->has('search_word')){
      $query = $query->searchWord($request->get('search_word'));
    }
    if( $request->has('id')){
      $query = $query->where('id', $request->get('id'));
    }
    if( $request->has('search_status')){
      $query = $query->findStatuses($request->get('search_status'));
    }

    if($request->has('search_type')){
      $query = $query->findTypes($request->get('search_type'));
    }
    if(!empty($request->get('search_from_date')) || !empty($request->get('search_to_date'))){
      $query = $query->rangeDate($request->get('search_from_date'),$request->get('search_to_date'));
    }
    if(!empty($request->get('search_evaluation'))){
      $query = $query->reviewEvaluation($request->get('search_evaluation'));
    }

    if( $request->has('search_subject_id') && is_numeric( $request->get('search_subject_id'))){
      $search_subject_id = $request->get('search_subject_id');
      $query = $query->searchBySubjectId($search_subject_id);
    }

    return $query;
  }

  public function scopeSearchWord($query, $word){
    //title,remarks以外のものはオーバーライドして使う
    $search_words = $this->get_search_word_array($word);
    $query = $query->where(function($query)use($search_words){
      foreach($search_words as $_search_word){
        $_like = '%'.$_search_word.'%';
        $query = $query->orWhere('remarks','like', $_like)
          ->orWhere('title','like', $_like);
      }
    });
    return $query;
  }

  public function scopeFindStatuses($query, $vals, $is_not=false){
    if(is_string($vals)){
      $vals = [$vals];
    }
    if($is_not){
      return $query->whereNotIn('status', $vals);
    }else{
      return $query->whereIn('status', $vals);
    }
  }

  public function scopeFindTypes($query, $vals, $is_not=false)
  {
    if(is_string($vals)){
      $vals = [$vals];
    }
    if($is_not){
      return $query->whereNotIn('type',$vals);
    }else{
      return $query->whereIn('type', $vals);
    }
  }

  public function scopeRangeDate($query, $from_date, $to_date=null)
  {
    $field = 'created_at';
    //日付検索
    if($from_date == $to_date){
      $query = $query->where(DB::raw('cast(created_at as date)'), $from_date);
    }
    else {
      if(!empty($from_date)){
        $query = $query->where($field, '>=', $from_date);
      }
      if(!empty($to_date)){
        $query = $query->where($field, '<', $to_date);
      }
    }
    return $query;
  }

  public function scopeReviewEvaluation($query, $evaluation){
    return $query->whereHas('task_reviews', function($query) use ($evaluation) {
        $query->whereIn('evaluation',$evaluation);
    });
  }

  public function scopeSearchBySubjectId($query,$subject_id){
    return $query->whereHas('subjects', function($query) use ($subject_id) {
        $query->where('subjects.id', $subject_id);
    });
  }
}

?>
