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

    'accepted'             => ':attribute phải được chấp nhận.',
    'active_url'           => 'Bắt buộc :attribute  URL phải hơp lệ.',
    'after'                => 'Bắt buộc :attribute  phải là một ngày sau :date.',
    'after_or_equal'       => 'Bắt buộc :attribute phải là ngày sau hoặc bằng :date.',
    'alpha'                => ' :attribute chỉ có thể chứa chữ cái.',
    'alpha_dash'           => ' :attribute chỉ có thể chứa chữ cái, số, và dấu ngạch ngang.',
    'alpha_num'            => ' :attribute chỉ có thể chứa chữ cái và số.',
    'array'                => 'Bắt buộc :attribute  phải là mảng.',
    'before'               => 'Bắt buộc :attribute phải là một ngày trước :date.',
    'before_or_equal'      => 'Bắt buộc :attribute phải là ngày sau hoặc bằng :date.',
    'between'              => [
        'numeric' => 'Bắt buộc :attribute phải là giữa :min và :max.',
        'file'    => 'Bắt buộc :attribute phải nằm trong khoảng :min và :max kilobytes.',
        'string'  => 'Bắt buộc :attribute phải nằm trong khoảng :min và :max characters.',
        'array'   => 'Bắt buộc :attribute phải nằm trong khoảng :min và :max items.',
    ],
    'boolean'              => 'Bắt buộc :attribute trường phải đúng hoặc sai.',
    'confirmed'            => ':attribute xác nhận không khớp',
    'date'                 => ':attribute không phải là ngày hợp lệ .',
    'date_format'          => ' :attribute không khớp với định dạng  :format.',
    'different'            => 'Bắt buộc :attribute và :other phải khác nhau.',
    'digits'               => 'Bắt buộc :attribute phải là :digits digits.',
    'digits_between'       => 'Bắt buộc :attribute phải nằm trong khoảng :min và :max digits.',
    'dimensions'           => 'Bắt buộc :attribute kích thước hình ảnh hợp lệ.',
    'distinct'             => 'Bắt buộc :attribute field has a duplicate value.',
    'email'                => 'Bắt buộc :attribute phải là một địa chỉ email hợp lệ .',
    'exists'               => 'Lựa chọn :attribute không hợp lệ .',
    'file'                 => 'Bắt buộc :attribute phải là một tập tin.',
    'filled'               => 'Bắt buộc :attribute phải có giá trị.',
    'image'                => 'Bắt buộc :attribute phải là hình ảnh.',
    'in'                   => 'Lựa chọn :attribute không hợp lệ.',
    'in_array'             => ':attribute không tồn tại trong :other.',
    'integer'              => 'Bắt buộc :attribute phải là số nguyên .',
    'ip'                   => 'Bắt buộc :attribute phải có địa chỉ IP hợp lệ.',
    'ipv4'                 => 'Bắt buộc :attribute phải có địa chỉ IPv4 hợp lệ.',
    'ipv6'                 => 'Bắt buộc :attribute phải có địa chỉ IPv6 hợp lệ.',
    'json'                 => 'Bắt buộc :attribute phải có chuỗi JSON hợp lệ.',
    'max'                  => [
        'numeric' => 'Bắt buộc :attribute không được lớn hơn :max.',
        'file'    => 'Bắt buộc :attribute không được lớn hơn :max kilobytes.',
        'string'  => 'Bắt buộc :attribute không được lớn hơn :max characters.',
        'array'   => 'Bắt buộc :attribute có thể không có nhiều hơn :max items.',
    ],
    'mimes'                => 'Bắt buộc :attribute phải là kiểu file: :values.',
    'mimetypes'            => 'Bắt buộc  :attribute phải là kiểu file: :values.',
    'min'                  => [
        'numeric' => 'Bắt buộc :attribute phải ít nhất :min.',
        'file'    => 'Một :attribute phải ít nhất :min kilobytes.',
        'string'  => 'Một :attribute phải ít nhất :min characters.',
        'array'   => 'Một :attribute phải có ít nhất :min items.',
    ],
    'not_in'               => 'Lựa chọn :attribute là không hợp lệ.',
    'numeric'              => 'Bắt buộc  :attribute phải là số.',
    'present'              => 'Bắt buộc :attribute phải có .',
    'regex'                => 'Một :attribute định dạng không hợp lệ.',
    'required'             => ' :attribute là trường bắt buộc.',
    'required_if'          => ' :attribute trường bắt buộc khi :other là :value.',
    'required_unless'      => ' :attribute trường bắt buộc trừ khi :other trong :values.',
    'required_with'        => ' :attribute trường bắt buộc khi :values i.',
    'required_with_all'    => ' :attribute trường bắt buộc khi :values này.',
    'required_without'     => ' :attribute trường bắt buộc khi không phải là :values này.',
    'required_without_all' => ' :attribute trường bắt buộc khi không có :values này.',
    'same'                 => 'Bắt buộc :attribute và :other phải bằng nhau.',
    'size'                 => [
        'numeric' => 'Bắt buộc :attribute phải có :size.',
        'file'    => 'Bắt buộc :attribute phải có :size kilobytes.',
        'string'  => 'Bắt buộc :attribute phải có :size characters.',
        'array'   => 'Bắt buộc :attribute phải chứa :size items.',
    ],
    'string'               => 'Bắt buộc :attribute phải là chuỗi.',
    'timezone'             => 'Một :attribute must be a valid zone.',
    'unique'               => ' :attribute đã tồn tại.',
    'uploaded'             => ' :attribute không tải lên được.',
    'url'                  => ' :attribute định dạng không hợp lệ.',

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
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
