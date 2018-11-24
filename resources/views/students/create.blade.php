@include('students.domain')
@section('title')
  @yield('domain_name')登録
@endsection
@extends('dashboard.common')
@include('dashboard.menu.page_sidemenu')

@section('contents')
<div class="card-header">
  <h3 class="card-title">@yield('title')</h3>
</div>
<div class="card-body">
  <form id="edit" method="POST" action="/@yield('domain')">
  @csrf
    <div class="row">
      <div class="col-12 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="name_last">
            氏
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="name_last" name="name_last" class="form-control" placeholder="例：山田" required="true" inputtype="zenkaku">
        </div>
      </div>
      <div class="col-12 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="name_first">
            名
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="name_first" name="name_first" class="form-control" placeholder="例：太郎" required="true" inputtype="zenkaku">
        </div>
      </div>
      <div class="col-12 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="kana_last">
            氏（カナ）
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="kana_last" name="kana_last" class="form-control" placeholder="例：ヤマダ" required="true" inputtype="zenkakukana">
        </div>
      </div>
      <div class="col-12 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="kana_first">
            名（カナ）
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="kana_first" name="kana_first" class="form-control" placeholder="例：タロウ" required="true" inputtype="zenkakukana">
        </div>
      </div>
      <div class="col-12 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="birth_day">
            生年月日
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input id="birth_day" type="text" class="form-control{{ $errors->has('birth_day') ? ' is-invalid' : '' }}" name="birth_day" value="{{ old('birth_day') }}" inputtype="date" plaeholder="例：2000/01/01" required="true">

          @if ($errors->has('birth_day'))
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('birth_day') }}</strong>
              </span>
          @endif
        </div>
      </div>
      <div class="col-12 col-lg-6 col-md-6">
        <div class="form-group">
          <label for="password-confirm">
            性別
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <div class="input-group">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="gender" id="gender_2" {{ old('gender') ? 'checked' : '' }} value="2" required="true">
                <label class="form-check-label" for="gender_2">
                    女性
                </label>
            </div>
            <div class="form-check ml-2">
                <input class="form-check-input" type="radio" name="gender" id="gender_1" {{ old('gender') ? 'checked' : '' }} value="1" required="true">
                <label class="form-check-label" for="gender_1">
                    男性
                </label>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="form-group">
          <label for="email">
            メールアドレス
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="text" id="email" name="email" class="form-control" placeholder="例：hachioji@sakura.com" required="true" inputtype="email" query_check="users/email" query_check_error="このメールアドレスは登録済みです">
        </div>
      </div>
      <div class="col-12">
        <div class="form-group">
          <label for="password">
            パスワード
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="password" id="password" name="password" class="form-control" placeholder="半角英数8文字以上16文字未満" minlength=8 maxlength=16 required="true">
        </div>
      </div>
      <div class="col-12">
        <div class="form-group">
          <label for="password-confirm">
            パスワード（確認）
            <span class="right badge badge-danger ml-1">必須</span>
          </label>
          <input type="password" id="password-confirm" name="password-confirm" class="form-control" placeholder="" minlength=8 maxlength=16 required="true" equal="password" equal‗error="パスワードが一致しません">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-lg-6 col-md-6">
          <button type="submit" class="btn btn-primary btn-block">
              登録する
          </button>
          @if(isset($error_message))
            <span class="invalid-feedback d-block ml-2 " role="alert">
                <strong>{{$error_message}}</strong>
            </span>
          @endif
      </div>
      <div class="col-12 col-lg-6 col-md-6">
          <button type="button" class="btn btn-secondary btn-block" accesskey="cancel" onClick="history.back();">
              キャンセル
          </button>
      </div>
    </div>
  </form>
</div>
<script>
$(function(){
  @if(env('APP_DEBUG'))
  var name1 = [
    {"name":"佐藤", "kana" : "サトウ", "alpha" : "sato"},
    {"name":"鈴木", "kana" : "スズキ", "alpha" : "suzuki"},
    {"name":"加藤", "kana" : "かとう", "alpha" : "kato"},
    {"name":"山口", "kana" : "やまぐち", "alpha" : "yamaguchi"},
    {"name":"安藤", "kana" : "あんどう", "alpha" : "ando"},
    {"name":"田中", "kana" : "たなか", "alpha" : "tanaka"},
    {"name":"杉山", "kana" : "すぎやま", "alpha" : "sugiyama"},
    {"name":"奥田", "kana" : "おくだ", "alpha" : "okuda"},
    {"name":"関口", "kana" : "せきぐち", "alpha" : "sekiguchi"},
    {"name":"篠崎", "kana" : "しのざき", "alpha" : "shinozaki"},
    {"name":"山崎", "kana" : "やまざき", "alpha" : "yamazaki"},
    {"name":"市田山", "kana" : "いちだやま", "alpha" : "ichidayama"}
  ];
  var name2 = [
    {"name" : "凪", "kana" : "なぎ","gender" : 1},
    {"name" : "一途", "kana" : "かずと","gender" : 1},
    {"name" : "日々輝", "kana" : "ひびき","gender" : 1},
    {"name" : "律", "kana" : "りつ","gender" : 1},
    {"name" : "理人", "kana" : "りひと","gender" : 1},
    {"name" : "明日真", "kana" : "あすま","gender" : 1},
    {"name" : "蛍", "kana" : "けい","gender" : 1},
    {"name" : "素晴", "kana" : "すばる","gender" : 1},
    {"name" : "今日平", "kana" : "きょうへい","gender" : 1},
    {"name" : "要", "kana" : "かなめ","gender" : 1},
    {"name" : "和音", "kana" : "かずね","gender" : 1},
    {"name" : "明希人", "kana" : "あきと","gender" : 1},
    {"name" : "礼", "kana" : "れい","gender" : 1},
    {"name" : "飛人", "kana" : "ひびと","gender" : 1},
    {"name" : "虹太郎", "kana" : "こうたろう","gender" : 1},
    {"name" : "漣", "kana" : "れん","gender" : 1},
    {"name" : "舞", "kana" : "まい", "gender" : 2 },
    {"name" : "真子", "kana" : "まこ", "gender" : 2 },
    {"name" : "千鶴子", "kana" : "ちづこ", "gender" : 2 },
    {"name" : "綾", "kana" : "あや", "gender" : 2 },
    {"name" : "葉月", "kana" : "はづき", "gender" : 2 },
    {"name" : "今日子", "kana" : "きょうこ", "gender" : 2 },
    {"name" : "薫", "kana" : "かおり", "gender" : 2 },
    {"name" : "如月", "kana" : "きさらぎ", "gender" : 2 },
    {"name" : "瑠璃子", "kana" : "るりこ", "gender" : 2 },
    {"name" : "華", "kana" : "はな", "gender" : 2 },
    {"name" : "灯里", "kana" : "あかり", "gender" : 2 },
    {"name" : "美和子", "kana" : "みわこ", "gender" : 2 },
    {"name" : "幸", "kana" : "さち、ゆき", "gender" : 2 },
    {"name" : "和葉", "kana" : "かずは", "gender" : 2 },
    {"name" : "小夜子", "kana" : "さよこ", "gender" : 2 },
    {"name" : "都", "kana" : "みやこ", "gender" : 2 },
    {"name" : "佳乃", "kana" : "よしの", "gender" : 2 },
    {"name" : "香耶乃", "kana" : "かやの", "gender" : 2 },
    {"name" : "円", "kana" : "まどか", "gender" : 2 },
    {"name" : "雛菊", "kana" : "ひなぎく", "gender" : 2 },
    {"name" : "奈々緒", "kana" : "ななお", "gender" : 2 },
    {"name" : "縁", "kana" : "ゆかり", "gender" : 2 },
    {"name" : "翠子", "kana" : "みどりこ", "gender" : 2 },
    {"name" : "明香里", "kana" : "あかり", "gender" : 2 },
    {"name" : "雅", "kana" : "みやび", "gender" : 2 },
    {"name" : "琴美", "kana" : "ことみ", "gender" : 2 },
    {"name" : "紗弥香", "kana" : "さやか", "gender" : 2 },
    {"name" : "詩", "kana" : "うた", "gender" : 2 },
    {"name" : "香代", "kana" : "かよ", "gender" : 2 },
    {"name" : "寿々花", "kana" : "すずか", "gender" : 2 }
  ];
  var n1 = (Math.random()*100|0)% name1.length;
  var n2 = (Math.random()*100|0) % name2.length;
  var by = 2018-(Math.random()*100|0)%15;
  var bm = (Math.random()*100|0)%12+1;
  var bd = (Math.random()*100|0)%28;
  var data = {
    "name_last" : name1[n1].name,
    "name_first" : name2[n2].name,
    "kana_last" : name1[n1].kana,
    "kana_first" : name2[n2].kana,
    "gender" : name2[n2].gender,
    "birth_day" : by+"/"+(bm<10 ? "0"+bm : bm)+"/"+(bd<10 ? "0"+bd : bd),
    "email" : name1[n1].alpha+""+((Math.random()*1000)|0)+"@gmail.com",
    "password-confirm" : "hogehoge",
    "password" : "hogehoge"
  };
  base.pageSettinged("edit", data);
  @else
  base.pageSettinged("edit", null);
  @endif
	$(".btn[type=submit]").on("click", function(){
		if(!front.validateFormValue("edit")) return false;
    $("#edit").submit();
	});
})
</script>
@endsection
