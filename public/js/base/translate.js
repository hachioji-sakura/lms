/**
* translateプラグイン
* @namespace
* @class translate
**/
;(function(root, undefined) {
	"use strict";
	var ja_to_en = {
		"当塾をお知りになった方法は何でしょうか" : "What is the way to know the winner?",
		"勤務実績確認は、本人のみ可能です" : "Work results confirmation can only be done by the person",
		"三者面談１（入塾後無料）" : "Three-party interview 1 (free after admission)",
		"三者面談２（入塾後有料）" : "Three interviews 2 (after the entry)",
		"講師設定を更新しました" : "Teacher settings have been updated",
		"契約者（生徒保護者）管理" : "Contractor (Student Parent) Management",
		"定期カレンダー反映方法" : "Periodic calendar reflection method",
		"スムーススクロール" : "Smooth scroll",
		"振替対象のみを表示" : "Exchange class only",
		"移動先を数値で取得" : "Get the move destination numerically",
		"面談（入塾前無料）" : "Interview (free before admission)",
		"体験授業申し込み登録" : "Trial class application registration",
		"定義項目の追加・編集" : "Add / Edit Definition Item",
		"アンカーの値取得" : "Get value of anchor",
		"グループレッスン" : "Group lessons",
		"学習管理システム" : "Learning management",
		"データがありません" : "there is no data",
		"マッチング決定理由" : "Matching decision reason",
		"体験申し込みの管理" : "Management of experience application",
		"進学、進級に関して" : "About going to school, going up",
		"アカウント設定" : "Account Setting",
		"タイトルの書式" : "Title format",
		"データロード中" : "Loading data",
		"パスワード設定" : "Password setting",
		"予定を確定する" : "Confirm schedule",
		"楢原・犬目校舎" : "arawore",
		"レッスン可能時間" : "Available lesson time",
		"レッスン可能曜日" : "Lesson day available",
		"レッスン希望科目" : "Lesson course",
		"イベント一覧" : "event list",
		"コメント一覧" : "Comment list",
		"スケジュール" : "Schedule",
		"ダットッチ校" : "dattchi",
		"マンツーマン" : "One to one",
		"体験申込一覧" : "Experience application list",
		"移動先を取得" : "Get destination",
		"イベントの管理" : "Event management",
		"コメントの管理" : "Comment management",
		"テストに関して" : "About the test",
		"フィルタリング" : "filtering",
		"宿題等に関して" : "Regarding homework etc",
		"生徒目標の管理" : "Manage student goals",
		"英会話レッスン" : "English conversation lesson",
		"英会話講師希望" : "English conversation teacher hope",
		"アカウント" : "account",
		"カレンダー" : "calendar",
		"キャンセル" : "Cancel",
		"ステータス" : "status",
		"ファミリー" : "family",
		"ログアウト" : "Logout",
		"予定確認中" : "Checking schedule",
		"予定調整中" : "Adjusting schedule ",
		"契約者一覧" : "Contractor List",
		"ご契約者様一覧" : "Contractor List",
		"日野豊田校" : "Hino Toyota school",
		"コメント登録" : "Comment registration",
		"コメント種別" : "Comment type",
		"事務員の登録" : "Add Officer",
		"体験授業一覧" : "Experience List",
		"体験申し込み" : "Experience application",
		"勤務可能時間" : "Working time",
		"学習に関して" : "About learning",
		"定義属性一覧" : "Definition attribute list",
		"定義属性登録" : "Definition attribute registration",
		"科目希望対応" : "Course request correspondence",
		"試験に関して" : "Regarding the exam",
		"タグ設定" : "Tag setting",
		"ユーザー" : "user",
		"事務一覧" : "Officer List",
		"休み予定" : "Rest schedule",
		"休講依頼" : "Request for lecture cancel",
		"依頼一覧" : "Request list",
		"出席済み" : "Presence",
		"出欠確認" : "Attendance confirmation",
		"勤務実績" : "Work record",
		"子安校舎" : "Koyasu School Building",
		"属性一覧" : "Attribute list",
		"振替対象" : "Exchange class",
		"振替登録" : "Add exchange",
		"授業予定" : "Class schedule",
		"授業履歴" : "Class history",
		"授業追加" : "Add class",
		"日付降順" : "Date descending order",
		"曜日名称" : "Name of the day",
		"曜日略称" : "Abbreviated day of the week",
		"最大時間" : "Maximum time",
		"最小時間" : "Minimum time",
		"本日予定" : "Planned today",
		"生徒一覧" : "Student list",
		"目標一覧" : "Goal list",
		"直近予定" : "Latest schedule",
		"絞り込み" : "Filter",
		"試験監督" : "Examination supervisor",
		"講師一覧" : "Teacher List",
		"講師設定" : "Teacher settings",
		"ピアノ経験" : "Piano experience",
		"生徒の管理" : "Student management",
		"講師の登録" : "Teacher registration",
		"いいえ" : "No",
		"その他" : "Other",
		"クリア" : "clear",
		"トップ" : "top",
		"南口校" : "South School",
		"国立校" : "National school",
		"月名称" : "Month name",
		"月略称" : "Month abbreviation",
		"表示順" : "Order",
		"選択可" : "Selectable",
		"レッスン" : "lesson",
		"事務兼務" : "Teacher & Officer",
		"事務設定" : "Officer setting",
		"代講依頼" : "Request for alternative lecture",
		"休み種別" : "Rest Type",
		"休講申請" : "Application for Lecture Cancel",
		"削除済み" : "deleted",
		"勤務予定" : "Work schedule",
		"定義項目" : "Defining project",
		"担当学年" : "Department year",
		"担当科目" : "Subject in charge",
		"授業形式" : "Class format",
		"授業時間" : "class time",
		"更新日時" : "Update date",
		"生徒特性" : "Student characteristics",
		"生徒種別" : "Student type",
		"登録日時" : "Registered Date",
		"登録済み" : "Registered",
		"目標登録" : "Goal registration",
		"目標種別" : "Target type",
		"講師特性" : "Teacher characteristics",
		"講師登録" : "Add Teacher",
		"はい" : "Yes",
		"事務" : "Officer",
		"今日" : "today",
		"休み" : "Rest",
		"休講" : "Lecture cancel",
		"作業" : "work",
		"化学" : "Chemistry",
		"場所" : "place",
		"変更" : "Change",
		"数Ⅲ" : "MathIII",
		"数学" : "Mathematics",
		"物理" : "Physics",
		"理科" : "Science",
		"日付" : "date",
		"本校" : "Main school",
		"欠席" : "Absent",
		"演習" : "Exercise",
		"算数" : "Moth",
		"詳細" : "Details",
		"追加" : "Add",
		"今日" : "today",
		"コース" : "course",
		"ルート" : "root",
		"仮登録" : "Temporary registration",
		"未公開" : "Unpublished",
		"未対応" : "Not compatible",
		"習い事" : "Lessons",
		"難易度" : "difficulty",
		"日" : "Day",
		"月" : "Month",
		"週" : "week",
		"削除" : "Delete",
		"学年" : "School year",
		"履歴" : "History",
		"操作" : "operation",
		"予定" : "Schedule",
		"業務" : "Business"
	};

	var public_method = {
		translate_start : translate_start,
	};
	function translate_start(selecter){
		var target_translate = localStorage.getItem("target_translate");
		if(target_translate==null) target_translate = {};
		else target_translate = JSON.parse(target_translate);

		if(util.isEmpty(selecter)) selecter ="body";
		//要素内テキストを抽出
		$("*:not(rb):not(script):not(footer):not(dl):not(ul):not([alt='student_name']):not([alt='teacher_name'])", $(selecter)).each(function(){
			var t = $(this).text().trim();
			var _html = $(this).html();
			t = t.replace_all("\n","");
			if(t.replace_all(" ", "") =="") t="";
			t = t.split(" ");
			for(var i=0,n=t.length;i<n;i++){
				if(t[i].length < 1) continue;
				if(!util.isZenkaku(t[i])) continue;
				if(t[i].replace_all(" ", "") =="") continue;
				if(!util.isEmpty(ja_to_en[t[i]])){
					_html = _html.replace_all(t[i], ja_to_en[t[i]]);
				}
				else {
					target_translate[t[i]] ="";
				}
			}
			$(this).html(_html);
		});
		target_translate = JSON.stringify(target_translate);
		localStorage.setItem("target_translate", target_translate);
	}
	root.translate = $.extend({}, root.translate, public_method);

})(this);
$(function(){
	//translate.translate_start();
});
