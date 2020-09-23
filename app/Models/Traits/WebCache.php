<?php
namespace App\Models\Traits;
use Illuminate\Support\Facades\Cache;

trait WebCache
{

  public funtion _cache_get($key)
  {
    if(Cache::has($key)){
      return Cache::get($key);
    }
    return null;
  }
  public funtion _cache_put($key, $user_id, $data, $minutes=60)
  {
    Cache::tags([$this->table, 'user_id='.$user_id])->put($key, $data, $minutes);
  }
  public funtion _user_cache_delete($user_id)
  {
    Cache::tags([$this->table, 'user_id='.$user_id])->flush();
  }
  public function CacheDelete(){
  }
}
