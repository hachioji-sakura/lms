<?php
namespace App\Models\Traits;
use Illuminate\Support\Facades\Cache;

trait WebCache
{
  protected $default_expired_minutes = 60;
  public function get_user_cache($cache_key, $save_id){
    try {
      return $this->get_cache($cache_key, 'user_id='.$save_id);
    }
    catch(\Exception $e){
      \Log::error('get_user_cache error:'.$e->getMessage());
      Cache::flush();
    }
  }
  public function put_user_cache($cache_key, $save_id, $data){
    try {
      return $this->put_cache($cache_key, 'user_id='.$save_id, $data);
    }
    catch(\Exception $e){
      \Log::error('put_user_cache error:'.$e->getMessage());
      Cache::flush();
    }
  }
  public function delete_user_cache($save_id){
    try {
      return $this->delete_cache('user_id='.$save_id);
    }
    catch(\Exception $e){
      \Log::error('delete_user_cache error:'.$e->getMessage());
      Cache::flush();
    }
  }
  /*
    キャッシュの保存形式
     1．保存するキー名＝prefix+id (ex. prefix:"user_id=" , id:"123")
     2．保存するデータの形式  = [$table_name][$save_item_name] = $save_data
        DB更新時に、削除するキャッシュの単位は、テーブル単位で行う
     3. 保存期限＝デフォルト　1時間
  */
  private function get_cache($cache_key, $save_id){
    \Log::warning("get_cache:$cache_key, $save_id");
    if(Cache::has($save_id)){
      $_cache = Cache::get($save_id);
      if(!isset($_cache['expired'])) {
        //期限設定がないCacheは削除
        Cache::forget($save_id);
        return null;
      }
      if(strtotime('now') > $_cache['expired']){
        //期限切れ
        Cache::forget($save_id);
        return null;
      }
      if(!isset($_cache[$this->table])) return null;
      if(!isset($_cache[$this->table][$cache_key])) return null;
      \Log::warning("get_cache: OK");
      return $_cache[$this->table][$cache_key];
    }
    return null;
  }
  private function put_cache($cache_key, $save_id, $data){
    $_cache = [];
    \Log::warning("put_cache:$cache_key, $save_id");
    if(Cache::has($save_id)){
      $_cache = Cache::get($save_id);
      //期限切れ　Cacheデータを持ち越さずに作る
      if(isset($_cache['expired']) && strtotime('now') > $_cache['expired']){
        $_cache = [];
      }
    }
    if(!isset($_cache[$this->table])) $_cache[$this->table] = [];
    $_cache[$this->table][$cache_key] = $data;
    //期限設定
    $_cache['expired'] = strtotime('+ '.$this->default_expired_minutes.' minute');
    Cache::put($save_id, $_cache, $this->default_expired_minutes);
  }
  private function delete_cache($save_id){
    $_cache = [];
    if(Cache::has($save_id)){
      $_cache = Cache::get($save_id);
      //期限切れ　Cacheデータそのものを削除
      if(isset($_cache['expired']) && strtotime('now') > $_cache['expired']){
        Cache::forget($save_id);
        return ;
      }
      //$this->tableのデータを削除
      if(isset($_cache[$this->table])) $_cache[$this->table] = [];
      Cache::put($save_id, $_cache, $this->default_expired_minutes);
    }
  }
}
