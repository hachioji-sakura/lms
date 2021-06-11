<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Scopes;

/**
 * App\Models\Subject
 *
 * @property int $id
 * @property string $name 科目名
 * @property string|null $remarks 備考
 * @property int|null $sort_no ソートナンバー
 * @property int $create_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $create_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Curriculum[] $curriculums
 * @property-read mixed $create_user_name
 * @property-read mixed $created_date
 * @property-read mixed $importance_label
 * @property-read mixed $target_user_name
 * @property-read mixed $type_name
 * @property-read mixed $updated_date
 * @property-read \App\User $target_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone fieldWhereIn($field, $vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findStatuses($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTargetUser($val)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone findTypes($vals, $is_not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone pagenation($page, $line)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject query()
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone rangeDate($from_date, $to_date = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject search($request)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone searchTags($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject searchWord($word)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone sortCreatedAt($sort)
 * @method static \Illuminate\Database\Eloquent\Builder|Milestone status($val)
 * @mixin \Eloquent
 */
class Subject extends Milestone
{
    //
    use Scopes;
    protected $connection = 'mysql';
    protected $table = 'lms.subjects';
    protected $guarded = array('id');

    public function curriculums(){
      return $this->belongsToMany('App\Models\Curriculum','curriculum_subject','subject_id','curriculum_id')->withTimestamps();
    }

    public function textbooks(){
      return $this->belongsToMany('App\Models\Textbook','textbook_subjects');
    }

    public function details(){
      $item = $this;
      $item["created_date"] = $this->created_at_label();
      $item["updated_date"] = $this->updated_at_label();
      $item["create_user_name"] = $this->create_user->details()->name();
      return $item;
    }

    public function update_curriculum($form, $curriculum_ids = null ){
      $this->update($form);
      $this->curriculums()->sync($curriculum_ids);
      return $this;
    }


    public function scopeSearchWord($query, $word){
      $search_words = $this->get_search_word_array($word);
      $query = $query->where(function($query)use($search_words){
        foreach($search_words as $_search_word){
          $_like = '%'.$_search_word.'%';
          $query = $query->orWhere('remarks','like', $_like)
            ->orWhere('name','like', $_like);
        }
      });
      return $query;
    }

    public function dispose(){
      $this->curriculums()->detach();
      $this->delete();
      return $this;
    }

    public function add_with_curriculums_by_name($subject_name,$curriculum_names){
      $sort_no = Subject::all()->max('sort_no') + 1;
      $subject_form = [
        'name' => $subject_name,
        'sort_no' => $sort_no,
        'create_user_id' => 1,
      ];
      $this->fill($subject_form)->save();

      $curriculum_form = [];
      foreach($curriculum_names as $name){
        $sort_no = Curriculum::all()->max('sort_no') + 1;
        $curriculum_form[] = [
          'name' => $name,
          'sort_no' => $sort_no,
          'create_user_id' => 1,
        ];
      }
      $this->curriculums()->createMany($curriculum_form);
    }
}
