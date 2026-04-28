<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attributeを承認してください。',
    'accepted_if' => ':otherが:valueの場合、:attributeを承認してください。',
    'active_url' => ':attributeは有効なURLではありません。',
    'after' => ':attributeには:date以降の日付を指定してください。',
    'after_or_equal' => ':attributeには:date以降の日付を指定してください。',
    'alpha' => ':attributeはアルファベットのみで入力してください。',
    'alpha_dash' => ':attributeはアルファベット、数字、ハイフン、アンダースコアのみ使用できます。',
    'alpha_num' => ':attributeはアルファベットと数字のみで入力してください。',
    'any_of' => ':attributeが無効です。',
    'array' => ':attributeは配列で指定してください。',
    'ascii' => ':attributeは半角英数字と記号のみで入力してください。',
    'before' => ':attributeには:date以前の日付を指定してください。',
    'before_or_equal' => ':attributeには:date以前の日付を指定してください。',
    'between' => [
        'array' => ':attributeは:min個から:max個の間で指定してください。',
        'file' => ':attributeは:min KBから:max KBの間で指定してください。',
        'numeric' => ':attributeは:minから:maxの間で指定してください。',
        'string' => ':attributeは:min文字から:max文字の間で入力してください。',
    ],
    'boolean' => ':attributeはtrueまたはfalseを指定してください。',
    'can' => ':attributeに権限のない値が含まれています。',
    'confirmed' => ':attributeが一致しません。',
    'contains' => ':attributeに必須の値が不足しています。',
    'current_password' => 'パスワードが正しくありません。',
    'date' => ':attributeは正しい日付を入力してください。',
    'date_equals' => ':attributeは:dateと同じ日付である必要があります。',
    'date_format' => ':attributeは:format形式で入力してください。',
    'decimal' => ':attributeは小数点以下:decimal桁で入力してください。',
    'declined' => ':attributeを拒否してください。',
    'declined_if' => ':otherが:valueの場合、:attributeを拒否してください。',
    'different' => ':attributeと:otherは異なる値を指定してください。',
    'digits' => ':attributeは:digits桁で入力してください。',
    'digits_between' => ':attributeは:min桁から:max桁の間で入力してください。',
    'dimensions' => ':attributeの画像サイズが無効です。',
    'distinct' => ':attributeに重複した値があります。',
    'doesnt_contain' => ':attributeは次の値を含むことができません: :values。',
    'doesnt_end_with' => ':attributeは次のいずれかで終わってはいけません: :values。',
    'doesnt_start_with' => ':attributeは次のいずれかで始まってはいけません: :values。',
    'email' => ':attributeは正しいメールアドレス形式で入力してください。',
    'encoding' => ':attributeは:encodingエンコーディングである必要があります。',
    'ends_with' => ':attributeは次のいずれかで終わる必要があります: :values。',
    'enum' => '選択された:attributeは有効ではありません。',
    'exists' => '選択された:attributeは無効です。',
    'extensions' => ':attributeは次の拡張子のいずれかである必要があります: :values。',
    'file' => ':attributeはファイルを指定してください。',
    'filled' => ':attributeを入力してください。',
    'gt' => [
        'array' => ':attributeは:value個より多く指定してください。',
        'file' => ':attributeは:value KBより大きいファイルを指定してください。',
        'numeric' => ':attributeは:valueより大きい値を指定してください。',
        'string' => ':attributeは:value文字より長く入力してください。',
    ],
    'gte' => [
        'array' => ':attributeは:value個以上指定してください。',
        'file' => ':attributeは:value KB以上のファイルを指定してください。',
        'numeric' => ':attributeは:value以上の値を指定してください。',
        'string' => ':attributeは:value文字以上で入力してください。',
    ],
    'hex_color' => ':attributeは正しい16進カラーコードで入力してください。',
    'image' => ':attributeは画像ファイルを指定してください。',
    'in' => '選択された:attributeは無効です。',
    'in_array' => ':attributeは:otherに存在する必要があります。',
    'in_array_keys' => ':attributeには次のキーのうち少なくとも1つを含めてください: :values。',
    'integer' => ':attributeは整数で入力してください。',
    'ip' => ':attributeは正しいIPアドレスを入力してください。',
    'ipv4' => ':attributeは正しいIPv4アドレスを入力してください。',
    'ipv6' => ':attributeは正しいIPv6アドレスを入力してください。',
    'json' => ':attributeは正しいJSON形式で指定してください。',
    'list' => ':attributeはリスト形式で指定してください。',
    'lowercase' => ':attributeは小文字で入力してください。',
    'lt' => [
        'array' => ':attributeは:value個より少なく指定してください。',
        'file' => ':attributeは:value KBより小さいファイルを指定してください。',
        'numeric' => ':attributeは:valueより小さい値を指定してください。',
        'string' => ':attributeは:value文字より少なく入力してください。',
    ],
    'lte' => [
        'array' => ':attributeは:value個以下で指定してください。',
        'file' => ':attributeは:value KB以下のファイルを指定してください。',
        'numeric' => ':attributeは:value以下の値を指定してください。',
        'string' => ':attributeは:value文字以下で入力してください。',
    ],
    'mac_address' => ':attributeは正しいMACアドレスを入力してください。',
    'max' => [
        'array' => ':attributeは:max個以下で指定してください。',
        'file' => ':attributeは:max KB以下のファイルを指定してください。',
        'numeric' => ':attributeは:max以下で指定してください。',
        'string' => ':attributeは:max文字以下で入力してください。',
    ],
    'max_digits' => ':attributeは最大:max桁で入力してください。',
    'mimes' => ':attributeは次のファイル形式を指定してください: :values。',
    'mimetypes' => ':attributeは次のファイル形式を指定してください: :values。',
    'min' => [
        'array' => ':attributeは:min個以上指定してください。',
        'file' => ':attributeは:min KB以上のファイルを指定してください。',
        'numeric' => ':attributeは:min以上で指定してください。',
        'string' => ':attributeは:min文字以上で入力してください。',
    ],
    'min_digits' => ':attributeは最低:min桁で入力してください。',
    'missing' => ':attributeは指定できません。',
    'missing_if' => ':otherが:valueの場合、:attributeは指定できません。',
    'missing_unless' => ':otherが:valueでない場合、:attributeは指定できません。',
    'missing_with' => ':valuesが指定されている場合、:attributeは指定できません。',
    'missing_with_all' => ':valuesが全て指定されている場合、:attributeは指定できません。',
    'multiple_of' => ':attributeは:valueの倍数で指定してください。',
    'not_in' => '選択された:attributeは無効です。',
    'not_regex' => ':attributeの形式が無効です。',
    'numeric' => ':attributeは数字で入力してください。',
    'password' => [
        'letters' => ':attributeには少なくとも1文字以上のアルファベットを含めてください。',
        'mixed' => ':attributeには少なくとも1文字以上の大文字と小文字を含めてください。',
        'numbers' => ':attributeには少なくとも1つ以上の数字を含めてください。',
        'symbols' => ':attributeには少なくとも1つ以上の記号を含めてください。',
        'uncompromised' => '指定された:attributeはデータ漏洩により流出しています。別の:attributeを指定してください。',
    ],
    'present' => ':attributeを指定してください。',
    'present_if' => ':otherが:valueの場合、:attributeを指定してください。',
    'present_unless' => ':otherが:valueでない場合、:attributeを指定してください。',
    'present_with' => ':valuesが指定されている場合、:attributeを指定してください。',
    'present_with_all' => ':valuesが全て指定されている場合、:attributeを指定してください。',
    'prohibited' => ':attributeは指定できません。',
    'prohibited_if' => ':otherが:valueの場合、:attributeは指定できません。',
    'prohibited_if_accepted' => ':otherが承認された場合、:attributeは指定できません。',
    'prohibited_if_declined' => ':otherが拒否された場合、:attributeは指定できません。',
    'prohibited_unless' => ':otherが:valuesでない限り、:attributeは指定できません。',
    'prohibits' => ':attributeは:otherの指定を許可しません。',
    'regex' => ':attributeの形式が無効です。',
    'required' => ':attributeを入力してください。',
    'required_array_keys' => ':attributeは次のキーを含む必要があります: :values。',
    'required_if' => ':otherが:valueの場合、:attributeを入力してください。',
    'required_if_accepted' => ':otherが承認された場合、:attributeを入力してください。',
    'required_if_declined' => ':otherが拒否された場合、:attributeを入力してください。',
    'required_unless' => ':otherが:valuesでない場合、:attributeを入力してください。',
    'required_with' => ':valuesが指定されている場合、:attributeを入力してください。',
    'required_with_all' => ':valuesが全て指定されている場合、:attributeを入力してください。',
    'required_without' => ':valuesが指定されていない場合、:attributeを入力してください。',
    'required_without_all' => ':valuesが全て指定されていない場合、:attributeを入力してください。',
    'same' => ':attributeと:otherは一致する必要があります。',
    'size' => [
        'array' => ':attributeは:size個指定してください。',
        'file' => ':attributeは:size KBのファイルを指定してください。',
        'numeric' => ':attributeは:sizeを指定してください。',
        'string' => ':attributeは:size文字で入力してください。',
    ],
    'starts_with' => ':attributeは次のいずれかで始まる必要があります: :values。',
    'string' => ':attributeは文字列で入力してください。',
    'timezone' => ':attributeは正しいタイムゾーンを指定してください。',
    'unique' => '指定された:attributeは既に使用されています。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'uppercase' => ':attributeは大文字で入力してください。',
    'url' => ':attributeは正しいURLを入力してください。',
    'ulid' => ':attributeは正しいULIDを指定してください。',
    'uuid' => ':attributeは正しいUUIDを指定してください。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'アカウント名',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'password_confirmation' => 'パスワード（確認）',
        'current_password' => '現在のパスワード',
    ],

];



