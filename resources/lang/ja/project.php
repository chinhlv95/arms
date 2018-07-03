<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Project language
	|--------------------------------------------------------------------------
	*/

	'page_title' => 'プロジェクト管理',
	'edit_page_title' => 'プロジェクト編集',
	'menu' => [
		'list' => 'プロジェクトリスト'
	],
	'list_project' => [
		'label_create' => '作成',
		'label_show' => '表示',
		'label_entries' => 'エントリ',
		'label_search' => '検索',
		'label_search_place_holder' => 'ネーム',
		'label_no' => '項番',
		'label_name' => 'ネーム',
		'label_action' => 'アクション',
		'label_edit' => '編集',
		'label_dialog_title' => 'システムからのお知らせ',
		'label_delete' => '削除',
		'label_dialog_delete_title' => 'プロジェクト削除',
		'label_delete_member' => 'プロジェクトメンバーを削除する',
		'label_delete_item' => '削除してもよろしいでしょうか？',
		'label_dialog_yes' => '保存',
		'label_dialog_close' => '閉じる',
		'label_delete_success' => 'プロジェクトを削除しました。',
		'label_delete_error' => '削除できません',
		'label_sync' => '同期',
		'label_assign' => 'アサイン',
		'label_member' => 'トータルメンバー',
		'label_project_choose' => 'メンバーリスト',
		'label_project_mapping' => 'マッチング',
		'label_project_mapping_success' => 'マッチング出来ました',
		'label_project_mapping_error' => 'マッチングできませんでした',
		'label_project_mapping_error_format' => 'テンプレートフォーマットが違ってました',
		'label_upload_file' => 'ファイルアップロード',
		'label_upload' => 'アップロード',
		'label_save' => '保存',
		'label_customer_name' => '顧客名',
		'label_project_jp_name' => '請求会社',
		'label_project_name' => 'プロジェクト名',
		'label_arms_project_name' => 'ARMSプロジェクト名',
		'label_arms_project_choose' => '--プロジェクトを選ぶ--',
		'label_client_choose' => '--クライアントを選ぶ--',
		'label_project_japan' => '日本側の名',
		'label_assigned_member_expanded' => 'メンバー表示',
		'label_assigned_member_collapsed' => '閉じる',
	],
	'create_project' => [
		'label_title' => 'プロジェクト作成',
		'label_button_save' => '保存',
		'plantime' => '予定日付',
		'actualtime' => '実際日付'
	],
	'message' => [
		'required_project_name' => 'プロジェクトが必須',
		'add_project_success' => '追加しました',
		'add_project_error' => '追加できません',
		'update_project_success' => '更新しました',
		'update_project_error' => '更新できません',
		'date_plan_equal_error' => 'スタート予定日は終了予定日より、小さくなければなりません。',
		'date_actual_equal_error' => 'スタート実際日は終了実際日より、小さくなければなりません。',
		'import_format_error' => 'ファイルは正しい形式でなければなりません。（Excel）',
		'import_file_required' => 'ファイルを選択してください',
		'import_template_error' => 'ファイルテンプレートが違います。',
		'uploading' => 'アップロード中....',
		'has_collumn_error' => 'コラムエラーがあります、ログ表示',
		'error_foreign_delete' => 'アサインされているメンバーがいるため、削除できませんでした。'
	]
];